package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;

@Entity
@Table(name = "tenant_modules")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TenantModule {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id", nullable = false)
    private Long tenantId;

    @ManyToOne(fetch = FetchType.EAGER)
    @JoinColumn(name = "module_id", nullable = false)
    private Module module;

    @Column(name = "is_enabled")
    private Boolean isEnabled;

    @Column(name = "custom_label", length = 100)
    private String customLabel;

    @Column(name = "display_order", nullable = false)
    private Integer displayOrder;

    @Column(name = "allowed_roles", length = 100)
    private String allowedRoles;
}
