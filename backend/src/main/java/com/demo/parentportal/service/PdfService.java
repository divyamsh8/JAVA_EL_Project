package com.demo.parentportal.service;

import com.demo.parentportal.dto.CieMarksDTO;
import com.demo.parentportal.dto.FeeDTO;
import com.demo.parentportal.dto.ResultsDTO;
import com.demo.parentportal.dto.StudentProfileDTO;
import com.demo.parentportal.entity.Payment;
import com.demo.parentportal.entity.Tenant;
import com.demo.parentportal.repository.PaymentRepository;
import com.lowagie.text.*;
import com.lowagie.text.pdf.PdfPCell;
import com.lowagie.text.pdf.PdfPTable;
import com.lowagie.text.pdf.PdfWriter;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;

import java.awt.Color;
import java.io.ByteArrayOutputStream;
import java.time.format.DateTimeFormatter;

@Service
@RequiredArgsConstructor
@Slf4j
public class PdfService {

    private final ParentPortalService parentPortalService;
    private final TenantService tenantService;
    private final PaymentRepository paymentRepository;

    public byte[] generateReportCardPdf(Long studentId) {
        StudentProfileDTO profile = parentPortalService.getProfile(studentId);
        CieMarksDTO cie = parentPortalService.getCieMarks(studentId, null);
        ResultsDTO results = parentPortalService.getResults(studentId, null);
        Tenant tenant = tenantService.resolveTenant(null);

        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        Document document = new Document(PageSize.A4, 36, 36, 40, 40);

        try {
            PdfWriter.getInstance(document, baos);
            document.open();

            // Colors
            Color primaryColor = new Color(30, 136, 229);
            Color headerBg = new Color(241, 245, 249);
            Color borderColor = new Color(226, 232, 240);

            // Fonts
            Font institutionFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 18, primaryColor);
            Font subtitleFont = FontFactory.getFont(FontFactory.HELVETICA, 10, Color.DARK_GRAY);
            Font titleFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 13, new Color(15, 23, 42));
            Font boldFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 9, Color.BLACK);
            Font textFont = FontFactory.getFont(FontFactory.HELVETICA, 9, new Color(51, 65, 85));
            Font badgeFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 8, new Color(220, 38, 38));

            // Header Banner
            Paragraph header = new Paragraph(tenant.getName().toUpperCase(), institutionFont);
            header.setAlignment(Element.ALIGN_CENTER);
            document.add(header);

            Paragraph sub = new Paragraph("Autonomous Institution Affiliated to VTU | Approved by AICTE, New Delhi\nOFFICIAL STUDENT ACADEMIC PROGRESS REPORT", subtitleFont);
            sub.setAlignment(Element.ALIGN_CENTER);
            sub.setSpacingAfter(15);
            document.add(sub);

            // Student Info Box
            PdfPTable infoTable = new PdfPTable(4);
            infoTable.setWidthPercentage(100);
            infoTable.setWidths(new float[]{20, 30, 20, 30});

            addInfoCell(infoTable, "Student Name:", boldFont);
            addInfoCell(infoTable, profile.getFirstName() + " " + profile.getLastName(), textFont);
            addInfoCell(infoTable, "USN:", boldFont);
            addInfoCell(infoTable, profile.getUsn(), boldFont);

            addInfoCell(infoTable, "Department:", boldFont);
            addInfoCell(infoTable, profile.getDepartmentName(), textFont);
            addInfoCell(infoTable, "Semester:", boldFont);
            addInfoCell(infoTable, "Semester " + profile.getCurrentSem(), textFont);

            addInfoCell(infoTable, "Parent Name:", boldFont);
            addInfoCell(infoTable, profile.getFatherName() != null ? profile.getFatherName() : "N/A", textFont);
            addInfoCell(infoTable, "Academic Term:", boldFont);
            addInfoCell(infoTable, "Even Term 2026", textFont);

            infoTable.setSpacingAfter(15);
            document.add(infoTable);

            // Section: Marks & Grades
            Paragraph sectionTitle = new Paragraph("Course Performance & Continuous Internal Evaluation (CIE)", titleFont);
            sectionTitle.setSpacingAfter(8);
            document.add(sectionTitle);

            PdfPTable table = new PdfPTable(7);
            table.setWidthPercentage(100);
            table.setWidths(new float[]{15, 33, 10, 10, 10, 11, 11});

            // Table Headers
            String[] headers = {"Code", "Subject Title", "Credits", "CIE-1", "CIE-2", "Total CIE", "Status"};
            for (String h : headers) {
                PdfPCell cell = new PdfPCell(new Phrase(h, boldFont));
                cell.setBackgroundColor(headerBg);
                cell.setPadding(6);
                cell.setBorderColor(borderColor);
                cell.setHorizontalAlignment(Element.ALIGN_CENTER);
                table.addCell(cell);
            }

            if (cie != null && cie.getSubjects() != null) {
                for (CieMarksDTO.SubjectCie s : cie.getSubjects()) {
                    addTableCell(table, s.getSubjectCode(), boldFont, Element.ALIGN_LEFT);
                    addTableCell(table, s.getSubjectTitle(), textFont, Element.ALIGN_LEFT);
                    addTableCell(table, s.getCredits() != null ? s.getCredits().toString() : "4.0", textFont, Element.ALIGN_CENTER);
                    addTableCell(table, s.getCie1() != null ? s.getCie1().toString() : "-", textFont, Element.ALIGN_RIGHT);
                    addTableCell(table, s.getCie2() != null ? s.getCie2().toString() : "-", textFont, Element.ALIGN_RIGHT);
                    addTableCell(table, s.getCieTotal() != null ? String.format("%.1f", s.getCieTotal()) : "-", boldFont, Element.ALIGN_RIGHT);

                    boolean isEligible = "Eligible".equalsIgnoreCase(s.getStatus());
                    String status = isEligible ? "ELIGIBLE" : "SHORTAGE";
                    Font sFont = isEligible ? textFont : badgeFont;
                    addTableCell(table, status, sFont, Element.ALIGN_CENTER);
                }
            }

            table.setSpacingAfter(15);
            document.add(table);

            // Performance Summary (SGPA / CGPA)
            if (results != null) {
                PdfPTable gpaTable = new PdfPTable(3);
                gpaTable.setWidthPercentage(100);
                gpaTable.setWidths(new float[]{33, 33, 34});

                addSummaryBox(gpaTable, "Current SGPA", results.getSgpa() != null ? String.format("%.2f", results.getSgpa()) : "8.42", primaryColor);
                addSummaryBox(gpaTable, "Cumulative CGPA", results.getCgpa() != null ? String.format("%.2f", results.getCgpa()) : "8.65", primaryColor);
                addSummaryBox(gpaTable, "Result Status", results.getOverallStatus() != null ? results.getOverallStatus() : "PASS", new Color(16, 185, 129));

                gpaTable.setSpacingAfter(20);
                document.add(gpaTable);
            }

            // Footer / Disclaimer
            Paragraph demoBadge = new Paragraph("DEMO – FICTIONAL DATA ONLY | Powered by Contineo Platform (Demo Migration)", badgeFont);
            demoBadge.setAlignment(Element.ALIGN_CENTER);
            demoBadge.setSpacingBefore(15);
            document.add(demoBadge);

            Paragraph stamp = new Paragraph("This is an electronically generated provisional report card. Does not require physical signature.", subtitleFont);
            stamp.setAlignment(Element.ALIGN_CENTER);
            document.add(stamp);

            document.close();
        } catch (Exception e) {
            log.error("Failed to generate Report Card PDF", e);
            throw new RuntimeException("Error generating PDF: " + e.getMessage());
        }

        return baos.toByteArray();
    }

    public byte[] generateFeeReceiptPdf(Long paymentId) {
        Payment payment = paymentRepository.findById(paymentId)
                .orElseThrow(() -> new IllegalArgumentException("Payment not found: " + paymentId));

        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        Document document = new Document(PageSize.A4, 36, 36, 40, 40);

        try {
            PdfWriter.getInstance(document, baos);
            document.open();

            Color primaryColor = new Color(5, 150, 105); // Emerald
            Color headerBg = new Color(241, 245, 249);
            Color borderColor = new Color(226, 232, 240);

            Font institutionFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 18, primaryColor);
            Font subtitleFont = FontFactory.getFont(FontFactory.HELVETICA, 10, Color.DARK_GRAY);
            Font titleFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 14, new Color(15, 23, 42));
            Font boldFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 9, Color.BLACK);
            Font textFont = FontFactory.getFont(FontFactory.HELVETICA, 9, new Color(51, 65, 85));

            Paragraph header = new Paragraph("DEMO INSTITUTE OF ENGINEERING", institutionFont);
            header.setAlignment(Element.ALIGN_CENTER);
            document.add(header);

            Paragraph sub = new Paragraph("OFFICIAL FEE PAYMENT RECEIPT (SANDBOX)", titleFont);
            sub.setAlignment(Element.ALIGN_CENTER);
            sub.setSpacingAfter(20);
            document.add(sub);

            PdfPTable table = new PdfPTable(2);
            table.setWidthPercentage(100);
            table.setWidths(new float[]{40, 60});

            addInfoCell(table, "Receipt Number:", boldFont);
            addInfoCell(table, payment.getReceiptNo(), boldFont);

            addInfoCell(table, "Transaction Reference:", boldFont);
            addInfoCell(table, payment.getTransactionRef(), textFont);

            addInfoCell(table, "Date & Time:", boldFont);
            String dateStr = (payment.getCreatedAt() != null)
                    ? payment.getCreatedAt().format(DateTimeFormatter.ofPattern("dd-MMM-yyyy hh:mm a"))
                    : "Recent";
            addInfoCell(table, dateStr, textFont);

            addInfoCell(table, "Payment Mode:", boldFont);
            addInfoCell(table, payment.getPaymentMethod() != null ? payment.getPaymentMethod() : "NetBanking", textFont);

            addInfoCell(table, "Payment Status:", boldFont);
            addInfoCell(table, payment.getStatus(), boldFont);

            addInfoCell(table, "Amount Paid:", boldFont);
            addInfoCell(table, "INR " + String.format("%.2f", payment.getAmount()), boldFont);

            table.setSpacingAfter(25);
            document.add(table);

            Paragraph stamp = new Paragraph("Verified & Processed via Sandbox Digital Fee Gateway\nDEMO – FICTIONAL DATA ONLY", subtitleFont);
            stamp.setAlignment(Element.ALIGN_CENTER);
            document.add(stamp);

            document.close();
        } catch (Exception e) {
            log.error("Failed to generate Fee Receipt PDF", e);
            throw new RuntimeException("Error generating receipt PDF: " + e.getMessage());
        }

        return baos.toByteArray();
    }

    private void addInfoCell(PdfPTable table, String text, Font font) {
        PdfPCell cell = new PdfPCell(new Phrase(text, font));
        cell.setPadding(6);
        cell.setBorder(Rectangle.NO_BORDER);
        table.addCell(cell);
    }

    private void addTableCell(PdfPTable table, String text, Font font, int alignment) {
        PdfPCell cell = new PdfPCell(new Phrase(text, font));
        cell.setPadding(6);
        cell.setHorizontalAlignment(alignment);
        cell.setBorderColor(new Color(226, 232, 240));
        table.addCell(cell);
    }

    private void addSummaryBox(PdfPTable table, String label, String value, Color color) {
        PdfPCell cell = new PdfPCell();
        cell.setPadding(8);
        cell.setHorizontalAlignment(Element.ALIGN_CENTER);
        cell.setBorderColor(new Color(226, 232, 240));
        cell.setBackgroundColor(new Color(248, 250, 252));

        Font labelFont = FontFactory.getFont(FontFactory.HELVETICA, 8, Color.GRAY);
        Font valueFont = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 14, color);

        Paragraph p1 = new Paragraph(label, labelFont);
        p1.setAlignment(Element.ALIGN_CENTER);
        Paragraph p2 = new Paragraph(value, valueFont);
        p2.setAlignment(Element.ALIGN_CENTER);

        cell.addElement(p1);
        cell.addElement(p2);
        table.addCell(cell);
    }
}
