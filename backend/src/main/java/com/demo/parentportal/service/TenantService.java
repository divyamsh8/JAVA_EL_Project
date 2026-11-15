package com.demo.parentportal.service;

import com.demo.parentportal.dto.TenantConfigDTO;
import com.demo.parentportal.dto.TermDTO;
import com.demo.parentportal.dto.TermLinkDTO;
import com.demo.parentportal.entity.*;
import com.demo.parentportal.repository.*;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.*;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
@Slf4j
@Transactional(readOnly = true)
public class TenantService {

    private final TenantRepository tenantRepository;
    private final TenantSettingsRepository tenantSettingsRepository;
    private final LoginSchemaRepository loginSchemaRepository;
    private final TenantModuleRepository tenantModuleRepository;
    private final RulesConfigRepository rulesConfigRepository;
    private final TermRepository termRepository;
    private final TermLinkRepository termLinkRepository;
    private final ObjectMapper objectMapper;

    public Tenant resolveTenant(String identifier) {
        if (identifier == null || identifier.isBlank() || "default".equalsIgnoreCase(identifier)) {
            return tenantRepository.findBySlug("die").orElseGet(() -> tenantRepository.findAll().get(0));
        }

        // Try slug first
        Optional<Tenant> bySlug = tenantRepository.findBySlug(identifier.toLowerCase());
        if (bySlug.isPresent()) return bySlug.get();

        // Try code
        Optional<Tenant> byCode = tenantRepository.findByCode(identifier.toUpperCase());
        if (byCode.isPresent()) return byCode.get();

        // Try domain
        Optional<Tenant> byDomain = tenantRepository.findByDomain(identifier.toLowerCase());
        if (byDomain.isPresent()) return byDomain.get();

        return tenantRepository.findBySlug("die").orElseGet(() -> tenantRepository.findAll().get(0));
    }

    public List<Tenant> listAllTenants() {
        return tenantRepository.findAll();
    }

    public TenantConfigDTO getTenantConfig(String identifier) {
        Tenant tenant = resolveTenant(identifier);
        Long tenantId = tenant.getId();

        TenantSettings settings = tenantSettingsRepository.findByTenantId(tenantId)
                .orElse(TenantSettings.builder()
                        .tenantId(tenantId)
                        .themeJson("{}")
                        .welcomeText("Welcome to the Institutional Parent Portal")
                        .noticeText("Academic sessions are active")
                        .footerText("Copyright © Institutional Platform")
                        .defaultLanguage("en")
                        .build());

        Map<String, Object> themeMap;
        try {
            themeMap = objectMapper.readValue(settings.getThemeJson(), new TypeReference<>() {});
        } catch (Exception e) {
            themeMap = Collections.emptyMap();
        }

        LoginSchema schema = loginSchemaRepository.findByTenantId(tenantId)
                .orElse(LoginSchema.builder()
                        .tenantId(tenantId)
                        .identifierField("USN")
                        .credentialType("DOB_DROPDOWN")
                        .allowedTermsJson("[]")
                        .rateLimitMax(5)
                        .lockoutMinutes(15)
                        .build());

        List<String> allowedTermsList;
        try {
            allowedTermsList = (schema.getAllowedTermsJson() != null)
                    ? objectMapper.readValue(schema.getAllowedTermsJson(), new TypeReference<>() {})
                    : Collections.emptyList();
        } catch (Exception e) {
            allowedTermsList = Collections.emptyList();
        }

        TenantConfigDTO.LoginSchemaConfig loginSchemaDTO = TenantConfigDTO.LoginSchemaConfig.builder()
                .identifierField(schema.getIdentifierField())
                .credentialType(schema.getCredentialType())
                .allowedTerms(allowedTermsList)
                .rateLimitMax(schema.getRateLimitMax())
                .lockoutMinutes(schema.getLockoutMinutes())
                .build();

        List<TenantModule> tenantModules = tenantModuleRepository.findByTenantIdAndIsEnabledTrueOrderByDisplayOrderAsc(tenantId);
        List<TenantConfigDTO.ModuleConfig> moduleConfigs = tenantModules.stream().map(tm -> {
            com.demo.parentportal.entity.Module m = tm.getModule();
            return TenantConfigDTO.ModuleConfig.builder()
                    .key(m.getModuleKey())
                    .label(tm.getCustomLabel() != null ? tm.getCustomLabel() : m.getDefaultLabel())
                    .icon(m.getDefaultIcon())
                    .route(m.getDefaultRoute())
                    .order(tm.getDisplayOrder())
                    .enabled(tm.getIsEnabled())
                    .build();
        }).collect(Collectors.toList());

        RulesConfig rules = rulesConfigRepository.findByTenantId(tenantId)
                .orElse(RulesConfig.builder().tenantId(tenantId).build());

        TenantConfigDTO.RulesConfigDTO rulesDTO = TenantConfigDTO.RulesConfigDTO.builder()
                .shortageThreshold(rules.getShortageThreshold())
                .cieMaxMarks(rules.getCieMaxMarks())
                .cieMinEligibility(rules.getCieMinEligibility())
                .feeDueGraceDays(rules.getFeeDueGraceDays())
                .build();

        List<TermDTO> terms = termRepository.findByTenantIdAndIsActiveTrue(tenantId).stream().map(t ->
                TermDTO.builder()
                        .id(t.getId())
                        .code(t.getCode())
                        .name(t.getName())
                        .academicYear(t.getAcademicYear())
                        .startDate(t.getStartDate())
                        .endDate(t.getEndDate())
                        .isActive(t.getIsActive())
                        .build()
        ).collect(Collectors.toList());

        List<TermLinkDTO> termLinks = termLinkRepository.findByTenantIdAndIsActiveTrueOrderByDisplayOrderAsc(tenantId).stream().map(l ->
                TermLinkDTO.builder()
                        .id(l.getId())
                        .title(l.getTitle())
                        .routeSlug(l.getRouteSlug())
                        .termCode(l.getTermCode())
                        .marginTop(l.getMarginTop())
                        .displayOrder(l.getDisplayOrder())
                        .build()
        ).collect(Collectors.toList());

        return TenantConfigDTO.builder()
                .id(tenant.getId())
                .slug(tenant.getSlug())
                .name(tenant.getName())
                .code(tenant.getCode())
                .theme(themeMap)
                .welcomeText(settings.getWelcomeText())
                .noticeText(settings.getNoticeText())
                .footerText(settings.getFooterText())
                .contactEmail(settings.getContactEmail())
                .contactPhone(settings.getContactPhone())
                .defaultLanguage(settings.getDefaultLanguage())
                .loginSchema(loginSchemaDTO)
                .modules(moduleConfigs)
                .terms(terms)
                .termLinks(termLinks)
                .rules(rulesDTO)
                .build();
    }

    @Transactional
    public void updateTheme(Long tenantId, Map<String, Object> themeMap, String welcomeText, String noticeText, String footerText) {
        TenantSettings settings = tenantSettingsRepository.findByTenantId(tenantId)
                .orElseGet(() -> TenantSettings.builder().tenantId(tenantId).build());

        try {
            settings.setThemeJson(objectMapper.writeValueAsString(themeMap));
        } catch (Exception e) {
            log.error("Failed to serialize theme JSON", e);
        }

        if (welcomeText != null) settings.setWelcomeText(welcomeText);
        if (noticeText != null) settings.setNoticeText(noticeText);
        if (footerText != null) settings.setFooterText(footerText);

        tenantSettingsRepository.save(settings);
    }
}
