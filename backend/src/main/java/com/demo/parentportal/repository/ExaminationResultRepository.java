package com.demo.parentportal.repository;

import com.demo.parentportal.entity.ExaminationResult;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ExaminationResultRepository extends JpaRepository<ExaminationResult, Long> {
    List<ExaminationResult> findByStudentId(Long studentId);
    List<ExaminationResult> findByStudentIdAndTermId(Long studentId, Long termId);
    List<ExaminationResult> findByStudentIdAndSemester(Long studentId, Integer semester);
}
