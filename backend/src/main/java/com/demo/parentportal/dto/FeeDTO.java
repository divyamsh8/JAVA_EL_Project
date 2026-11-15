package com.demo.parentportal.dto;

import lombok.*;
import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class FeeDTO {
    private BigDecimal totalPayable;
    private BigDecimal totalPaid;
    private BigDecimal totalBalance;
    private String overallStatus;
    private List<FeeItem> records;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    @Builder
    public static class FeeItem {
        private Long id;
        private String academicYear;
        private String feeCategory;
        private BigDecimal totalAmount;
        private BigDecimal paidAmount;
        private BigDecimal balanceAmount;
        private String paymentStatus;
        private String receiptNo;
        private LocalDate paymentDate;
        private String paymentMode;
    }
}
