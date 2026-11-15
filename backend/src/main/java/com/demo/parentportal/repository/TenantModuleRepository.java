package com.demo.parentportal.repository;

import com.demo.parentportal.entity.TenantModule;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface TenantModuleRepository extends JpaRepository<TenantModule, Long> {
    List<TenantModule> findByTenantIdAndIsEnabledTrueOrderByDisplayOrderAsc(Long tenantId);
    List<TenantModule> findByTenantIdOrderByDisplayOrderAsc(Long tenantId);
    Optional<TenantModule> findByTenantIdAndModule_ModuleKey(Long tenantId, String moduleKey);
}
