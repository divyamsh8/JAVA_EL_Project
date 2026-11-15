package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Timetable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface TimetableRepository extends JpaRepository<Timetable, Long> {
    List<Timetable> findByDepartmentIdAndSemesterOrderByDayOfWeekAscPeriodNumberAsc(Long departmentId, Integer semester);
    List<Timetable> findByTermIdAndDepartmentIdAndSemester(Long termId, Long departmentId, Integer semester);
}
