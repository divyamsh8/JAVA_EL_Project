package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Attendance;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface AttendanceRepository extends JpaRepository<Attendance, Long> {
    List<Attendance> findByStudentId(Long studentId);
    List<Attendance> findByStudentIdAndTermId(Long studentId, Long termId);
    List<Attendance> findByStudentIdAndSubjectId(Long studentId, Long subjectId);
}
