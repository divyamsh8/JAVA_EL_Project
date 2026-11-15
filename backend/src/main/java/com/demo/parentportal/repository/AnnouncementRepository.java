package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Announcement;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface AnnouncementRepository extends JpaRepository<Announcement, Long> {
    List<Announcement> findByIsPublishedTrueOrderByPublishedDateDesc();
    List<Announcement> findByTermIdAndIsPublishedTrueOrderByPublishedDateDesc(Long termId);
}
