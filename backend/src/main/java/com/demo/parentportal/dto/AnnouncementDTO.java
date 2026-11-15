package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalDate;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class AnnouncementDTO {
    private Long id;
    private Long termId;
    private String termName;
    private String title;
    private String category;
    private String description;
    private String attachmentFile;
    private LocalDate publishedDate;
}
