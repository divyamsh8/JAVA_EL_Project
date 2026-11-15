package com.demo.parentportal.controller;

import com.demo.parentportal.dto.*;
import com.demo.parentportal.entity.*;
import com.demo.parentportal.repository.StudentRepository;
import com.demo.parentportal.repository.UserRepository;
import com.demo.parentportal.service.AuthService;
import com.demo.parentportal.service.ParentPortalService;
import com.demo.parentportal.service.PdfService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;
import java.util.List;
import java.util.Set;
import java.util.stream.Collectors;

@RestController
@RequestMapping("/api/v1/parent")
@RequiredArgsConstructor
public class ParentPortalController {

    private final ParentPortalService parentPortalService;
    private final AuthService authService;
    private final UserRepository userRepository;
    private final StudentRepository studentRepository;
    private final PdfService pdfService;

    private Long resolveAuthorizedStudentId(Authentication authentication, Long requestedStudentId) {
        String username = authentication.getName();
        User user = userRepository.findByUsername(username)
                .orElseThrow(() -> new IllegalArgumentException("User not found"));

        boolean isAdmin = user.getRole().getName().contains("ADMIN");
        List<Student> ownStudents = studentRepository.findByUserId(user.getId());

        if (isAdmin) {
            if (requestedStudentId != null) return requestedStudentId;
            return !ownStudents.isEmpty() ? ownStudents.get(0).getId() : 1L;
        }

        if (ownStudents.isEmpty()) {
            throw new IllegalArgumentException("No student linked to this account");
        }

        Set<Long> allowedStudentIds = ownStudents.stream().map(Student::getId).collect(Collectors.toSet());
        // Also if parent entity exists, allow all siblings
        if (ownStudents.get(0).getParent() != null) {
            List<Student> siblings = studentRepository.findByParentId(ownStudents.get(0).getParent().getId());
            siblings.forEach(s -> allowedStudentIds.add(s.getId()));
        }

        if (requestedStudentId != null) {
            if (!allowedStudentIds.contains(requestedStudentId)) {
                throw new SecurityException("Unauthorized access to another student's records");
            }
            return requestedStudentId;
        }

        return ownStudents.get(0).getId();
    }

