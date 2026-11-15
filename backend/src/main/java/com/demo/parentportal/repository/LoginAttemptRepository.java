package com.demo.parentportal.repository;

import com.demo.parentportal.entity.LoginAttempt;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

@Repository
public interface LoginAttemptRepository extends JpaRepository<LoginAttempt, Long> {
    @Query("SELECT COUNT(l) FROM LoginAttempt l WHERE l.username = :username AND l.ipAddress = :ipAddress AND l.attemptTime > :thresholdTime")
    long countRecentAttempts(@Param("username") String username, @Param("ipAddress") String ipAddress, @Param("thresholdTime") Long thresholdTime);

    @Modifying
    @Query("DELETE FROM LoginAttempt l WHERE l.username = :username AND l.ipAddress = :ipAddress")
    void clearAttempts(@Param("username") String username, @Param("ipAddress") String ipAddress);

    @Modifying
    @Query("DELETE FROM LoginAttempt l WHERE l.attemptTime < :olderThanTime")
    void deleteOlderThan(@Param("olderThanTime") Long olderThanTime);
}
