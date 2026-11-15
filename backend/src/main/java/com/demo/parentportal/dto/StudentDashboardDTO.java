package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class StudentDashboardDTO {
    private StudentProfileDTO profile;
    private BigDecimal attendancePercentage;
    private boolean shortageAlert;
    private BigDecimal sgpa;
    private BigDecimal cgpa;
    private BigDecimal feeBalance;
    private List<AnnouncementDTO> recentAnnouncements;
    private TimetableDTO todayTimetable;
}
