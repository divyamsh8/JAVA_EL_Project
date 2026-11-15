package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class AttendanceDTO {
    private Integer overallTotalClasses;
    private Integer overallAttendedClasses;
    private BigDecimal overallPercentage;
    private boolean shortageAlert; // true if overall or any subject < 75%
    private List<SubjectAttendance> subjects;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class SubjectAttendance {
        private Long subjectId;
        private String subjectCode;
        private String subjectTitle;
        private BigDecimal credits;
        private Integer totalClasses;
        private Integer attendedClasses;
        private BigDecimal percentage;
        private boolean isShortage;
        private String remarks;
    }
}
