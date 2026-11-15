package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class ResultsDTO {
    private Integer semester;
    private String examSession;
    private LocalDate declaredDate;
    private BigDecimal sgpa;
    private BigDecimal cgpa;
    private String overallStatus;
    private List<SubjectResult> subjects;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class SubjectResult {
        private Long subjectId;
        private String subjectCode;
        private String subjectTitle;
        private BigDecimal credits;
        private String gradeLetter;
        private Integer gradePoints;
        private String resultStatus;
    }
}
