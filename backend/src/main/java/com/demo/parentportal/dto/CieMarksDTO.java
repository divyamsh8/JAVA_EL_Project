package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CieMarksDTO {
    private List<SubjectCie> subjects;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class SubjectCie {
        private Long subjectId;
        private String subjectCode;
        private String subjectTitle;
        private BigDecimal credits;
        private BigDecimal cie1; // out of 50
        private BigDecimal cie2; // out of 50
        private BigDecimal assignment1; // out of 10
        private BigDecimal assignment2; // out of 10
        private BigDecimal cieTotal; // (cie1+cie2)/2 + assign1 + assign2, capped at 50
        private BigDecimal maxMarks; // 50.0
        private String status; // Eligible / Ineligible (<20)
    }
}
