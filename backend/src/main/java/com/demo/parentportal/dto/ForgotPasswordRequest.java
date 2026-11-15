package com.demo.parentportal.dto;

import lombok.*;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class ForgotPasswordRequest {
    private Integer step; // 1: verify USN+DOB, 2: verify OTP, 3: reset password
    private String usn;
    private String day;
    private String month;
    private String year;
    private String otp;
    private String newPassword;
    private String confirmPassword;
}
