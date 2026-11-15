package com.demo.parentportal.security;

import com.demo.parentportal.entity.LoginAttempt;
import com.demo.parentportal.entity.LoginLog;
import com.demo.parentportal.repository.LoginAttemptRepository;
import com.demo.parentportal.repository.LoginLogRepository;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;

@Service
@RequiredArgsConstructor
@Slf4j
public class RateLimiterService {

    private final LoginAttemptRepository loginAttemptRepository;
    private final LoginLogRepository loginLogRepository;

    @Value("${app.rate-limit.max-attempts:5}")
    private int maxAttempts;

    @Value("${app.rate-limit.window-minutes:15}")
    private int windowMinutes;

    public boolean isBlocked(String username, String ipAddress) {
        long thresholdTime = Instant.now().getEpochSecond() - (windowMinutes * 60L);
        long failedCount = loginAttemptRepository.countRecentAttempts(username, ipAddress, thresholdTime);
        return failedCount >= maxAttempts;
    }

    public int getRemainingAttempts(String username, String ipAddress) {
        long thresholdTime = Instant.now().getEpochSecond() - (windowMinutes * 60L);
        long failedCount = loginAttemptRepository.countRecentAttempts(username, ipAddress, thresholdTime);
        int remaining = maxAttempts - (int) failedCount;
        return Math.max(0, remaining);
    }

    @Transactional
    public void recordFailedAttempt(String username, String ipAddress, String userAgent, String reason) {
        long now = Instant.now().getEpochSecond();
        loginAttemptRepository.save(LoginAttempt.builder()
                .username(username)
                .ipAddress(ipAddress)
                .attemptTime(now)
                .build());

        loginLogRepository.save(LoginLog.builder()
                .username(username)
                .ipAddress(ipAddress)
                .userAgent(userAgent)
                .status("FAILED")
                .failureReason(reason)
                .build());
    }

    @Transactional
    public void recordSuccessfulLogin(String username, String ipAddress, String userAgent) {
        loginAttemptRepository.clearAttempts(username, ipAddress);

        loginLogRepository.save(LoginLog.builder()
                .username(username)
                .ipAddress(ipAddress)
                .userAgent(userAgent)
                .status("SUCCESS")
                .build());
    }

    @Transactional
    public void recordBlockedAttempt(String username, String ipAddress, String userAgent) {
        loginLogRepository.save(LoginLog.builder()
                .username(username)
                .ipAddress(ipAddress)
                .userAgent(userAgent)
                .status("LOCKED")
                .failureReason("Account temporarily locked due to too many failed attempts")
                .build());
    }
}
