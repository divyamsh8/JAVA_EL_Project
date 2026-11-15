package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "parents")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Parent {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "tenant_id")
    private Long tenantId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "user_id")
    private User user;

    @Column(name = "father_name", nullable = false, length = 150)
    private String fatherName;

    @Column(name = "mother_name", nullable = false, length = 150)
    private String motherName;

    @Column(name = "primary_mobile", nullable = false, length = 25)
    private String primaryMobile;

    @Column(name = "alternate_mobile", length = 25)
    private String alternateMobile;

    @Column(length = 150)
    private String email;

    @Column(nullable = false, columnDefinition = "TEXT")
    private String address;

    @Column(length = 100)
    private String occupation;

    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "updated_at", insertable = false, updatable = false)
    private LocalDateTime updatedAt;
}
