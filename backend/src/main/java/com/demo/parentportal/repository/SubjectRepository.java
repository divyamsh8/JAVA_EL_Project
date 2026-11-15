package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Subject;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface SubjectRepository extends JpaRepository<Subject, Long> {
    List<Subject> findByDepartmentIdAndSemester(Long departmentId, Integer semester);
    List<Subject> findByTermId(Long termId);
}
