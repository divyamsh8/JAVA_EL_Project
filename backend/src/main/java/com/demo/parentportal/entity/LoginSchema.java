package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;

@Entity
@Table(name = "login_schemas")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class LoginSchema {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id", nullable = false)
    private Long tenantId;

    @Column(name = "identifier_field", length = 50)
    private String identifierField; // 'USN', 'ROLL_NO', 'EMAIL', 'PHONE'

    @Column(name = "credential_type", length = 50)
    private String credentialType; // 'DOB_DROPDOWN', 'PASSWORD', 'OTP'

    @Column(name = "allowed_terms_json", columnDefinition = "TEXT")
    private String allowedTermsJson;

    @Column(name = "rate_limit_max")
    private Integer rateLimitMax;

    @Column(name = "lockout_minutes")
    private Integer lockoutMinutes;
}
