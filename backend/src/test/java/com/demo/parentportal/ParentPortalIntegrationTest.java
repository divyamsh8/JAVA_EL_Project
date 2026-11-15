package com.demo.parentportal;

import com.demo.parentportal.dto.LoginRequest;
import com.demo.parentportal.dto.LoginResponse;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.http.MediaType;
import org.springframework.test.web.servlet.MockMvc;

import static org.hamcrest.Matchers.*;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@SpringBootTest
@AutoConfigureMockMvc
class ParentPortalIntegrationTest {

    @Autowired
    private MockMvc mockMvc;

    @Autowired
    private ObjectMapper objectMapper;

    @Test
    void testPublicTermsEndpoint() throws Exception {
        mockMvc.perform(get("/api/v1/public/terms"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.success").value(true))
                .andExpect(jsonPath("$.data", hasSize(greaterThanOrEqualTo(1))));
    }

    @Test
    void testPublicLoginNoticeEndpoint() throws Exception {
        mockMvc.perform(get("/api/v1/public/notices/login"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.success").value(true));
    }

    @Test
    void testValidStudentLogin_returnsToken() throws Exception {
        LoginRequest request = LoginRequest.builder()
                .username("1DM24CS001")
                .day("15")
                .month("Apr")
                .year("2005")
                .build();

        mockMvc.perform(post("/api/v1/auth/login")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(request)))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.success").value(true))
                .andExpect(jsonPath("$.token").isString())
                .andExpect(jsonPath("$.user.username").value("1DM24CS001"))
                .andExpect(jsonPath("$.user.usn").value("1DM24CS001"));
    }

    @Test
    void testInvalidStudentLogin_returnsUnauthorizedWithAttemptsRemaining() throws Exception {
        LoginRequest request = LoginRequest.builder()
                .username("1DM24CS001")
                .day("10")
                .month("Jan")
                .year("2000") // Wrong DOB
                .build();

        mockMvc.perform(post("/api/v1/auth/login")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(request)))
                .andExpect(status().isUnauthorized())
                .andExpect(jsonPath("$.success").value(false))
                .andExpect(jsonPath("$.attemptsRemaining").isNumber())
                .andExpect(jsonPath("$.message").value("You have entered an invalid username or password."));
    }

    @Test
    void testCrossStudentAccessControl_returnsForbidden() throws Exception {
        // First log in as student 1 (1DM24CS001)
        LoginRequest loginReq = LoginRequest.builder()
                .username("1DM24CS001")
                .day("15")
                .month("Apr")
                .year("2005")
                .build();

        String responseStr = mockMvc.perform(post("/api/v1/auth/login")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(loginReq)))
                .andExpect(status().isOk())
                .andReturn().getResponse().getContentAsString();

        LoginResponse loginRes = objectMapper.readValue(responseStr, LoginResponse.class);
        String token = loginRes.getToken();

        // Access sibling student 2 (Ananya Sharma) should succeed (200 OK)
        mockMvc.perform(get("/api/v1/parent/profile")
                        .param("studentId", "2")
                        .header("Authorization", "Bearer " + token))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.success").value(true))
                .andExpect(jsonPath("$.data.usn").value("1DM24EC002"));

        // Attempt to access unrelated student 3 (Nikhil Hegde) with student 1's token -> 403 Forbidden
        mockMvc.perform(get("/api/v1/parent/profile")
                        .param("studentId", "3")
                        .header("Authorization", "Bearer " + token))
                .andExpect(status().isForbidden())
                .andExpect(jsonPath("$.success").value(false));
    }

    @Test
    void testTenantConfig_DIE_returnsDobDropdownSchema() throws Exception {
        mockMvc.perform(get("/api/v1/tenant/config").header("X-Tenant", "die"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.success").value(true))
                .andExpect(jsonPath("$.data.slug").value("die"))
                .andExpect(jsonPath("$.data.loginSchema.credentialType").value("DOB_DROPDOWN"))
                .andExpect(jsonPath("$.data.modules", hasSize(greaterThanOrEqualTo(5))));
    }

    @Test
    void testTenantConfig_AIT_returnsPasswordSchema() throws Exception {
        mockMvc.perform(get("/api/v1/tenant/config").header("X-Tenant", "ait"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.success").value(true))
                .andExpect(jsonPath("$.data.slug").value("ait"))
                .andExpect(jsonPath("$.data.loginSchema.credentialType").value("PASSWORD"));
    }

    @Test
    void testCalendarIcsExport_returnsCalendarStream() throws Exception {
        mockMvc.perform(get("/api/v1/calendar/export.ics"))
                .andExpect(status().isOk())
                .andExpect(header().string("Content-Type", containsString("text/calendar")))
                .andExpect(content().string(containsString("BEGIN:VCALENDAR")))
                .andExpect(content().string(containsString("END:VCALENDAR")));
    }

    @Test
    void testPdfReportCardGeneration_returnsPdfStream() throws Exception {
        // Log in to get token
        LoginRequest loginReq = LoginRequest.builder()
                .username("1DM24CS001")
                .day("15")
                .month("Apr")
                .year("2005")
                .build();

        String responseStr = mockMvc.perform(post("/api/v1/auth/login")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(loginReq)))
                .andExpect(status().isOk())
                .andReturn().getResponse().getContentAsString();

        LoginResponse loginRes = objectMapper.readValue(responseStr, LoginResponse.class);
        String token = loginRes.getToken();

        mockMvc.perform(get("/api/v1/parent/reports/report-card.pdf")
                        .header("Authorization", "Bearer " + token))
                .andExpect(status().isOk())
                .andExpect(header().string("Content-Type", containsString("application/pdf")));
    }
}
