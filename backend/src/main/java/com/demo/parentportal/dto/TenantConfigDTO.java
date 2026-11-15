package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.util.List;
import java.util.Map;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TenantConfigDTO {
    private Long id;
    private String slug;
    private String name;
    private String code;
    private Map<String, Object> theme;
    private String welcomeText;
    private String noticeText;
    private String footerText;
    private String contactEmail;
    private String contactPhone;
    private String defaultLanguage;

    private LoginSchemaConfig loginSchema;
    private List<ModuleConfig> modules;
    private List<TermDTO> terms;
    private List<TermLinkDTO> termLinks;
    private RulesConfigDTO rules;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class LoginSchemaConfig {
        private String identifierField; // 'USN', 'ROLL_NO', 'EMAIL', 'PHONE'
        private String credentialType;  // 'DOB_DROPDOWN', 'PASSWORD', 'OTP'
        private List<String> allowedTerms;
        private Integer rateLimitMax;
        private Integer lockoutMinutes;
    }

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class ModuleConfig {
        private String key;
        private String label;
        private String icon;
        private String route;
        private Integer order;
        private Boolean enabled;
    }

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class RulesConfigDTO {
        private BigDecimal shortageThreshold;
        private BigDecimal cieMaxMarks;
        private BigDecimal cieMinEligibility;
        private Integer feeDueGraceDays;
    }
}
