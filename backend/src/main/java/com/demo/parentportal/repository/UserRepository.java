package com.demo.parentportal.repository;

import com.demo.parentportal.entity.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface UserRepository extends JpaRepository<User, Long> {
    Optional<User> findByUsername(String username);
    Optional<User> findByTenantIdAndUsername(Long tenantId, String username);
    boolean existsByTenantIdAndUsername(Long tenantId, String username);
}
