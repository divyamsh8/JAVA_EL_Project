package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Tenant;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface TenantRepository extends JpaRepository<Tenant, Long> {
    Optional<Tenant> findBySlug(String slug);
    Optional<Tenant> findByDomain(String domain);
    Optional<Tenant> findByCode(String code);
}
