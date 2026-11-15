package com.demo.parentportal.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;

@Controller
public class SpaWebController {

    @RequestMapping(value = {
            "/",
            "/parentseven/**",
            "/parentssupply/**",
            "/parentssupplypart2/**",
            "/login/**",
            "/forgot-password/**",
            "/dashboard/**",
            "/attendance/**",
            "/cie/**",
            "/results/**",
            "/timetable/**",
            "/announcements/**",
            "/fees/**",
            "/calendar/**",
            "/leave/**",
            "/feedback/**",
            "/mentors/**",
            "/profile/**",
            "/change-password/**",
            "/admin/**"
    })
    public String forwardSpaRoutes() {
        return "forward:/index.html";
    }
}
