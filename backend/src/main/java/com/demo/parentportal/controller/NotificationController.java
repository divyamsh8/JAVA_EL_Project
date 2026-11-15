package com.demo.parentportal.controller;

import com.demo.parentportal.dto.ApiResponse;
import com.demo.parentportal.dto.NotificationDTO;
import com.demo.parentportal.entity.Notification;
import com.demo.parentportal.entity.User;
import com.demo.parentportal.repository.UserRepository;
import com.demo.parentportal.service.SseNotificationService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.method.annotation.SseEmitter;

import java.util.List;

@RestController
@RequiredArgsConstructor
public class NotificationController {

    private final SseNotificationService sseNotificationService;
    private final UserRepository userRepository;

    @GetMapping(value = "/api/v1/notifications/stream", produces = MediaType.TEXT_EVENT_STREAM_VALUE)
    public SseEmitter streamNotifications() {
        return sseNotificationService.createEmitter();
    }

    @GetMapping("/api/v1/parent/notifications")
    @PreAuthorize("hasAnyRole('PARENT', 'SUPER_ADMIN')")
    public ResponseEntity<ApiResponse<List<NotificationDTO>>> getParentNotifications(
            Authentication authentication,
            @RequestHeader(value = "X-Tenant", required = false) String tenantSlug) {

        String username = authentication.getName();
        User user = userRepository.findByUsername(username).orElse(null);
        Long userId = user != null ? user.getId() : null;
        Long tenantId = user != null ? user.getTenantId() : 1L;

        List<NotificationDTO> list = sseNotificationService.getNotificationsForUser(tenantId, userId);
        return ResponseEntity.ok(ApiResponse.ok(list));
    }

    @PutMapping("/api/v1/parent/notifications/{id}/read")
    @PreAuthorize("hasAnyRole('PARENT', 'SUPER_ADMIN')")
    public ResponseEntity<ApiResponse<Void>> markAsRead(@PathVariable Long id) {
        sseNotificationService.markAsRead(id);
        return ResponseEntity.ok(ApiResponse.ok("Marked as read", null));
    }

    @PostMapping("/api/v1/admin/notifications/broadcast")
    @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'DEPT_ADMIN')")
    public ResponseEntity<ApiResponse<Notification>> broadcastNotification(
            @RequestBody NotificationDTO request) {

        Notification sent = sseNotificationService.sendNotification(
                1L,
                null,
                request.getTitle(),
                request.getMessage(),
                request.getType() != null ? request.getType() : "INFO"
        );
        return ResponseEntity.ok(ApiResponse.ok("Notification broadcast successfully", sent));
    }
}
