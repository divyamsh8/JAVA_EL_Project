package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalDate;
import java.time.LocalDateTime;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class LeaveRequestDTO {
    private Long id;
    private Long studentId;
    private String studentName;
    private String usn;
    private LocalDate startDate;
    private LocalDate endDate;
    private String reason;
    private String status; // 'PENDING', 'APPROVED', 'REJECTED'
    private LocalDateTime submittedAt;
    private String approvedBy;
    private String remarks;
}
