package com.demo.parentportal.repository;

import com.demo.parentportal.entity.TermLink;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface TermLinkRepository extends JpaRepository<TermLink, Long> {
    List<TermLink> findByIsActiveTrueOrderByDisplayOrderAsc();
    List<TermLink> findByTenantId(Long tenantId);
    List<TermLink> findByTenantIdAndIsActiveTrueOrderByDisplayOrderAsc(Long tenantId);
}
