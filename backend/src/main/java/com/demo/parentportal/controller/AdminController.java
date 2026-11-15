package com.demo.parentportal.controller;

import com.demo.parentportal.dto.*;
import com.demo.parentportal.entity.*;
import com.demo.parentportal.service.AdminService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/v1/admin")
@RequiredArgsConstructor
@PreAuthorize("hasAnyRole('SUPER_ADMIN', 'DEPT_ADMIN')")
public class AdminController {

    private final AdminService adminService;

    @GetMapping("/stats")
    public ResponseEntity<ApiResponse<AdminStatsDTO>> getStats() {
        return ResponseEntity.ok(ApiResponse.ok(adminService.getStats()));
    }

    @GetMapping("/students")
    public ResponseEntity<ApiResponse<List<StudentProfileDTO>>> listStudents(
            @RequestParam(required = false) String search,
            @RequestParam(required = false) Long departmentId) {
        return ResponseEntity.ok(ApiResponse.ok(adminService.listStudents(search, departmentId)));
    }

    @PutMapping("/notices/{id}")
    public ResponseEntity<ApiResponse<Notice>> updateNotice(
            @PathVariable Long id,
            @RequestBody NoticeDTO dto) {
        Notice updated = adminService.updateNotice(id, dto.getTitle(), dto.getContent(), dto.getIsLoginNotice());
        return ResponseEntity.ok(ApiResponse.ok("Notice updated successfully", updated));
    }

    @PutMapping("/tenant/{id}/settings")
    public ResponseEntity<ApiResponse<TenantSettings>> updateTenantSettings(
            @PathVariable Long id,
            @RequestBody Map<String, String> body) {
        TenantSettings updated = adminService.updateTenantSettings(
                id,
                body.get("themeJson"),
                body.get("welcomeText"),
                body.get("noticeText"),
                body.get("footerText")
        );
        return ResponseEntity.ok(ApiResponse.ok("Tenant settings updated", updated));
    }

    @PutMapping("/tenant/{id}/module")
    public ResponseEntity<ApiResponse<TenantModule>> updateTenantModule(
            @PathVariable Long id,
            @RequestBody Map<String, Object> body) {
        String key = (String) body.get("moduleKey");
        Boolean enabled = (Boolean) body.get("isEnabled");
        String customLabel = (String) body.get("customLabel");
        Integer order = body.get("displayOrder") != null ? ((Number) body.get("displayOrder")).intValue() : null;

        TenantModule updated = adminService.updateTenantModule(id, key, enabled, customLabel, order);
        return ResponseEntity.ok(ApiResponse.ok("Module updated", updated));
    }

    @PutMapping("/tenant/{id}/rules")
    public ResponseEntity<ApiResponse<RulesConfig>> updateRules(
            @PathVariable Long id,
            @RequestBody RulesConfig rules) {
        RulesConfig updated = adminService.updateRules(
                id,
                rules.getShortageThreshold(),
                rules.getCieMaxMarks(),
                rules.getCieMinEligibility(),
                rules.getFeeDueGraceDays()
        );
        return ResponseEntity.ok(ApiResponse.ok("Rules config updated", updated));
    }

    @GetMapping("/audit-logs")
    public ResponseEntity<ApiResponse<List<LoginLog>>> getAuditLogs() {
        return ResponseEntity.ok(ApiResponse.ok(adminService.getAuditLogs()));
    }
}
