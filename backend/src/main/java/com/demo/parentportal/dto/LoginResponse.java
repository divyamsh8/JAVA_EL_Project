package com.demo.parentportal.dto;

import lombok.*;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class LoginResponse {
    private boolean success;
    private String token;
    private String message;
    private Integer attemptsRemaining;
    private UserSummary user;
    private List<StudentSummary> siblings;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class UserSummary {
        private Long id;
        private Long tenantId;
        private String username;
        private String role;
        private String displayName;
        private String email;
        private Long studentId;
        private String usn;
        private Long departmentId;
        private String departmentName;
    }

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class StudentSummary {
        private Long studentId;
        private String usn;
        private String fullName;
        private String departmentCode;
        private String courseName;
        private Integer currentSem;
        private String photoUrl;
    }
}
