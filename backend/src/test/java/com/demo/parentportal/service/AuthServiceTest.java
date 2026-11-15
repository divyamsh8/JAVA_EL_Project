package com.demo.parentportal.service;

import com.demo.parentportal.dto.LoginRequest;
import com.demo.parentportal.dto.LoginResponse;
import com.demo.parentportal.entity.Role;
import com.demo.parentportal.entity.Student;
import com.demo.parentportal.entity.User;
import com.demo.parentportal.repository.RoleRepository;
import com.demo.parentportal.repository.StudentRepository;
import com.demo.parentportal.repository.UserRepository;
import com.demo.parentportal.security.JwtUtils;
import com.demo.parentportal.security.RateLimiterService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.time.LocalDate;
import java.util.Optional;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class AuthServiceTest {

    @Mock
    private UserRepository userRepository;

    @Mock
    private StudentRepository studentRepository;

    @Mock
    private RoleRepository roleRepository;

    @Mock
    private PasswordEncoder passwordEncoder;

    @Mock
    private JwtUtils jwtUtils;

    @Mock
    private RateLimiterService rateLimiterService;

    @InjectMocks
    private AuthService authService;

    @Test
    void testNormalizeAndValidateDob_validDate() {
        String dob = authService.normalizeAndValidateDob("15", "Apr", "2005");
        assertEquals("2005-04-15", dob);

        String dob2 = authService.normalizeAndValidateDob("29", "Feb", "2024"); // Leap year
        assertEquals("2024-02-29", dob2);
    }

    @Test
    void testNormalizeAndValidateDob_invalidDateRejection() {
        // February 31st does not exist
        assertThrows(Exception.class, () -> authService.normalizeAndValidateDob("31", "Feb", "2025"));

        // Feb 29 in non-leap year
        assertThrows(Exception.class, () -> authService.normalizeAndValidateDob("29", "Feb", "2025"));
    }

    @Test
    void testRateLimiting_whenBlocked_returnsZeroAttempts() {
        when(rateLimiterService.isBlocked("1DM24CS001", "127.0.0.1")).thenReturn(true);

        LoginRequest request = LoginRequest.builder()
                .username("1DM24CS001")
                .day("15")
                .month("Apr")
                .year("2005")
                .build();

        LoginResponse response = authService.authenticate(request, "127.0.0.1", "TestAgent");

        assertFalse(response.isSuccess());
        assertEquals(0, response.getAttemptsRemaining());
        assertTrue(response.getMessage().contains("temporarily locked"));
        verify(rateLimiterService).recordBlockedAttempt("1DM24CS001", "127.0.0.1", "TestAgent");
    }

    @Test
    void testAuthenticate_failedPassword_decrementsAttempts() {
        User user = User.builder()
                .id(1L)
                .username("1DM24CS001")
                .passwordHash("hashed")
                .role(Role.builder().id(4L).name("parent").build())
                .build();

        when(rateLimiterService.isBlocked("1DM24CS001", "127.0.0.1")).thenReturn(false);
        when(userRepository.findByUsername("1DM24CS001")).thenReturn(Optional.of(user));
        when(passwordEncoder.matches("2005-04-15", "hashed")).thenReturn(false);
        when(rateLimiterService.getRemainingAttempts("1DM24CS001", "127.0.0.1")).thenReturn(4);

        LoginRequest request = LoginRequest.builder()
                .username("1DM24CS001")
                .day("15")
                .month("Apr")
                .year("2005")
                .build();

        LoginResponse response = authService.authenticate(request, "127.0.0.1", "TestAgent");

        assertFalse(response.isSuccess());
        assertEquals(4, response.getAttemptsRemaining());
        verify(rateLimiterService).recordFailedAttempt(eq("1DM24CS001"), eq("127.0.0.1"), eq("TestAgent"), eq("Password mismatch"));
    }
}
