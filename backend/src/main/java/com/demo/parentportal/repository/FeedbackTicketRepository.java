package com.demo.parentportal.repository;

import com.demo.parentportal.entity.FeedbackTicket;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface FeedbackTicketRepository extends JpaRepository<FeedbackTicket, Long> {
    List<FeedbackTicket> findByTenantIdAndStudentIdOrderByCreatedAtDesc(Long tenantId, Long studentId);
    List<FeedbackTicket> findByTenantIdOrderByCreatedAtDesc(Long tenantId);
}
