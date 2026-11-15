package com.demo.parentportal.service;

import com.demo.parentportal.dto.*;
import com.demo.parentportal.entity.*;
import com.demo.parentportal.repository.*;
import com.demo.parentportal.repository.FeeRecordRepository;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.math.RoundingMode;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.*;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
@Slf4j
@Transactional(readOnly = true)
public class ParentPortalService {

    private final StudentRepository studentRepository;
    private final AttendanceRepository attendanceRepository;
    private final InternalMarkRepository internalMarkRepository;
    private final ExaminationResultRepository examinationResultRepository;
    private final TimetableRepository timetableRepository;
    private final AnnouncementRepository announcementRepository;
    private final FeeRecordRepository feeRecordRepository;
    private final TermRepository termRepository;
    private final LeaveRequestRepository leaveRequestRepository;
    private final FeedbackTicketRepository feedbackTicketRepository;
    private final MentorAppointmentRepository mentorAppointmentRepository;
    private final PaymentRepository paymentRepository;

    public StudentProfileDTO getProfile(Long studentId) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found with id: " + studentId));
        return mapToProfileDTO(student);
    }

    public AttendanceDTO getAttendance(Long studentId, Long termId) {
        List<Attendance> records = (termId != null)
                ? attendanceRepository.findByStudentIdAndTermId(studentId, termId)
                : attendanceRepository.findByStudentId(studentId);

        int totalConducted = 0;
        int totalAttended = 0;
        boolean overallShortage = false;

        Map<Long, List<Attendance>> bySubject = records.stream()
                .collect(Collectors.groupingBy(a -> a.getSubject().getId()));

        List<AttendanceDTO.SubjectAttendance> subjectList = new ArrayList<>();

        for (Map.Entry<Long, List<Attendance>> entry : bySubject.entrySet()) {
            List<Attendance> subRecords = entry.getValue();
            Attendance sample = subRecords.get(0);
            Subject subject = sample.getSubject();

            int subTotal = subRecords.stream().mapToInt(Attendance::getTotalClasses).sum();
            int subAttended = subRecords.stream().mapToInt(Attendance::getAttendedClasses).sum();

            totalConducted += subTotal;
            totalAttended += subAttended;

            BigDecimal pct = (subTotal > 0)
                    ? BigDecimal.valueOf((double) subAttended / subTotal * 100.0).setScale(2, RoundingMode.HALF_UP)
                    : BigDecimal.ZERO;

            boolean isShort = pct.compareTo(BigDecimal.valueOf(75.0)) < 0;
            if (isShort) {
                overallShortage = true;
            }

            subjectList.add(AttendanceDTO.SubjectAttendance.builder()
                    .subjectId(subject.getId())
                    .subjectCode(subject.getCode())
                    .subjectTitle(subject.getTitle())
                    .credits(subject.getCredits())
                    .totalClasses(subTotal)
                    .attendedClasses(subAttended)
                    .percentage(pct)
                    .isShortage(isShort)
                    .remarks(sample.getRemarks())
                    .build());
        }

        BigDecimal overallPct = (totalConducted > 0)
                ? BigDecimal.valueOf((double) totalAttended / totalConducted * 100.0).setScale(2, RoundingMode.HALF_UP)
                : BigDecimal.ZERO;

        if (overallPct.compareTo(BigDecimal.valueOf(75.0)) < 0) {
            overallShortage = true;
        }

        return AttendanceDTO.builder()
                .overallTotalClasses(totalConducted)
                .overallAttendedClasses(totalAttended)
                .overallPercentage(overallPct)
                .shortageAlert(overallShortage)
                .subjects(subjectList)
                .build();
    }

    public CieMarksDTO getCieMarks(Long studentId, Long termId) {
        List<InternalMark> records = (termId != null)
                ? internalMarkRepository.findByStudentIdAndTermId(studentId, termId)
                : internalMarkRepository.findByStudentId(studentId);

        Map<Long, List<InternalMark>> bySubject = records.stream()
                .collect(Collectors.groupingBy(m -> m.getSubject().getId()));

        List<CieMarksDTO.SubjectCie> subjects = new ArrayList<>();

        for (Map.Entry<Long, List<InternalMark>> entry : bySubject.entrySet()) {
            List<InternalMark> list = entry.getValue();
            Subject subject = list.get(0).getSubject();

            BigDecimal cie1 = BigDecimal.ZERO;
            BigDecimal cie2 = BigDecimal.ZERO;
            BigDecimal assign1 = BigDecimal.ZERO;
            BigDecimal assign2 = BigDecimal.ZERO;

            for (InternalMark m : list) {
                String testName = m.getTestName().toUpperCase();
                if (testName.contains("CIE-1") || testName.contains("CIE 1") || testName.contains("TEST-1")) {
                    cie1 = m.getMarksObtained();
                } else if (testName.contains("CIE-2") || testName.contains("CIE 2") || testName.contains("TEST-2")) {
                    cie2 = m.getMarksObtained();
                } else if (testName.contains("ASSIGNMENT-1") || testName.contains("ASSIGNMENT 1") || testName.contains("A1")) {
                    assign1 = m.getMarksObtained();
                } else if (testName.contains("ASSIGNMENT-2") || testName.contains("ASSIGNMENT 2") || testName.contains("A2")) {
                    assign2 = m.getMarksObtained();
                }
            }

            // Formula: CIE Total = (CIE1 + CIE2) / 2 + Assign1 + Assign2 (Capped at 50)
            BigDecimal cieAvg = cie1.add(cie2).divide(BigDecimal.valueOf(2), 2, RoundingMode.HALF_UP);
            BigDecimal total = cieAvg.add(assign1).add(assign2);
            if (total.compareTo(BigDecimal.valueOf(50.0)) > 0) {
                total = BigDecimal.valueOf(50.0);
            }

            String status = (total.compareTo(BigDecimal.valueOf(20.0)) >= 0) ? "Eligible" : "Ineligible (< 20)";

            subjects.add(CieMarksDTO.SubjectCie.builder()
                    .subjectId(subject.getId())
                    .subjectCode(subject.getCode())
                    .subjectTitle(subject.getTitle())
                    .credits(subject.getCredits())
                    .cie1(cie1)
                    .cie2(cie2)
                    .assignment1(assign1)
                    .assignment2(assign2)
                    .cieTotal(total)
                    .maxMarks(BigDecimal.valueOf(50.0))
                    .status(status)
                    .build());
        }

        return CieMarksDTO.builder().subjects(subjects).build();
    }

    public ResultsDTO getResults(Long studentId, Long termId) {
        List<ExaminationResult> results = (termId != null)
                ? examinationResultRepository.findByStudentIdAndTermId(studentId, termId)
                : examinationResultRepository.findByStudentId(studentId);

        if (results.isEmpty()) {
            return ResultsDTO.builder()
                    .semester(4)
                    .sgpa(BigDecimal.ZERO)
                    .cgpa(BigDecimal.ZERO)
                    .overallStatus("No results published")
                    .subjects(Collections.emptyList())
                    .build();
        }

        ExaminationResult first = results.get(0);
        List<ResultsDTO.SubjectResult> subjects = results.stream()
                .map(r -> ResultsDTO.SubjectResult.builder()
                        .subjectId(r.getSubject().getId())
                        .subjectCode(r.getSubject().getCode())
                        .subjectTitle(r.getSubject().getTitle())
                        .credits(r.getCredits())
                        .gradeLetter(r.getGradeLetter())
                        .gradePoints(r.getGradePoints() != null ? r.getGradePoints().intValue() : 0)
                        .resultStatus(r.getResultStatus())
                        .build())
                .collect(Collectors.toList());

        boolean allPass = results.stream().allMatch(r -> "PASS".equalsIgnoreCase(r.getResultStatus()));

        return ResultsDTO.builder()
                .semester(first.getSemester())
                .examSession(first.getExamSession())
                .declaredDate(first.getDeclaredDate())
                .sgpa(first.getSgpa())
                .cgpa(first.getCgpa())
                .overallStatus(allPass ? "PASS" : "FAIL")
                .subjects(subjects)
                .build();
    }

    public TimetableDTO getTimetable(Long studentId) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));

        List<Timetable> entries = timetableRepository.findByDepartmentIdAndSemesterOrderByDayOfWeekAscPeriodNumberAsc(
                student.getDepartment().getId(),
                student.getCurrentSem() != null ? student.getCurrentSem() : 4
        );

        Map<String, List<TimetableDTO.PeriodSlot>> schedule = new LinkedHashMap<>();
        for (String day : List.of("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday")) {
            schedule.put(day, new ArrayList<>());
        }

        for (Timetable entry : entries) {
            String day = entry.getDayOfWeek();
            schedule.computeIfAbsent(day, k -> new ArrayList<>()).add(
                    TimetableDTO.PeriodSlot.builder()
                            .periodNumber(entry.getPeriodNumber())
                            .startTime(entry.getStartTime())
                            .endTime(entry.getEndTime())
                            .subjectCode(entry.getSubject().getCode())
                            .subjectTitle(entry.getSubject().getTitle())
                            .roomNo(entry.getRoomNo())
                            .facultyName(entry.getFacultyName())
                            .build()
            );
        }

        return TimetableDTO.builder()
                .departmentName(student.getDepartment().getName())
                .semester(student.getCurrentSem())
                .schedule(schedule)
                .build();
    }

    public List<AnnouncementDTO> getAnnouncements(Long termId) {
        List<Announcement> list = (termId != null)
                ? announcementRepository.findByTermIdAndIsPublishedTrueOrderByPublishedDateDesc(termId)
                : announcementRepository.findByIsPublishedTrueOrderByPublishedDateDesc();

        return list.stream().map(a -> AnnouncementDTO.builder()
                .id(a.getId())
                .termId(a.getTerm() != null ? a.getTerm().getId() : null)
                .termName(a.getTerm() != null ? a.getTerm().getName() : "All Terms")
                .title(a.getTitle())
                .category(a.getCategory())
                .description(a.getDescription())
                .attachmentFile(a.getAttachmentFile())
                .publishedDate(a.getPublishedDate())
                .build()).collect(Collectors.toList());
    }

    public FeeDTO getFeeRecords(Long studentId, Long termId) {
        List<FeeRecord> records = (termId != null)
                ? feeRecordRepository.findByStudentIdAndTermId(studentId, termId)
                : feeRecordRepository.findByStudentId(studentId);

        BigDecimal totalPayable = BigDecimal.ZERO;
        BigDecimal totalPaid = BigDecimal.ZERO;
        BigDecimal totalBalance = BigDecimal.ZERO;

        List<FeeDTO.FeeItem> items = new ArrayList<>();
        for (FeeRecord f : records) {
            totalPayable = totalPayable.add(f.getTotalAmount());
            totalPaid = totalPaid.add(f.getPaidAmount());
            totalBalance = totalBalance.add(f.getBalanceAmount());

            items.add(FeeDTO.FeeItem.builder()
                    .id(f.getId())
                    .academicYear(f.getAcademicYear())
                    .feeCategory(f.getFeeCategory())
                    .totalAmount(f.getTotalAmount())
                    .paidAmount(f.getPaidAmount())
                    .balanceAmount(f.getBalanceAmount())
                    .paymentStatus(f.getPaymentStatus())
                    .receiptNo(f.getReceiptNo())
                    .paymentDate(f.getPaymentDate())
                    .paymentMode(f.getPaymentMode())
                    .build());
        }

        String overallStatus = (totalBalance.compareTo(BigDecimal.ZERO) == 0) ? "PAID"
                : (totalPaid.compareTo(BigDecimal.ZERO) > 0 ? "PARTIAL" : "PENDING");

        return FeeDTO.builder()
                .totalPayable(totalPayable)
                .totalPaid(totalPaid)
                .totalBalance(totalBalance)
                .overallStatus(overallStatus)
                .records(items)
                .build();
    }

    public StudentDashboardDTO getDashboard(Long studentId, Long termId) {
        StudentProfileDTO profile = getProfile(studentId);
        AttendanceDTO attendance = getAttendance(studentId, termId);
        ResultsDTO results = getResults(studentId, termId);
        FeeDTO fee = getFeeRecords(studentId, termId);
        List<AnnouncementDTO> announcements = getAnnouncements(termId);
        TimetableDTO timetable = getTimetable(studentId);

        return StudentDashboardDTO.builder()
                .profile(profile)
                .attendancePercentage(attendance.getOverallPercentage())
                .shortageAlert(attendance.isShortageAlert())
                .sgpa(results.getSgpa())
                .cgpa(results.getCgpa())
                .feeBalance(fee.getTotalBalance())
                .recentAnnouncements(announcements.stream().limit(5).collect(Collectors.toList()))
                .todayTimetable(timetable)
                .build();
    }

    private StudentProfileDTO mapToProfileDTO(Student s) {
        Parent p = s.getParent();
        return StudentProfileDTO.builder()
                .id(s.getId())
                .usn(s.getUsn())
                .rollNo(s.getRollNo())
                .firstName(s.getFirstName())
                .lastName(s.getLastName())
                .fullName(s.getFirstName() + " " + s.getLastName())
                .gender(s.getGender())
                .dob(s.getDob())
                .bloodGroup(s.getBloodGroup())
                .admissionYear(s.getAdmissionYear())
                .currentSem(s.getCurrentSem())
                .photoUrl(s.getPhotoUrl())
                .departmentId(s.getDepartment().getId())
                .departmentCode(s.getDepartment().getCode())
                .departmentName(s.getDepartment().getName())
                .courseName(s.getCourse().getName())
                .degreeLevel(s.getCourse().getDegreeLevel())
                .fatherName(p.getFatherName())
                .motherName(p.getMotherName())
                .primaryMobile(p.getPrimaryMobile())
                .alternateMobile(p.getAlternateMobile())
                .parentEmail(p.getEmail())
                .address(p.getAddress())
                .occupation(p.getOccupation())
                .build();
    }

    public List<LoginResponse.StudentSummary> getSiblings(Long studentId) {
        Student s = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        if (s.getParent() == null) return Collections.emptyList();
        return studentRepository.findByParentId(s.getParent().getId()).stream()
                .map(ward -> LoginResponse.StudentSummary.builder()
                        .studentId(ward.getId())
                        .usn(ward.getUsn())
                        .fullName(ward.getFirstName() + " " + ward.getLastName())
                        .departmentCode(ward.getDepartment() != null ? ward.getDepartment().getCode() : "")
                        .courseName(ward.getCourse() != null ? ward.getCourse().getName() : "")
                        .currentSem(ward.getCurrentSem())
                        .photoUrl(ward.getPhotoUrl())
                        .build())
                .collect(Collectors.toList());
    }

    public List<LeaveRequestDTO> getLeaveRequests(Long studentId) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        return leaveRequestRepository.findByTenantIdAndStudentIdOrderBySubmittedAtDesc(student.getTenantId(), studentId)
                .stream().map(l -> LeaveRequestDTO.builder()
                        .id(l.getId())
                        .studentId(studentId)
                        .studentName(student.getFirstName() + " " + student.getLastName())
                        .usn(student.getUsn())
                        .startDate(l.getStartDate())
                        .endDate(l.getEndDate())
                        .reason(l.getReason())
                        .status(l.getStatus())
                        .submittedAt(l.getSubmittedAt())
                        .approvedBy(l.getApprovedBy())
                        .remarks(l.getRemarks())
                        .build()
                ).collect(Collectors.toList());
    }

    @Transactional
    public LeaveRequest submitLeaveRequest(Long studentId, LeaveRequestDTO dto) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        LeaveRequest req = LeaveRequest.builder()
                .tenantId(student.getTenantId())
                .student(student)
                .parent(student.getParent())
                .startDate(dto.getStartDate())
                .endDate(dto.getEndDate())
                .reason(dto.getReason())
                .status("PENDING")
                .submittedAt(LocalDateTime.now())
                .build();
        return leaveRequestRepository.save(req);
    }

    public List<FeedbackDTO> getFeedbackTickets(Long studentId) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        return feedbackTicketRepository.findByTenantIdAndStudentIdOrderByCreatedAtDesc(student.getTenantId(), studentId)
                .stream().map(f -> FeedbackDTO.builder()
                        .id(f.getId())
                        .studentId(studentId)
                        .studentName(student.getFirstName() + " " + student.getLastName())
                        .usn(student.getUsn())
                        .category(f.getCategory())
                        .subject(f.getSubject())
                        .message(f.getMessage())
                        .status(f.getStatus())
                        .response(f.getResponse())
                        .createdAt(f.getCreatedAt())
                        .build()
                ).collect(Collectors.toList());
    }

    @Transactional
    public FeedbackTicket submitFeedbackTicket(Long studentId, FeedbackDTO dto) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        FeedbackTicket ticket = FeedbackTicket.builder()
                .tenantId(student.getTenantId())
                .student(student)
                .parent(student.getParent())
                .category(dto.getCategory() != null ? dto.getCategory() : "ACADEMIC")
                .subject(dto.getSubject())
                .message(dto.getMessage())
                .status("OPEN")
                .createdAt(LocalDateTime.now())
                .build();
        return feedbackTicketRepository.save(ticket);
    }

    public List<MentorAppointment> getMentorAppointments(Long studentId) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        return mentorAppointmentRepository.findByTenantIdAndStudentIdOrderByAppointmentTimeAsc(student.getTenantId(), studentId);
    }

    @Transactional
    public MentorAppointment bookMentorAppointment(Long studentId, String facultyName, LocalDateTime time, String mode, String topic, String notes) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        MentorAppointment appt = MentorAppointment.builder()
                .tenantId(student.getTenantId())
                .student(student)
                .facultyName(facultyName != null ? facultyName : "Dr. Ramesh Babu (CSE Faculty Mentor)")
                .appointmentTime(time != null ? time : LocalDateTime.now().plusDays(2).withHour(15).withMinute(0))
                .mode(mode != null ? mode : "ONLINE")
                .status("CONFIRMED")
                .topic(topic != null ? topic : "Semester Progress Review")
                .notes(notes)
                .createdAt(LocalDateTime.now())
                .build();
        return mentorAppointmentRepository.save(appt);
    }

    @Transactional
    public Payment processSandboxPayment(Long studentId, Long feeId, BigDecimal amount, String mode) {
        Student student = studentRepository.findById(studentId)
                .orElseThrow(() -> new IllegalArgumentException("Student not found"));
        FeeRecord fee = feeRecordRepository.findById(feeId)
                .orElseThrow(() -> new IllegalArgumentException("Fee record not found"));

        BigDecimal newPaid = fee.getPaidAmount().add(amount);
        fee.setPaidAmount(newPaid);
        BigDecimal newBal = fee.getTotalAmount().subtract(newPaid);
        fee.setBalanceAmount(newBal.max(BigDecimal.ZERO));
        if (newBal.compareTo(BigDecimal.ZERO) <= 0) {
            fee.setPaymentStatus("PAID");
        } else {
            fee.setPaymentStatus("PARTIAL");
        }
        feeRecordRepository.save(fee);

        String ref = "TXN-" + System.currentTimeMillis();
        String receipt = "RCP-" + (10000 + new Random().nextInt(90000));

        Payment payment = Payment.builder()
                .tenantId(student.getTenantId())
                .student(student)
                .feeRecord(fee)
                .transactionRef(ref)
                .amount(amount)
                .paymentMethod(mode != null ? mode : "SANDBOX_GATEWAY")
                .status("SUCCESS")
                .receiptNo(receipt)
                .createdAt(LocalDateTime.now())
                .build();

        return paymentRepository.save(payment);
    }
}
