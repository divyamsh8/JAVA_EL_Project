package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Notification;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface NotificationRepository extends JpaRepository<Notification, Long> {
    List<Notification> findByTenantIdOrderByCreatedAtDesc(Long tenantId);
    List<Notification> findByTenantIdAndUserIdOrderByCreatedAtDesc(Long tenantId, Long userId);
    List<Notification> findByTenantIdAndUserIdAndIsReadFalseOrderByCreatedAtDesc(Long tenantId, Long userId);
}
