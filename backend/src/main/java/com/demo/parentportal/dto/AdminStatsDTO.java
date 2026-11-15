package com.demo.parentportal.dto;

import com.demo.parentportal.entity.LoginLog;
import lombok.*;
import java.math.BigDecimal;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class AdminStatsDTO {
    private long totalStudents;
    private long totalDepartments;
    private long totalCourses;
    private long shortageCount;
    private BigDecimal totalFeesPending;
    private List<StudentProfileDTO> shortageStudents;
    private List<LoginLog> recentLogs;
}
