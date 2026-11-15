package com.demo.parentportal.entity;

import jakarta.persistence.*;
import lombok.*;

@Entity
@Table(name = "modules")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Module {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "module_key", nullable = false, unique = true, length = 50)
    private String moduleKey;

    @Column(name = "default_label", nullable = false, length = 100)
    private String defaultLabel;

    @Column(name = "default_icon", nullable = false, length = 50)
    private String defaultIcon;

    @Column(name = "default_order", nullable = false)
    private Integer defaultOrder;

    @Column(name = "default_route", nullable = false, length = 100)
    private String defaultRoute;
}
