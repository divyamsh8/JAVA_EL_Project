package com.demo.parentportal.repository;

import com.demo.parentportal.entity.Module;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface ModuleRepository extends JpaRepository<Module, Long> {
    Optional<Module> findByModuleKey(String moduleKey);
    List<Module> findAllByOrderByDefaultOrderAsc();
}
