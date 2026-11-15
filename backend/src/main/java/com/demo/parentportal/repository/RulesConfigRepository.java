package com.demo.parentportal.repository;

import com.demo.parentportal.entity.RulesConfig;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface RulesConfigRepository extends JpaRepository<RulesConfig, Long> {
    Optional<RulesConfig> findByTenantId(Long tenantId);
}
