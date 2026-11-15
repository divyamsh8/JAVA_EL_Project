import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

const resources = {
  en: {
    translation: {
      appName: 'Parent Portal',
      contineoDemo: 'Contineo Platform (Demo)',
      demoBadge: 'DEMO – fictional data',
      nav: {
        dashboard: 'Dashboard',
        profile: 'Student Profile',
        attendance: 'Attendance',
        cie: 'Internal Marks (CIE)',
        results: 'Semester Results',
        timetable: 'Timetable',
        calendar: 'Academic Calendar',
        announcements: 'Announcements',
        fees: 'Fee Details & Pay',
        leave: 'Absence / Leave',
        feedback: 'Grievance / Feedback',
        mentors: 'Mentor Consultation',
        logout: 'Logout',
        admin: 'Admin Console'
      },
      dashboard: {
        welcomeBack: 'Welcome back',
        overview: 'Semester Overview & Quick Summary',
        overallAttendance: 'Overall Attendance',
        shortageWarning: 'Attendance Shortage Alert (<75%)',
        sgpa: 'Latest SGPA',
        cgpa: 'Cumulative CGPA',
        feeBalance: 'Pending Fee Balance',
        todayClasses: "Today's Class Schedule",
        recentAnnouncements: 'Recent Announcements',
        viewAll: 'View Details',
        switchWard: 'Switch Ward / Sibling'
      },
      attendance: {
        title: 'Attendance Intelligence',
        subtitle: 'Subject-wise attendance tracking and monthly trends',
        conducted: 'Conducted',
        attended: 'Attended',
        percentage: 'Percentage',
        status: 'Status',
        regular: 'Regular (>=75%)',
        critical: 'Critical Shortage (<75%)',
        monthlyTrend: 'Monthly Attendance Trend (%)',
        classAverage: 'Class Average Comparison'
      },
      cie: {
        title: 'Continuous Internal Evaluation (CIE)',
        formulaNote: 'CIE Calculation Formula: (CIE-1 + CIE-2) / 2 + Assignment-1 + Assignment-2 (Capped at 50)',
        eligibilityCutoff: 'Minimum Eligibility Cutoff: 20 / 50 Marks',
        subject: 'Subject',
        cie1: 'CIE-1 (50)',
        cie2: 'CIE-2 (50)',
        assign1: 'A-1 (10)',
        assign2: 'A-2 (10)',
        totalCie: 'Scaled Total (50)',
        eligibility: 'Eligibility Status',
        eligible: 'Eligible for SEE',
        ineligible: 'Shortage (<20)'
      },
      fees: {
        title: 'Fee Details & Sandbox Online Payment',
        totalPayable: 'Total Payable',
        totalPaid: 'Total Paid',
        balance: 'Balance Due',
        payNow: 'Pay Online (Sandbox)',
        downloadReceipt: 'Download Receipt (PDF)'
      },
      calendar: {
        title: 'Academic Calendar 2026',
        exportIcs: 'Add to Calendar (.ics)',
        exams: 'Examinations',
        holidays: 'Holidays',
        academic: 'Academic Deadlines'
      },
      login: {
        title: 'Institutional Parent Login',
        subtitle: 'Please select term and enter your ward credentials',
        usnLabel: 'University Seat Number (USN)',
        dobLabel: 'Student Date of Birth (DOB)',
        passwordLabel: 'Password',
        termLabel: 'Academic Session',
        loginButton: 'Sign In to Portal',
        forgotPassword: 'Forgot Password / Reset?',
        rateLimitNote: 'Protected by Rate Limiting (5 attempts / 15 mins)'
      }
    }
  },
  kn: {
    translation: {
      appName: 'ಪೋಷಕರ ಪೋರ್ಟಲ್',
      contineoDemo: 'ಕಾಂಟಿನಿಯೊ ಪ್ಲಾಟ್‌ಫಾರ್ಮ್ (ಡೆಮೊ)',
      demoBadge: 'ಡೆಮೊ – ಕಾಲ್ಪನಿಕ ಮಾಹಿತಿ',
      nav: {
        dashboard: 'ಮುಖಪುಟ',
        profile: 'ವಿದ್ಯಾರ್ಥಿ ಪ್ರೊಫೈಲ್',
        attendance: 'ಹಾಜರಾತಿ',
        cie: 'ಆಂತರಿಕ ಅಂಕಗಳು (CIE)',
        results: 'ಸೆಮಿಸ್ಟರ್ ಫಲಿತಾಂಶ',
        timetable: 'ವೇಳಾಪಟ್ಟಿ',
        calendar: 'ಶೈಕ್ಷಣಿಕ ಕ್ಯಾಲೆಂಡರ್',
        announcements: 'ಪ್ರಕಟಣೆಗಳು',
        fees: 'ಶುಲ್ಕ ವಿವರ & ಪಾವತಿ',
        leave: 'ರಜೆ ಅರ್ಜಿ',
        feedback: 'ದೂರು / ಸಲಹೆ',
        mentors: 'ಮಾರ್ಗದರ್ಶಕರ ಭೇಟಿ',
        logout: 'ನಿರ್ಗಮಿಸಿ',
        admin: 'ನಿರ್ವಾಹಕ ಪುಟ'
      },
      dashboard: {
        welcomeBack: 'ಸುಸ್ವಾಗತ',
        overview: 'ಸೆಮಿಸ್ಟರ್ ಅವಲೋಕನ & ತ್ವರಿತ ಸಾರಾಂಶ',
        overallAttendance: 'ಒಟ್ಟು ಹಾಜರಾತಿ',
        shortageWarning: 'ಹಾಜರಾತಿ ಕೊರತೆಯ ಎಚ್ಚರಿಕೆ (<75%)',
        sgpa: 'ಪ್ರಸ್ತುತ SGPA',
        cgpa: 'ಒಟ್ಟು CGPA',
        feeBalance: 'ಬಾಕಿ ಶುಲ್ಕ',
        todayClasses: 'ಇಂದಿನ ತರಗತಿಗಳ ವಿವರ',
        recentAnnouncements: 'ಇತ್ತೀಚಿನ ಪ್ರಕಟಣೆಗಳು',
        viewAll: 'ವಿವರ ನೋಡಿ',
        switchWard: 'ವಿದ್ಯಾರ್ಥಿ ಬದಲಾಯಿಸಿ'
      },
      attendance: {
        title: 'ಹಾಜರಾತಿ ವಿವರ',
        subtitle: 'ವಿಷಯವಾರು ಹಾಜರಾತಿ ಮತ್ತು ಮಾಸಿಕ ಪ್ರವೃತ್ತಿ',
        conducted: 'ನಡೆದ ತರಗತಿ',
        attended: 'ಹಾಜರಾದ ತರಗತಿ',
        percentage: 'ಶೇಕಡಾವಾರು',
        status: 'ಸ್ಥಿತಿ',
        regular: 'ಸಮರ್ಪಕ (>=75%)',
        critical: 'ಗಂಭೀರ ಕೊರತೆ (<75%)',
        monthlyTrend: 'ಮಾಸಿಕ ಹಾಜರಾತಿ ಪ್ರವೃತ್ತಿ (%)',
        classAverage: 'ತರಗತಿಯ ಸರಾಸರಿ ಹೋಲಿಕೆ'
      },
      cie: {
        title: 'ಆಂತರಿಕ ಮೌಲ್ಯಮಾಪನ (CIE)',
        formulaNote: 'CIE ಸೂತ್ರ: (CIE-1 + CIE-2) / 2 + ಅಸೈನ್‌ಮೆಂಟ್-1 + ಅಸೈನ್‌ಮೆಂಟ್-2 (ಗರಿಷ್ಠ 50)',
        eligibilityCutoff: 'ಕನಿಷ್ಠ ಅರ್ಹತಾ ಅಂಕ: 20 / 50',
        subject: 'ವಿಷಯ',
        cie1: 'CIE-1 (50)',
        cie2: 'CIE-2 (50)',
        assign1: 'A-1 (10)',
        assign2: 'A-2 (10)',
        totalCie: 'ಒಟ್ಟು ಅಂಕ (50)',
        eligibility: 'ಅರ್ಹತಾ ಸ್ಥಿತಿ',
        eligible: 'ಪರೀಕ್ಷೆಗೆ ಅರ್ಹರು',
        ineligible: 'ಅನರ್ಹರು (<20)'
      },
      fees: {
        title: 'ಶುಲ್ಕ ವಿವರ ಮತ್ತು ಆನ್‌ಲೈನ್ ಪಾವತಿ',
        totalPayable: 'ಒಟ್ಟು ಶುಲ್ಕ',
        totalPaid: 'ಪಾವತಿಸಿದ ಶುಲ್ಕ',
        balance: 'ಬಾಕಿ ಮೊತ್ತ',
        payNow: 'ಆನ್‌ಲೈನ್ ಪಾವತಿಸಿ',
        downloadReceipt: 'ರಸೀದಿ ಡೌನ್‌ಲೋಡ್ (PDF)'
      },
      calendar: {
        title: 'ಶೈಕ್ಷಣಿಕ ಕ್ಯಾಲೆಂಡರ್ 2026',
        exportIcs: 'ಕ್ಯಾಲೆಂಡರ್‌ಗೆ ಸೇರಿಸಿ (.ics)',
        exams: 'ಪರೀಕ್ಷೆಗಳು',
        holidays: 'ರಜಾದಿನಗಳು',
        academic: 'ಶೈಕ್ಷಣಿಕ ದಿನಾಂಕಗಳು'
      },
      login: {
        title: 'ಸಂಸ್ಥೆಯ ಪೋಷಕರ ಲಾಗಿನ್',
        subtitle: 'ದಯವಿಟ್ಟು ಅವಧಿಯನ್ನು ಆಯ್ಕೆಮಾಡಿ ಮತ್ತು ಲಾಗಿನ್ ಮಾಡಿ',
        usnLabel: 'ವಿಶ್ವವಿದ್ಯಾಲಯ ಸೀಟ್ ಸಂಖ್ಯೆ (USN)',
        dobLabel: 'ವಿದ್ಯಾರ್ಥಿಯ ಜನ್ಮ ದಿನಾಂಕ (DOB)',
        passwordLabel: 'ಗುಪ್ತಪದ (Password)',
        termLabel: 'ಶೈಕ್ಷಣಿಕ ಅವಧಿ',
        loginButton: 'ಲಾಗಿನ್ ಮಾಡಿ',
        forgotPassword: 'ಗುಪ್ತಪದ ಮರೆತಿದ್ದೀರಾ?',
        rateLimitNote: 'ಸುರಕ್ಷಿತ ಲಾಗಿನ್ (5 ಪ್ರಯತ್ನಗಳು / 15 ನಿಮಿಷ)'
      }
    }
  },
  hi: {
    translation: {
      appName: 'अभिभावक पोर्टल',
      contineoDemo: 'कंटीनियो प्लेटफॉर्म (डेमो)',
      demoBadge: 'डेमो – काल्पनिक डेटा',
      nav: {
        dashboard: 'डैशबोर्ड',
        profile: 'छात्र प्रोफ़ाइल',
        attendance: 'उपस्थिति',
        cie: 'आंतरिक अंक (CIE)',
        results: 'परीक्षा परिणाम',
        timetable: 'समय सारणी',
        calendar: 'शैक्षणिक कैलेंडर',
        announcements: 'सूचनाएं',
        fees: 'शुल्क विवरण व भुगतान',
        leave: 'अवकाश आवेदन',
        feedback: 'शिकायत / फीडबैक',
        mentors: 'परामर्शदाता परामर्श',
        logout: 'लॉग आउट',
        admin: 'एडमिन पैनल'
      },
      dashboard: {
        welcomeBack: 'स्वागत है',
        overview: 'सत्र सारांश एवं मुख्य आंकड़े',
        overallAttendance: 'कुल उपस्थिति',
        shortageWarning: 'उपस्थिति की कमी चेतावनी (<75%)',
        sgpa: 'नवीनतम SGPA',
        cgpa: 'कुल CGPA',
        feeBalance: 'बकाया शुल्क',
        todayClasses: 'आज की कक्षाएं',
        recentAnnouncements: 'हाल की घोषणाएं',
        viewAll: 'विवरण देखें',
        switchWard: 'छात्र बदलें'
      },
      attendance: {
        title: 'उपस्थिति विवरण',
        subtitle: 'विषयवार उपस्थिति एवं मासिक रुझान',
        conducted: 'आयोजित कक्षाएं',
        attended: 'उपस्थित कक्षाएं',
        percentage: 'प्रतिशत',
        status: 'स्थिति',
        regular: 'संतोषजनक (>=75%)',
        critical: 'गंभीर कमी (<75%)',
        monthlyTrend: 'मासिक उपस्थिति रुझान (%)',
        classAverage: 'कक्षा औसत तुलना'
      },
      cie: {
        title: 'सतत आंतरिक मूल्यांकन (CIE)',
        formulaNote: 'CIE गणना सूत्र: (CIE-1 + CIE-2) / 2 + असाइनमेंट-1 + असाइनमेंट-2 (अधिकतम 50)',
        eligibilityCutoff: 'न्यूनतम पात्रता अंक: 20 / 50',
        subject: 'विषय',
        cie1: 'CIE-1 (50)',
        cie2: 'CIE-2 (50)',
        assign1: 'A-1 (10)',
        assign2: 'A-2 (10)',
        totalCie: 'कुल अंक (50)',
        eligibility: 'पात्रता स्थिति',
        eligible: 'परीक्षा हेतु पात्र',
        ineligible: 'अपात्र (<20)'
      },
      fees: {
        title: 'शुल्क विवरण एवं ऑनलाइन भुगतान',
        totalPayable: 'कुल देय राशि',
        totalPaid: 'भुगतान की गई राशि',
        balance: 'बकाया राशि',
        payNow: 'ऑनलाइन भुगतान करें',
        downloadReceipt: 'रसीद डाउनलोड (PDF)'
      },
      calendar: {
        title: 'शैक्षणिक कैलेंडर 2026',
        exportIcs: 'कैलेंडर में जोड़ें (.ics)',
        exams: 'परीक्षाएं',
        holidays: 'अवकाश',
        academic: 'शैक्षणिक तिथियां'
      },
      login: {
        title: 'संस्थान अभिभावक लॉगिन',
        subtitle: 'कृपया सत्र चुनें और विवरण दर्ज करें',
        usnLabel: 'विश्वविद्यालय सीट संख्या (USN)',
        dobLabel: 'छात्र जन्म तिथि (DOB)',
        passwordLabel: 'पासवर्ड',
        termLabel: 'शैक्षणिक सत्र',
        loginButton: 'लॉगिन करें',
        forgotPassword: 'पासवर्ड भूल गए?',
        rateLimitNote: 'सुरक्षित लॉगिन (5 प्रयास / 15 मिनट)'
      }
    }
  }
};

i18n
  .use(initReactI18next)
  .init({
    resources,
    lng: localStorage.getItem('portal_lang') || 'en',
    fallbackLng: 'en',
    interpolation: {
      escapeValue: false
    }
  });

export default i18n;
