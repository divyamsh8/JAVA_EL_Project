package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Student;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface StudentRepository extends JpaRepository<Student, Long> {
    Optional<Student> findByUsn(String usn);
    Optional<Student> findByTenantIdAndUsn(Long tenantId, String usn);
    List<Student> findByUserId(Long userId);
    List<Student> findByDepartmentId(Long departmentId);
    List<Student> findByParentId(Long parentId);
    List<Student> findByTenantId(Long tenantId);
    List<Student> findByTenantIdAndDepartmentId(Long tenantId, Long departmentId);

    @Query("SELECT s FROM Student s WHERE (LOWER(s.usn) LIKE LOWER(CONCAT('%', :query, '%')) OR LOWER(s.firstName) LIKE LOWER(CONCAT('%', :query, '%')) OR LOWER(s.lastName) LIKE LOWER(CONCAT('%', :query, '%')))")
    List<Student> searchStudents(@Param("query") String query);

    @Query("SELECT s FROM Student s WHERE s.tenantId = :tenantId AND (LOWER(s.usn) LIKE LOWER(CONCAT('%', :query, '%')) OR LOWER(s.firstName) LIKE LOWER(CONCAT('%', :query, '%')) OR LOWER(s.lastName) LIKE LOWER(CONCAT('%', :query, '%')))")
    List<Student> searchStudents(@Param("tenantId") Long tenantId, @Param("query") String query);
}
