package com.demo.parentportal.service;

import com.demo.parentportal.dto.AttendanceDTO;
import com.demo.parentportal.dto.CieMarksDTO;
import com.demo.parentportal.entity.Attendance;
import com.demo.parentportal.entity.InternalMark;
import com.demo.parentportal.entity.Subject;
import com.demo.parentportal.repository.*;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.math.BigDecimal;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.Mockito.when;

@ExtendWith(MockitoExtension.class)
class ParentPortalServiceTest {

    @Mock
    private AttendanceRepository attendanceRepository;

    @Mock
    private InternalMarkRepository internalMarkRepository;

    @Mock
    private StudentRepository studentRepository;

    @Mock
    private ExaminationResultRepository examinationResultRepository;

    @Mock
    private TimetableRepository timetableRepository;

    @Mock
    private AnnouncementRepository announcementRepository;

    @Mock
    private FeeRecordRepository feeRecordRepository;

    @Mock
    private TermRepository termRepository;

    @InjectMocks
    private ParentPortalService parentPortalService;

    @Test
    void testAttendance_percentageAndShortageCalculation() {
        Subject sub1 = Subject.builder().id(1L).code("21CS41").title("Algorithms").credits(BigDecimal.valueOf(4.0)).build();
        Subject sub2 = Subject.builder().id(2L).code("21CS42").title("OS").credits(BigDecimal.valueOf(4.0)).build();

        // sub1: 30 / 40 = 75.0% (No shortage)
        Attendance att1 = Attendance.builder()
                .subject(sub1)
                .totalClasses(40)
                .attendedClasses(30)
                .percentage(BigDecimal.valueOf(75.00))
                .build();

        // sub2: 28 / 40 = 70.0% (<75%, SHORTAGE ALERT)
        Attendance att2 = Attendance.builder()
                .subject(sub2)
                .totalClasses(40)
                .attendedClasses(28)
                .percentage(BigDecimal.valueOf(70.00))
                .build();

        when(attendanceRepository.findByStudentId(1L)).thenReturn(List.of(att1, att2));

        AttendanceDTO result = parentPortalService.getAttendance(1L, null);

        assertEquals(80, result.getOverallTotalClasses());
        assertEquals(58, result.getOverallAttendedClasses());
        // 58 / 80 = 72.50%
        assertEquals(new BigDecimal("72.50"), result.getOverallPercentage());
        assertTrue(result.isShortageAlert()); // Alert triggered because < 75%
    }

    @Test
    void testCieScalingFormula_calculatesCorrectScaledTotal() {
        Subject sub = Subject.builder().id(1L).code("21CS41").title("Algorithms").credits(BigDecimal.valueOf(4.0)).build();

        // CIE 1 = 40, CIE 2 = 44, Assign 1 = 9, Assign 2 = 8
        // Average CIE = (40 + 44) / 2 = 42
        // Total = 42 + 9 + 8 = 59 -> Capped at 50.0!
        InternalMark m1 = InternalMark.builder().subject(sub).testName("CIE-1").marksObtained(BigDecimal.valueOf(40.0)).build();
        InternalMark m2 = InternalMark.builder().subject(sub).testName("CIE-2").marksObtained(BigDecimal.valueOf(44.0)).build();
        InternalMark m3 = InternalMark.builder().subject(sub).testName("Assignment-1").marksObtained(BigDecimal.valueOf(9.0)).build();
        InternalMark m4 = InternalMark.builder().subject(sub).testName("Assignment-2").marksObtained(BigDecimal.valueOf(8.0)).build();

        when(internalMarkRepository.findByStudentId(1L)).thenReturn(List.of(m1, m2, m3, m4));

        CieMarksDTO cie = parentPortalService.getCieMarks(1L, null);

        assertEquals(1, cie.getSubjects().size());
        CieMarksDTO.SubjectCie s = cie.getSubjects().get(0);
        assertEquals(new BigDecimal("50.0"), s.getCieTotal()); // Capped at 50
        assertEquals("Eligible", s.getStatus());
    }
}
