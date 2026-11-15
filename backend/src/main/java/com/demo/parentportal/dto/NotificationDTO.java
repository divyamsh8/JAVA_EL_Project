package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalDateTime;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class NotificationDTO {
    private Long id;
    private String title;
    private String message;
    private String type; // 'INFO', 'EXAM', 'FEE', 'ATTENDANCE'
    private Boolean isRead;
    private LocalDateTime createdAt;
}
