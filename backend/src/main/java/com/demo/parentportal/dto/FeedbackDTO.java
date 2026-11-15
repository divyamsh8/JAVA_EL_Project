package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalDateTime;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class FeedbackDTO {
    private Long id;
    private Long studentId;
    private String studentName;
    private String usn;
    private String category;
    private String subject;
    private String message;
    private String status; // 'OPEN', 'RESOLVED'
    private String response;
    private LocalDateTime createdAt;
}
