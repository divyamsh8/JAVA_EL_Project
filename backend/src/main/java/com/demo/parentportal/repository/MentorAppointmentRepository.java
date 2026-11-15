package com.demo.parentportal.repository;

import com.demo.parentportal.entity.MentorAppointment;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface MentorAppointmentRepository extends JpaRepository<MentorAppointment, Long> {
    List<MentorAppointment> findByTenantIdAndStudentIdOrderByAppointmentTimeAsc(Long tenantId, Long studentId);
}
