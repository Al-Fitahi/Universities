import React, {
  createContext,
  useContext,
  useEffect,
  ReactNode,
} from "react";

type Language = "ar";
type Direction = "rtl";

interface Translations {
  [key: string]: {
    ar: string;
  };
}

const translations: Translations = {
  home: { ar: "الرئيسية" },
  universities: { ar: "الجامعات" },
  colleges: { ar: "الكليات" },
  projects: { ar: "مشاريع التخرج" },
  allColleges: { ar: "جميع الكليات" },
  allMajors: { ar: "جميع التخصصات" },
  allDates: { ar: "كل التواريخ" },
  allTypes: { ar: "جميع الأنواع" },
  allUniversities: { ar: "جميع الجامعات" },
  research: { ar: "بحث" },
  application: { ar: "تطبيق" },
  hardware: { ar: "عتاد" },
  filterBy: { ar: "فرز حسب" },
  resetFilters: { ar: "إعادة ضبط الفرز" },
  guidance: { ar: "التوجيه الذكي" },
  about: { ar: "من نحن" },
  contact: { ar: "اتصل بنا" },
  login: { ar: "تسجيل الدخول" },
  dashboard: { ar: "الذهاب للوحة التحكم" },
  apply: { ar: "قدم الآن" },
  search: { ar: "بحث..." },
  readMore: { ar: "اقرأ المزيد" },
  viewDetails: { ar: "عرض التفاصيل" },
  copyright: { ar: "© 2026 UniGuide. جميع الحقوق محفوظة." },
  exploreUnis: { ar: "استكشف الجامعات" },
  exploreColleges: { ar: "استكشف الكليات" },
  heroTitle: { ar: "اكتشف مستقبلك" },
  heroSubtitle: { ar: "بوابتك لأفضل الجامعات والمسارات الأكاديمية." },
  popularUniversities: { ar: "الجامعات الشائعة" },
  latestProjects: { ar: "أحدث المشاريع" },
  whyChooseUs: { ar: "لماذا تختارنا" },
  compare: { ar: "مقارنة" },
};

interface LanguageContextType {
  language: Language;
  direction: Direction;
  setLanguage: (lang: Language) => void;
  t: (key: string) => string;
}

const LanguageContext = createContext<LanguageContextType | undefined>(
  undefined,
);

export const LanguageProvider = ({ children }: { children: ReactNode }) => {
  const language: Language = "ar";
  const direction: Direction = "rtl";
  const setLanguage: LanguageContextType["setLanguage"] = () => {};

  useEffect(() => {
    document.documentElement.dir = direction;
    document.documentElement.lang = language;
    localStorage.setItem("appLanguage", language);
  }, [direction, language]);

  const t = (key: string) => {
    return translations[key]?.[language] || key;
  };

  return (
    <LanguageContext.Provider
      value={{ language, direction, setLanguage, t }}
    >
      {children}
    </LanguageContext.Provider>
  );
};

export const useLanguage = () => {
  const context = useContext(LanguageContext);
  if (context === undefined) {
    throw new Error("useLanguage must be used within a LanguageProvider");
  }
  return context;
};
