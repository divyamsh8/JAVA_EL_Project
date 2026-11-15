package com.demo.parentportal.service;

import com.demo.parentportal.dto.ChangePasswordRequest;
import com.demo.parentportal.dto.ForgotPasswordRequest;
import com.demo.parentportal.dto.LoginRequest;
import com.demo.parentportal.dto.LoginResponse;
import com.demo.parentportal.entity.Role;
import com.demo.parentportal.entity.Student;
import com.demo.parentportal.entity.User;
import com.demo.parentportal.repository.RoleRepository;
import com.demo.parentportal.repository.StudentRepository;
import com.demo.parentportal.repository.UserRepository;
import com.demo.parentportal.security.JwtUtils;
import com.demo.parentportal.security.RateLimiterService;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.Month;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;
import java.util.Collections;
import java.util.List;
import java.util.Locale;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
@Slf4j
public class AuthService {

    private final UserRepository userRepository;
    private final StudentRepository studentRepository;
    private final RoleRepository roleRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtUtils jwtUtils;
    private final RateLimiterService rateLimiterService;

    public LoginResponse authenticate(LoginRequest request, String ipAddress, String userAgent) {
        String username = request.getUsername().trim();

        // 1. Rate Limiting Check
        if (rateLimiterService.isBlocked(username, ipAddress)) {
            rateLimiterService.recordBlockedAttempt(username, ipAddress, userAgent);
            return LoginResponse.builder()
                    .success(false)
                    .message("Account is temporarily locked due to too many failed attempts. Please try again after 15 minutes.")
                    .attemptsRemaining(0)
                    .build();
        }

        // 2. Fetch User
        Optional<User> userOpt = userRepository.findByUsername(username);
        if (userOpt.isEmpty()) {
            rateLimiterService.recordFailedAttempt(username, ipAddress, userAgent, "User not found");
            int remaining = rateLimiterService.getRemainingAttempts(username, ipAddress);
            return LoginResponse.builder()
                    .success(false)
                    .message("You have entered an invalid username or password.")
                    .attemptsRemaining(remaining)
                    .build();
        }

        User user = userOpt.get();

        // 3. Determine credential input: DOB dropdowns or direct password
        String candidatePassword;
        if (request.getDay() != null && request.getMonth() != null && request.getYear() != null) {
            try {
                candidatePassword = normalizeAndValidateDob(request.getDay(), request.getMonth(), request.getYear());
            } catch (IllegalArgumentException e) {
                rateLimiterService.recordFailedAttempt(username, ipAddress, userAgent, "Invalid calendar date");
                int remaining = rateLimiterService.getRemainingAttempts(username, ipAddress);
                return LoginResponse.builder()
                        .success(false)
                        .message("Invalid date format or non-existent calendar date. " + e.getMessage())
                        .attemptsRemaining(remaining)
                        .build();
            }
        } else if (request.getPassword() != null && !request.getPassword().isBlank()) {
            candidatePassword = request.getPassword().trim();
        } else {
            return LoginResponse.builder()
                    .success(false)
                    .message("Password or Date of Birth is required.")
                    .attemptsRemaining(rateLimiterService.getRemainingAttempts(username, ipAddress))
                    .build();
        }

        // 4. Verify Password against BCrypt hash
        boolean matches = passwordEncoder.matches(candidatePassword, user.getPasswordHash());

        if (!matches) {
            rateLimiterService.recordFailedAttempt(username, ipAddress, userAgent, "Password mismatch");
            int remaining = rateLimiterService.getRemainingAttempts(username, ipAddress);
            return LoginResponse.builder()
                    .success(false)
                    .message("You have entered an invalid username or password.")
                    .attemptsRemaining(remaining)
                    .build();
        }

        // 5. Successful login
        rateLimiterService.recordSuccessfulLogin(username, ipAddress, userAgent);
        user.setLastLoginAt(LocalDateTime.now());
        userRepository.save(user);

        // Find associated student info if applicable
        List<Student> students = studentRepository.findByUserId(user.getId());
        Student student = students.stream()
                .filter(s -> s.getUsn().equalsIgnoreCase(username))
                .findFirst()
                .orElse(students.isEmpty() ? null : students.get(0));

        Long studentId = student != null ? student.getId() : null;
        String usn = student != null ? student.getUsn() : null;
        String displayName = student != null ? (student.getFirstName() + " " + student.getLastName()) : user.getUsername();
        Long deptId = (student != null && student.getDepartment() != null) ? student.getDepartment().getId() : null;
        String deptName = (student != null && student.getDepartment() != null) ? student.getDepartment().getName() : null;

        // Populate siblings if parent has multiple wards
        List<LoginResponse.StudentSummary> siblings = Collections.emptyList();
        if (student != null && student.getParent() != null) {
            siblings = studentRepository.findByParentId(student.getParent().getId()).stream()
                    .map(s -> LoginResponse.StudentSummary.builder()
                            .studentId(s.getId())
                            .usn(s.getUsn())
                            .fullName(s.getFirstName() + " " + s.getLastName())
                            .departmentCode(s.getDepartment() != null ? s.getDepartment().getCode() : "")
                            .courseName(s.getCourse() != null ? s.getCourse().getName() : "")
                            .currentSem(s.getCurrentSem())
                            .photoUrl(s.getPhotoUrl())
                            .build())
                    .collect(Collectors.toList());
        }

        String roleName = user.getRole().getName();
        String token = jwtUtils.generateToken(user.getUsername(), roleName, studentId);

        return LoginResponse.builder()
                .success(true)
                .token(token)
                .message("Login successful")
                .attemptsRemaining(5)
                .user(LoginResponse.UserSummary.builder()
                        .id(user.getId())
                        .tenantId(user.getTenantId())
                        .username(user.getUsername())
                        .role(roleName)
                        .displayName(displayName)
                        .email(user.getEmail())
                        .studentId(studentId)
                        .usn(usn)
                        .departmentId(deptId)
                        .departmentName(deptName)
                        .build())
                .siblings(siblings)
                .build();
    }

