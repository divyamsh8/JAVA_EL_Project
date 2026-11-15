package com.demo.parentportal.controller;

import com.demo.parentportal.dto.*;
import com.demo.parentportal.entity.Student;
import com.demo.parentportal.entity.User;
import com.demo.parentportal.repository.StudentRepository;
import com.demo.parentportal.repository.UserRepository;
import com.demo.parentportal.service.AuthService;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@RequestMapping("/api/v1/auth")
@RequiredArgsConstructor
public class AuthController {

    private final AuthService authService;
    private final UserRepository userRepository;
    private final StudentRepository studentRepository;

    @PostMapping("/login")
    public ResponseEntity<LoginResponse> login(@Valid @RequestBody LoginRequest request, HttpServletRequest httpRequest) {
        String ipAddress = getClientIp(httpRequest);
        String userAgent = httpRequest.getHeader("User-Agent");

        LoginResponse response = authService.authenticate(request, ipAddress, userAgent);
        if (!response.isSuccess()) {
            if (response.getAttemptsRemaining() != null && response.getAttemptsRemaining() == 0) {
                return ResponseEntity.status(HttpStatus.TOO_MANY_REQUESTS).body(response);
            }
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(response);
        }
        return ResponseEntity.ok(response);
    }

    @PostMapping("/forgot-password")
    public ResponseEntity<ApiResponse<String>> forgotPassword(@RequestBody ForgotPasswordRequest request) {
        try {
            String message = authService.handleForgotPassword(request);
            return ResponseEntity.ok(ApiResponse.ok(message, message));
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(ApiResponse.error(e.getMessage()));
        }
    }

    @PostMapping("/logout")
    public ResponseEntity<ApiResponse<String>> logout() {
        return ResponseEntity.ok(ApiResponse.ok("Logged out successfully", "Logged out"));
    }

    @GetMapping("/me")
    public ResponseEntity<ApiResponse<LoginResponse.UserSummary>> getCurrentUser(Authentication authentication) {
        if (authentication == null || !authentication.isAuthenticated()) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(ApiResponse.error("Not authenticated"));
        }

        String username = authentication.getName();
        User user = userRepository.findByUsername(username)
                .orElseThrow(() -> new IllegalArgumentException("User not found"));

        List<Student> students = studentRepository.findByUserId(user.getId());
        Student student = students.stream()
                .filter(s -> s.getUsn().equalsIgnoreCase(username))
                .findFirst()
                .orElse(students.isEmpty() ? null : students.get(0));

        LoginResponse.UserSummary summary = LoginResponse.UserSummary.builder()
                .id(user.getId())
                .tenantId(user.getTenantId())
                .username(user.getUsername())
                .role(user.getRole().getName())
                .displayName(student != null ? (student.getFirstName() + " " + student.getLastName()) : user.getUsername())
                .email(user.getEmail())
                .studentId(student != null ? student.getId() : null)
                .usn(student != null ? student.getUsn() : null)
                .departmentId((student != null && student.getDepartment() != null) ? student.getDepartment().getId() : null)
                .departmentName((student != null && student.getDepartment() != null) ? student.getDepartment().getName() : null)
                .build();

        return ResponseEntity.ok(ApiResponse.ok(summary));
    }

    private String getClientIp(HttpServletRequest request) {
        String xfHeader = request.getHeader("X-Forwarded-For");
        if (xfHeader == null || xfHeader.isEmpty() || "unknown".equalsIgnoreCase(xfHeader)) {
            return request.getRemoteAddr();
        }
        return xfHeader.split(",")[0].trim();
    }
}
