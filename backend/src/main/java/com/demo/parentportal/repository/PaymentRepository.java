package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Payment;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface PaymentRepository extends JpaRepository<Payment, Long> {
    List<Payment> findByTenantIdAndStudentIdOrderByCreatedAtDesc(Long tenantId, Long studentId);
    Optional<Payment> findByTransactionRef(String transactionRef);
}
