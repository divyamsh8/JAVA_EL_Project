package com.demo.parentportal.service;

import com.demo.parentportal.dto.AdminStatsDTO;
import com.demo.parentportal.dto.AttendanceDTO;
import com.demo.parentportal.dto.StudentProfileDTO;
import com.demo.parentportal.entity.*;
import com.demo.parentportal.repository.*;
import com.demo.parentportal.repository.FeeRecordRepository;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
@Slf4j
@Transactional
public class AdminService {

    private final StudentRepository studentRepository;
    private final DepartmentRepository departmentRepository;
    private final CourseRepository courseRepository;
    private final FeeRecordRepository feeRecordRepository;
    private final LoginLogRepository loginLogRepository;
    private final NoticeRepository noticeRepository;
    private final ParentPortalService parentPortalService;
    private final TenantSettingsRepository tenantSettingsRepository;
    private final TenantModuleRepository tenantModuleRepository;
    private final RulesConfigRepository rulesConfigRepository;
    private final ModuleRepository moduleRepository;

    @Transactional(readOnly = true)
    public AdminStatsDTO getStats() {
        long totalStudents = studentRepository.count();
        long totalDepts = departmentRepository.count();
        long totalCourses = courseRepository.count();

        List<Student> allStudents = studentRepository.findAll();
        List<StudentProfileDTO> shortageList = new ArrayList<>();

        for (Student s : allStudents) {
            AttendanceDTO att = parentPortalService.getAttendance(s.getId(), null);
            if (att.isShortageAlert()) {
                shortageList.add(parentPortalService.getProfile(s.getId()));
            }
        }

        BigDecimal pendingFees = feeRecordRepository.findAll().stream()
                .map(FeeRecord::getBalanceAmount)
                .reduce(BigDecimal.ZERO, BigDecimal::add);

        List<LoginLog> recentLogs = loginLogRepository.findTop50ByOrderByCreatedAtDesc();

        return AdminStatsDTO.builder()
                .totalStudents(totalStudents)
                .totalDepartments(totalDepts)
                .totalCourses(totalCourses)
                .shortageCount(shortageList.size())
                .shortageStudents(shortageList)
                .totalFeesPending(pendingFees)
                .recentLogs(recentLogs)
                .build();
    }

    @Transactional(readOnly = true)
    public List<StudentProfileDTO> listStudents(String query, Long deptId) {
        List<Student> list;
        if (query != null && !query.isBlank()) {
            list = studentRepository.searchStudents(query.trim());
        } else if (deptId != null) {
            list = studentRepository.findByDepartmentId(deptId);
        } else {
            list = studentRepository.findAll();
        }

        return list.stream()
                .map(s -> parentPortalService.getProfile(s.getId()))
                .collect(Collectors.toList());
    }

    public Notice updateNotice(Long id, String title, String content, Boolean isLoginNotice) {
        Notice notice = noticeRepository.findById(id)
                .orElseThrow(() -> new IllegalArgumentException("Notice not found with id: " + id));
        if (title != null) notice.setTitle(title);
        if (content != null) notice.setContent(content);
        if (isLoginNotice != null) notice.setIsLoginNotice(isLoginNotice);
        return noticeRepository.save(notice);
    }

    public TenantSettings updateTenantSettings(Long tenantId, String themeJson, String welcomeText, String noticeText, String footerText) {
        TenantSettings settings = tenantSettingsRepository.findByTenantId(tenantId)
                .orElse(TenantSettings.builder().tenantId(tenantId).build());
        if (themeJson != null) settings.setThemeJson(themeJson);
        if (welcomeText != null) settings.setWelcomeText(welcomeText);
        if (noticeText != null) settings.setNoticeText(noticeText);
        if (footerText != null) settings.setFooterText(footerText);
        return tenantSettingsRepository.save(settings);
    }

    public TenantModule updateTenantModule(Long tenantId, String moduleKey, Boolean isEnabled, String customLabel, Integer displayOrder) {
        TenantModule tm = tenantModuleRepository.findByTenantIdAndModule_ModuleKey(tenantId, moduleKey)
                .orElseGet(() -> {
                    com.demo.parentportal.entity.Module mod = moduleRepository.findByModuleKey(moduleKey)
                            .orElseThrow(() -> new IllegalArgumentException("Module not found: " + moduleKey));
                    return TenantModule.builder().tenantId(tenantId).module(mod).isEnabled(true).displayOrder(10).build();
                });
        if (isEnabled != null) tm.setIsEnabled(isEnabled);
        if (customLabel != null) tm.setCustomLabel(customLabel);
        if (displayOrder != null) tm.setDisplayOrder(displayOrder);
        return tenantModuleRepository.save(tm);
    }

    public RulesConfig updateRules(Long tenantId, BigDecimal shortageThreshold, BigDecimal cieMaxMarks, BigDecimal cieMinEligibility, Integer feeGraceDays) {
        RulesConfig rules = rulesConfigRepository.findByTenantId(tenantId)
                .orElse(RulesConfig.builder().tenantId(tenantId).build());
        if (shortageThreshold != null) rules.setShortageThreshold(shortageThreshold);
        if (cieMaxMarks != null) rules.setCieMaxMarks(cieMaxMarks);
        if (cieMinEligibility != null) rules.setCieMinEligibility(cieMinEligibility);
        if (feeGraceDays != null) rules.setFeeDueGraceDays(feeGraceDays);
        return rulesConfigRepository.save(rules);
    }

    @Transactional(readOnly = true)
    public List<LoginLog> getAuditLogs() {
        return loginLogRepository.findTop50ByOrderByCreatedAtDesc();
    }
}
