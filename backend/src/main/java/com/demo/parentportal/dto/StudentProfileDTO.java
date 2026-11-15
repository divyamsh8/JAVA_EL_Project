package com.demo.parentportal.dto;

import lombok.*;
import java.time.LocalDate;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class StudentProfileDTO {
    private Long id;
    private String usn;
    private String rollNo;
    private String firstName;
    private String lastName;
    private String fullName;
    private String gender;
    private LocalDate dob;
    private String bloodGroup;
    private Integer admissionYear;
    private Integer currentSem;
    private String photoUrl;

    // Department & Course
    private Long departmentId;
    private String departmentCode;
    private String departmentName;
    private String courseName;
    private String degreeLevel;

    // Parent details
    private String fatherName;
    private String motherName;
    private String primaryMobile;
    private String alternateMobile;
    private String parentEmail;
    private String address;
    private String occupation;
}
