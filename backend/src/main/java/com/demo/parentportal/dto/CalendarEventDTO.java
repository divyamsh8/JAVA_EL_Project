package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalDate;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class CalendarEventDTO {
    private Long id;
    private String title;
    private String eventType; // 'EXAM', 'HOLIDAY', 'ACADEMIC', 'FEE_DUE'
    private LocalDate startDate;
    private LocalDate endDate;
    private String description;
}
