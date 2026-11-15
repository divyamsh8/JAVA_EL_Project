package com.demo.parentportal.service;

import com.demo.parentportal.dto.NotificationDTO;
import com.demo.parentportal.entity.Notification;
import com.demo.parentportal.repository.NotificationRepository;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.web.servlet.mvc.method.annotation.SseEmitter;

import java.io.IOException;
import java.time.LocalDateTime;
import java.util.List;
import java.util.concurrent.CopyOnWriteArrayList;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
@Slf4j
public class SseNotificationService {

    private final List<SseEmitter> emitters = new CopyOnWriteArrayList<>();
    private final NotificationRepository notificationRepository;

    public SseEmitter createEmitter() {
        // 30 minutes timeout
        SseEmitter emitter = new SseEmitter(1800000L);
        emitters.add(emitter);

        emitter.onCompletion(() -> emitters.remove(emitter));
        emitter.onTimeout(() -> emitters.remove(emitter));
        emitter.onError(e -> emitters.remove(emitter));

        try {
            emitter.send(SseEmitter.event()
                    .name("CONNECTED")
                    .data("Connected to live notification stream"));
        } catch (IOException e) {
            emitters.remove(emitter);
        }

        return emitter;
    }

    public void broadcast(Notification notification) {
        NotificationDTO dto = NotificationDTO.builder()
                .id(notification.getId())
                .title(notification.getTitle())
                .message(notification.getMessage())
                .type(notification.getType())
                .isRead(notification.getIsRead())
                .createdAt(notification.getCreatedAt())
                .build();

        for (SseEmitter emitter : emitters) {
            try {
                emitter.send(SseEmitter.event()
                        .name("NOTIFICATION")
                        .data(dto));
            } catch (Exception e) {
                emitters.remove(emitter);
            }
        }
    }

    public Notification sendNotification(Long tenantId, Long userId, String title, String message, String type) {
        Notification n = Notification.builder()
                .tenantId(tenantId)
                .userId(userId != null ? userId : 3L)
                .title(title)
                .message(message)
                .type(type != null ? type : "INFO")
                .isRead(false)
                .createdAt(LocalDateTime.now())
                .build();
        Notification saved = notificationRepository.save(n);
        broadcast(saved);
        return saved;
    }

    public List<NotificationDTO> getNotificationsForUser(Long tenantId, Long userId) {
        List<Notification> list = (userId != null)
                ? notificationRepository.findByTenantIdAndUserIdOrderByCreatedAtDesc(tenantId, userId)
                : notificationRepository.findByTenantIdOrderByCreatedAtDesc(tenantId);

        return list.stream().map(n -> NotificationDTO.builder()
                .id(n.getId())
                .title(n.getTitle())
                .message(n.getMessage())
                .type(n.getType())
                .isRead(n.getIsRead())
                .createdAt(n.getCreatedAt())
                .build()
        ).collect(Collectors.toList());
    }

    public void markAsRead(Long id) {
        notificationRepository.findById(id).ifPresent(n -> {
            n.setIsRead(true);
            notificationRepository.save(n);
        });
    }
}
