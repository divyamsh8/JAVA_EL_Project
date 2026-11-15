package com.demo.parentportal.repository;

import com.demo.parentportal.entity.LeaveRequest;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface LeaveRequestRepository extends JpaRepository<LeaveRequest, Long> {
    List<LeaveRequest> findByTenantIdAndStudentIdOrderBySubmittedAtDesc(Long tenantId, Long studentId);
    List<LeaveRequest> findByTenantIdOrderBySubmittedAtDesc(Long tenantId);
}
