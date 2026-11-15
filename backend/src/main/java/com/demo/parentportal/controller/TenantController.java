package com.demo.parentportal.controller;

import com.demo.parentportal.dto.ApiResponse;
import com.demo.parentportal.dto.TenantConfigDTO;
import com.demo.parentportal.entity.Tenant;
import com.demo.parentportal.service.TenantService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/v1/tenant")
@RequiredArgsConstructor
public class TenantController {

    private final TenantService tenantService;

    @GetMapping("/config")
    public ResponseEntity<ApiResponse<TenantConfigDTO>> getConfig(
            @RequestHeader(value = "X-Tenant", required = false) String headerTenant,
            @RequestParam(value = "tenant", required = false) String paramTenant,
            @RequestParam(value = "slug", required = false) String paramSlug) {

        String tenantKey = (headerTenant != null && !headerTenant.isBlank()) ? headerTenant :
                (paramTenant != null && !paramTenant.isBlank()) ? paramTenant :
                (paramSlug != null && !paramSlug.isBlank()) ? paramSlug : "die";

        TenantConfigDTO config = tenantService.getTenantConfig(tenantKey);
        return ResponseEntity.ok(ApiResponse.ok(config));
    }

    @GetMapping("/list")
    public ResponseEntity<ApiResponse<List<Tenant>>> listTenants() {
        return ResponseEntity.ok(ApiResponse.ok(tenantService.listAllTenants()));
    }
}
