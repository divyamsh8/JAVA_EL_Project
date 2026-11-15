package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "term_links")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TermLink {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id")
    private Long tenantId;

    @Column(nullable = false, length = 150)
    private String title;

    @Column(name = "route_slug", nullable = false, length = 50)
    private String routeSlug;

    @Column(name = "term_code", nullable = false, length = 50)
    private String termCode;

    @Column(name = "margin_top")
    private Integer marginTop;

    @Column(name = "display_order")
    private Integer displayOrder;

    @Column(name = "is_active")
    private Boolean isActive;

    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;
}
