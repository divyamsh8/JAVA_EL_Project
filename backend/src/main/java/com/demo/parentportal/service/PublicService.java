package com.demo.parentportal.service;

import com.demo.parentportal.dto.NoticeDTO;
import com.demo.parentportal.dto.TermDTO;
import com.demo.parentportal.dto.TermLinkDTO;
import com.demo.parentportal.entity.Notice;
import com.demo.parentportal.entity.Term;
import com.demo.parentportal.entity.TermLink;
import com.demo.parentportal.repository.NoticeRepository;
import com.demo.parentportal.repository.TermLinkRepository;
import com.demo.parentportal.repository.TermRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
@Transactional(readOnly = true)
public class PublicService {

    private final TermRepository termRepository;
    private final TermLinkRepository termLinkRepository;
    private final NoticeRepository noticeRepository;

    public List<TermDTO> getActiveTerms() {
        return termRepository.findByIsActiveTrue().stream()
                .map(t -> TermDTO.builder()
                        .id(t.getId())
                        .code(t.getCode())
                        .name(t.getName())
                        .academicYear(t.getAcademicYear())
                        .startDate(t.getStartDate())
                        .endDate(t.getEndDate())
                        .isActive(t.getIsActive())
                        .build())
                .collect(Collectors.toList());
    }

    public List<TermLinkDTO> getTermLinks() {
        return termLinkRepository.findByIsActiveTrueOrderByDisplayOrderAsc().stream()
                .map(l -> TermLinkDTO.builder()
                        .id(l.getId())
                        .title(l.getTitle())
                        .routeSlug(l.getRouteSlug())
                        .termCode(l.getTermCode())
                        .marginTop(l.getMarginTop())
                        .displayOrder(l.getDisplayOrder())
                        .build())
                .collect(Collectors.toList());
    }

    public Optional<NoticeDTO> getLoginNotice() {
        return noticeRepository.findFirstByIsLoginNoticeTrueAndIsActiveTrueOrderByPriorityAsc()
                .map(this::mapNotice);
    }

    public List<NoticeDTO> getAllNotices() {
        return noticeRepository.findByIsActiveTrueOrderByPriorityAsc().stream()
                .map(this::mapNotice)
                .collect(Collectors.toList());
    }

    private NoticeDTO mapNotice(Notice n) {
        return NoticeDTO.builder()
                .id(n.getId())
                .title(n.getTitle())
                .content(n.getContent())
                .isLoginNotice(n.getIsLoginNotice())
                .priority(n.getPriority())
                .build();
    }
}
