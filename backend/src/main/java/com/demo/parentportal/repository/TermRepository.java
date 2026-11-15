package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Term;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface TermRepository extends JpaRepository<Term, Long> {
    Optional<Term> findByCode(String code);
    Optional<Term> findByTenantIdAndCode(Long tenantId, String code);
    List<Term> findByTenantIdAndIsActiveTrue(Long tenantId);
    List<Term> findByIsActiveTrue();
}
