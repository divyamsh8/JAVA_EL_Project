package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class PaymentDTO {
    private Long id;
    private Long feeId;
    private Long studentId;
    private String transactionRef;
    private BigDecimal amount;
    private String paymentMethod;
    private String status;
    private String receiptNo;
    private LocalDateTime createdAt;
}
