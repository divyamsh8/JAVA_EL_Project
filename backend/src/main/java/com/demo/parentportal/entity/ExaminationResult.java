package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;
import java.math.BigDecimal;
import java.time.LocalDate;

@Entity
@Table(name = "examination_results")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class ExaminationResult {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id")
    private Long tenantId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "student_id", nullable = false)
    private Student student;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "subject_id", nullable = false)
    private Subject subject;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "term_id", nullable = false)
    private Term term;

    @Column(nullable = false)
    private Integer semester;

    @Column(nullable = false, precision = 3, scale = 1)
    private BigDecimal credits;

    @Column(name = "grade_letter", nullable = false, length = 5)
    private String gradeLetter;

    @Column(name = "grade_points", nullable = false, precision = 4, scale = 2)
    private BigDecimal gradePoints;

    @Column(name = "result_status", nullable = false, length = 20)
    private String resultStatus;

    @Column(nullable = false, precision = 4, scale = 2)
    private BigDecimal sgpa;

    @Column(nullable = false, precision = 4, scale = 2)
    private BigDecimal cgpa;

    @Column(name = "exam_session", nullable = false, length = 50)
    private String examSession;

    @Column(name = "declared_date", nullable = false)
    private LocalDate declaredDate;
}
