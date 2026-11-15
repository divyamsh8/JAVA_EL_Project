package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "tenant_settings")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TenantSettings {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id", nullable = false)
    private Long tenantId;

    @Column(name = "theme_json", nullable = false, columnDefinition = "TEXT")
    private String themeJson;

    @Column(name = "welcome_text", nullable = false, columnDefinition = "TEXT")
    private String welcomeText;

    @Column(name = "notice_text", nullable = false, columnDefinition = "TEXT")
    private String noticeText;

    @Column(name = "footer_text", nullable = false, columnDefinition = "TEXT")
    private String footerText;

    @Column(name = "contact_email", length = 150)
    private String contactEmail;

    @Column(name = "contact_phone", length = 50)
    private String contactPhone;

    @Column(name = "default_language", length = 10)
    private String defaultLanguage;

    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "updated_at", insertable = false, updatable = false)
    private LocalDateTime updatedAt;
}
