package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;
import java.math.BigDecimal;

@Entity
@Table(name = "rules_config")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class RulesConfig {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id", nullable = false)
    private Long tenantId;

    @Column(name = "shortage_threshold", precision = 5, scale = 2)
    private BigDecimal shortageThreshold; // default 75.00

    @Column(name = "cie_max_marks", precision = 5, scale = 2)
    private BigDecimal cieMaxMarks; // default 50.00

    @Column(name = "cie_min_eligibility", precision = 5, scale = 2)
    private BigDecimal cieMinEligibility; // default 20.00

    @Column(name = "grading_scale_json", columnDefinition = "TEXT")
    private String gradingScaleJson;

    @Column(name = "fee_due_grace_days")
    private Integer feeDueGraceDays;
}
