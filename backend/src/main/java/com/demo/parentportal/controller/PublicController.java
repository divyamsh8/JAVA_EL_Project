package com.demo.parentportal.controller;

import com.demo.parentportal.dto.*;
import com.demo.parentportal.service.PublicService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/v1/public")
@RequiredArgsConstructor
public class PublicController {

    private final PublicService publicService;

    @GetMapping("/terms")
    public ResponseEntity<ApiResponse<List<TermDTO>>> getTerms() {
        return ResponseEntity.ok(ApiResponse.ok(publicService.getActiveTerms()));
    }

    @GetMapping("/term-links")
    public ResponseEntity<ApiResponse<List<TermLinkDTO>>> getTermLinks() {
        return ResponseEntity.ok(ApiResponse.ok(publicService.getTermLinks()));
    }

    @GetMapping("/notices/login")
    public ResponseEntity<ApiResponse<NoticeDTO>> getLoginNotice() {
        return publicService.getLoginNotice()
                .map(n -> ResponseEntity.ok(ApiResponse.ok(n)))
                .orElse(ResponseEntity.ok(ApiResponse.ok(null)));
    }

    @GetMapping("/notices")
    public ResponseEntity<ApiResponse<List<NoticeDTO>>> getAllNotices() {
        return ResponseEntity.ok(ApiResponse.ok(publicService.getAllNotices()));
    }
}
