package com.demo.parentportal.dto;

import lombok.*;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class TermLinkDTO {
    private Long id;
    private String title;
    private String routeSlug;
    private String termCode;
    private Integer marginTop;
    private Integer displayOrder;
}
