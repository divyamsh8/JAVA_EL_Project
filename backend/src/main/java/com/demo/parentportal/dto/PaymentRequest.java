package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class PaymentRequest {
    private Long feeId;
    private Long studentId;
    private BigDecimal amount;
    private String mode;
}
