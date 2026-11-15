package com.demo.parentportal.dto;

import jakarta.validation.constraints.NotBlank;
import lombok.*;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class LoginRequest {
    @NotBlank(message = "Username or USN is required")
    private String username;

    // For parent/student login using DOB dropdowns
    private String day;
    private String month;
    private String year;

    // For direct password (e.g. admin or fallback)
    private String password;

    // Academic term context if logging in from term route
    private String termCode;
}
