package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Notice;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface NoticeRepository extends JpaRepository<Notice, Long> {
    Optional<Notice> findFirstByIsLoginNoticeTrueAndIsActiveTrueOrderByPriorityAsc();
    List<Notice> findByIsActiveTrueOrderByPriorityAsc();
}
