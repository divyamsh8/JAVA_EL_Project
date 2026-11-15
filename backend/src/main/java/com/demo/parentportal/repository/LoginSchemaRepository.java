package com.demo.parentportal.repository;

import com.demo.parentportal.entity.LoginSchema;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface LoginSchemaRepository extends JpaRepository<LoginSchema, Long> {
    Optional<LoginSchema> findByTenantId(Long tenantId);
}