    @GetMapping("/dashboard")
    public ResponseEntity<ApiResponse<StudentDashboardDTO>> getDashboard(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestParam(required = false) Long termId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getDashboard(targetId, termId)));
    }

    @GetMapping("/profile")
    public ResponseEntity<ApiResponse<StudentProfileDTO>> getProfile(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getProfile(targetId)));
    }

    @GetMapping("/attendance")
    public ResponseEntity<ApiResponse<AttendanceDTO>> getAttendance(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestParam(required = false) Long termId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getAttendance(targetId, termId)));
    }

    @GetMapping("/cie")
    public ResponseEntity<ApiResponse<CieMarksDTO>> getCieMarks(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestParam(required = false) Long termId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getCieMarks(targetId, termId)));
    }

    @GetMapping("/results")
    public ResponseEntity<ApiResponse<ResultsDTO>> getResults(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestParam(required = false) Long termId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getResults(targetId, termId)));
    }

    @GetMapping("/timetable")
    public ResponseEntity<ApiResponse<TimetableDTO>> getTimetable(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getTimetable(targetId)));
    }

    @GetMapping("/announcements")
    public ResponseEntity<ApiResponse<List<AnnouncementDTO>>> getAnnouncements(
            @RequestParam(required = false) Long termId) {
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getAnnouncements(termId)));
    }

    @GetMapping("/fees")
    public ResponseEntity<ApiResponse<FeeDTO>> getFees(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestParam(required = false) Long termId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getFeeRecords(targetId, termId)));
    }

    @PostMapping("/change-password")
    public ResponseEntity<ApiResponse<String>> changePassword(
            Authentication authentication,
            @Valid @RequestBody ChangePasswordRequest request) {
        try {
            authService.changePassword(authentication.getName(), request);
            return ResponseEntity.ok(ApiResponse.ok("Password changed successfully", "Password changed successfully"));
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(ApiResponse.error(e.getMessage()));
        }
    }

    @GetMapping(value = "/reports/report-card.pdf", produces = MediaType.APPLICATION_PDF_VALUE)
    public ResponseEntity<byte[]> downloadReportCard(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        byte[] pdf = pdfService.generateReportCardPdf(targetId);
        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"report-card-" + targetId + ".pdf\"")
                .contentType(MediaType.APPLICATION_PDF)
                .body(pdf);
    }

    @GetMapping(value = "/fees/receipt/{paymentId}.pdf", produces = MediaType.APPLICATION_PDF_VALUE)
    public ResponseEntity<byte[]> downloadFeeReceipt(@PathVariable Long paymentId) {
        byte[] pdf = pdfService.generateFeeReceiptPdf(paymentId);
        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"fee-receipt-" + paymentId + ".pdf\"")
                .contentType(MediaType.APPLICATION_PDF)
                .body(pdf);
    }

    @GetMapping("/siblings")
    public ResponseEntity<ApiResponse<List<LoginResponse.StudentSummary>>> getSiblings(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getSiblings(targetId)));
    }

    @GetMapping("/leave")
    public ResponseEntity<ApiResponse<List<LeaveRequestDTO>>> getLeaveRequests(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getLeaveRequests(targetId)));
    }

    @PostMapping("/leave")
    public ResponseEntity<ApiResponse<LeaveRequest>> submitLeaveRequest(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestBody LeaveRequestDTO dto) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        LeaveRequest req = parentPortalService.submitLeaveRequest(targetId, dto);
        return ResponseEntity.ok(ApiResponse.ok("Leave request submitted successfully", req));
    }

    @GetMapping("/feedback")
    public ResponseEntity<ApiResponse<List<FeedbackDTO>>> getFeedback(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getFeedbackTickets(targetId)));
    }

    @PostMapping("/feedback")
    public ResponseEntity<ApiResponse<FeedbackTicket>> submitFeedback(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestBody FeedbackDTO dto) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        FeedbackTicket ticket = parentPortalService.submitFeedbackTicket(targetId, dto);
        return ResponseEntity.ok(ApiResponse.ok("Feedback submitted successfully", ticket));
    }

    @GetMapping("/mentors/appointments")
    public ResponseEntity<ApiResponse<List<MentorAppointment>>> getMentorAppointments(
            Authentication authentication,
            @RequestParam(required = false) Long studentId) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        return ResponseEntity.ok(ApiResponse.ok(parentPortalService.getMentorAppointments(targetId)));
    }

    @PostMapping("/mentors/appointments")
    public ResponseEntity<ApiResponse<MentorAppointment>> bookMentorAppointment(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestBody MentorAppointment req) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        MentorAppointment booked = parentPortalService.bookMentorAppointment(
                targetId, req.getFacultyName(), req.getAppointmentTime(), req.getMode(), req.getTopic(), req.getNotes()
        );
        return ResponseEntity.ok(ApiResponse.ok("Appointment booked successfully", booked));
    }

    @PostMapping("/fees/pay")
    public ResponseEntity<ApiResponse<Payment>> payFees(
            Authentication authentication,
            @RequestParam(required = false) Long studentId,
            @RequestParam Long feeId,
            @RequestParam BigDecimal amount,
            @RequestParam(defaultValue = "ONLINE_SANDBOX") String mode) {
        Long targetId = resolveAuthorizedStudentId(authentication, studentId);
        Payment payment = parentPortalService.processSandboxPayment(targetId, feeId, amount, mode);
        return ResponseEntity.ok(ApiResponse.ok("Payment processed successfully", payment));
    }

    @ExceptionHandler(SecurityException.class)
    public ResponseEntity<ApiResponse<String>> handleSecurityException(SecurityException e) {
        return ResponseEntity.status(HttpStatus.FORBIDDEN).body(ApiResponse.error(e.getMessage()));
    }
}
