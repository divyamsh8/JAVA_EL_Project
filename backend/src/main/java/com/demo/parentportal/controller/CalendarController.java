package com.demo.parentportal.controller;

import com.demo.parentportal.dto.ApiResponse;
import com.demo.parentportal.dto.CalendarEventDTO;
import com.demo.parentportal.entity.CalendarEvent;
import com.demo.parentportal.repository.CalendarEventRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.time.format.DateTimeFormatter;
import java.util.List;
import java.util.stream.Collectors;

@RestController
@RequestMapping("/api/v1/calendar")
@RequiredArgsConstructor
public class CalendarController {

    private final CalendarEventRepository calendarEventRepository;

    @GetMapping("/events")
    public ResponseEntity<ApiResponse<List<CalendarEventDTO>>> getEvents(
            @RequestHeader(value = "X-Tenant", required = false) String tenantSlug,
            @RequestParam(required = false) Long termId) {

        List<CalendarEvent> events = calendarEventRepository.findByTenantIdOrderByStartDateAsc(1L);

        List<CalendarEventDTO> dtos = events.stream().map(e -> CalendarEventDTO.builder()
                .id(e.getId())
                .title(e.getTitle())
                .description(e.getDescription())
                .eventType(e.getEventType())
                .startDate(e.getStartDate())
                .endDate(e.getEndDate())
                .build()
        ).collect(Collectors.toList());

        return ResponseEntity.ok(ApiResponse.ok(dtos));
    }

    @GetMapping(value = "/export.ics", produces = "text/calendar")
    public ResponseEntity<byte[]> exportIcs(
            @RequestHeader(value = "X-Tenant", required = false) String tenantSlug) {

        List<CalendarEvent> events = calendarEventRepository.findByTenantIdOrderByStartDateAsc(1L);
        DateTimeFormatter dtf = DateTimeFormatter.ofPattern("yyyyMMdd");

        StringBuilder ics = new StringBuilder();
        ics.append("BEGIN:VCALENDAR\r\n");
        ics.append("VERSION:2.0\r\n");
        ics.append("PRODID:-//Demo Institute of Engineering//Contineo Parent Portal//EN\r\n");
        ics.append("CALSCALE:GREGORIAN\r\n");
        ics.append("METHOD:PUBLISH\r\n");
        ics.append("X-WR-CALNAME:Academic Calendar 2026\r\n");

        for (CalendarEvent e : events) {
            ics.append("BEGIN:VEVENT\r\n");
            ics.append("UID:event-").append(e.getId()).append("@demo-institute.edu.in\r\n");
            ics.append("SUMMARY:").append(e.getTitle()).append("\r\n");
            if (e.getDescription() != null) {
                ics.append("DESCRIPTION:").append(e.getDescription()).append("\r\n");
            }
            ics.append("DTSTART;VALUE=DATE:").append(e.getStartDate().format(dtf)).append("\r\n");
            // End date in iCal is exclusive, so add 1 day
            ics.append("DTEND;VALUE=DATE:").append(e.getEndDate().plusDays(1).format(dtf)).append("\r\n");
            ics.append("CATEGORIES:").append(e.getEventType()).append("\r\n");
            ics.append("STATUS:CONFIRMED\r\n");
            ics.append("END:VEVENT\r\n");
        }

        ics.append("END:VCALENDAR\r\n");

        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"academic-calendar-2026.ics\"")
                .contentType(MediaType.parseMediaType("text/calendar"))
                .body(ics.toString().getBytes());
    }
}
