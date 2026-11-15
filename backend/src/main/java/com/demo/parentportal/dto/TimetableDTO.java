package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalTime;
import java.util.List;
import java.util.Map;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TimetableDTO {
    private String departmentName;
    private Integer semester;
    // Map day (e.g. "Monday", "Tuesday", etc.) to period slots
    private Map<String, List<PeriodSlot>> schedule;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class PeriodSlot {
        private Integer periodNumber;
        private LocalTime startTime;
        private LocalTime endTime;
        private String subjectCode;
        private String subjectTitle;
        private String roomNo;
        private String facultyName;
    }
}
