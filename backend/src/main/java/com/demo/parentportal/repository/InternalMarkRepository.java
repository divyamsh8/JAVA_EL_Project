package com.demo.parentportal.repository;

import com.demo.parentportal.entity.InternalMark;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface InternalMarkRepository extends JpaRepository<InternalMark, Long> {
    List<InternalMark> findByStudentId(Long studentId);
    List<InternalMark> findByStudentIdAndTermId(Long studentId, Long termId);
    List<InternalMark> findByStudentIdAndSubjectId(Long studentId, Long subjectId);
}