    public String normalizeAndValidateDob(String dayStr, String monthStr, String yearStr) {
        int day = Integer.parseInt(dayStr.trim());
        int year = Integer.parseInt(yearStr.trim());

        int monthNum;
        String m = monthStr.trim();
        if (m.matches("\\d+")) {
            monthNum = Integer.parseInt(m);
        } else {
            // e.g. "Jan", "January", "Feb", etc.
            String sub = m.substring(0, Math.min(3, m.length())).toLowerCase(Locale.ROOT);
            monthNum = switch (sub) {
                case "jan" -> 1;
                case "feb" -> 2;
                case "mar" -> 3;
                case "apr" -> 4;
                case "may" -> 5;
                case "jun" -> 6;
                case "jul" -> 7;
                case "aug" -> 8;
                case "sep" -> 9;
                case "oct" -> 10;
                case "nov" -> 11;
                case "dec" -> 12;
                default -> throw new IllegalArgumentException("Unknown month: " + monthStr);
            };
        }

        // Real calendar validation (e.g. checks leap years, days in month)
        LocalDate date = LocalDate.of(year, monthNum, day);
        return date.format(DateTimeFormatter.ofPattern("yyyy-MM-dd"));
    }

    @Transactional
    public void changePassword(String username, ChangePasswordRequest request) {
        User user = userRepository.findByUsername(username)
                .orElseThrow(() -> new IllegalArgumentException("User not found"));

        if (!passwordEncoder.matches(request.getCurrentPassword(), user.getPasswordHash())) {
            throw new IllegalArgumentException("Current password does not match");
        }

        if (!request.getNewPassword().equals(request.getConfirmPassword())) {
            throw new IllegalArgumentException("New passwords do not match");
        }

        if (request.getNewPassword().length() < 6) {
            throw new IllegalArgumentException("Password must be at least 6 characters");
        }

        user.setPasswordHash(passwordEncoder.encode(request.getNewPassword()));
        userRepository.save(user);
    }

    public String handleForgotPassword(ForgotPasswordRequest req) {
        if (req.getStep() == null || req.getStep() == 1) {
            // Step 1: verify USN and DOB
            String usn = req.getUsn().trim();
            Student student = studentRepository.findByUsn(usn)
                    .orElseThrow(() -> new IllegalArgumentException("No student found with USN: " + usn));

            String formattedDob = normalizeAndValidateDob(req.getDay(), req.getMonth(), req.getYear());
            if (!student.getDob().toString().equals(formattedDob)) {
                throw new IllegalArgumentException("Date of birth does not match institutional records.");
            }
            // Return demo OTP
            return "123456";
        } else if (req.getStep() == 2) {
            // Step 2: verify OTP
            if (!"123456".equals(req.getOtp())) {
                throw new IllegalArgumentException("Invalid OTP entered. Please enter demo OTP: 123456");
            }
            return "OTP verified successfully";
        } else if (req.getStep() == 3) {
            // Step 3: Reset password
            if (!req.getNewPassword().equals(req.getConfirmPassword())) {
                throw new IllegalArgumentException("Passwords do not match");
            }
            User user = userRepository.findByUsername(req.getUsn().trim())
                    .orElseThrow(() -> new IllegalArgumentException("User not found"));
            user.setPasswordHash(passwordEncoder.encode(req.getNewPassword()));
            userRepository.save(user);
            return "Password has been successfully reset. Please log in.";
        }
        throw new IllegalArgumentException("Invalid step");
    }
}
