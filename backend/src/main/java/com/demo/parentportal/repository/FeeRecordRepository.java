package com.demo.parentportal.repository;

import com.demo.parentportal.entity.FeeRecord;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface FeeRecordRepository extends JpaRepository<FeeRecord, Long> {
    List<FeeRecord> findByStudentId(Long studentId);
    List<FeeRecord> findByStudentIdAndTermId(Long studentId, Long termId);
}
