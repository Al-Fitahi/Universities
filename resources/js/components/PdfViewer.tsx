import { useState, useEffect } from "react";
import { Document, Page, pdfjs } from "react-pdf";
import { Loader2, ZoomIn, ZoomOut } from "lucide-react";
import { Button } from "./ui/button";
import { useLanguage } from "@/contexts/LanguageContext";
import "react-pdf/dist/Page/AnnotationLayer.css";
import "react-pdf/dist/Page/TextLayer.css";

pdfjs.GlobalWorkerOptions.workerSrc = `//unpkg.com/pdfjs-dist@${pdfjs.version}/build/pdf.worker.min.mjs`;


interface PdfViewerProps {
  url: string;
}

export function PdfViewer({ url }: PdfViewerProps) {
  const [numPages, setNumPages] = useState<number>();
  const [scale, setScale] = useState<number>(1.0);
  const [windowWidth, setWindowWidth] = useState(
    typeof window !== "undefined" ? window.innerWidth : 800,
  );
  const { language } = useLanguage();

  useEffect(() => {
    const handleResize = () => setWindowWidth(window.innerWidth);
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, []);

  function onDocumentLoadSuccess({ numPages }: { numPages: number }): void {
    setNumPages(numPages);
  }

  const handleContextMenu = (e: React.MouseEvent) => {
    e.preventDefault();
  };

  return (
    <div
      className="flex flex-col items-center w-full min-h-full select-none"
      onContextMenu={handleContextMenu}
      style={{ userSelect: "none", WebkitUserSelect: "none" }}
    >
      {/* شريط الزوم فقط */}
      <div className="flex items-center gap-2 w-full max-w-3xl mb-4 bg-card p-2 rounded-xl border shadow-sm sticky top-0 z-10 justify-end">
        <Button
          variant="ghost"
          size="icon"
          onClick={() => setScale((prev) => Math.max(prev - 0.2, 0.5))}
          disabled={scale <= 0.5}
          className="h-8 w-8"
          title={language === "ar" ? "تصغير" : "Zoom Out"}
        >
          <ZoomOut className="h-4 w-4" />
        </Button>
        <span className="text-xs font-medium w-12 text-center">
          {Math.round(scale * 100)}%
        </span>
        <Button
          variant="ghost"
          size="icon"
          onClick={() => setScale((prev) => Math.min(prev + 0.2, 2.5))}
          disabled={scale >= 2.5}
          className="h-8 w-8"
          title={language === "ar" ? "تكبير" : "Zoom In"}
        >
          <ZoomIn className="h-4 w-4" />
        </Button>
      </div>

      {/* كل الصفحات */}
      <Document
        file={url}
        onLoadSuccess={onDocumentLoadSuccess}
        loading={
          <div className="flex flex-col items-center justify-center p-20 text-muted-foreground min-h-[400px]">
            <Loader2 className="h-10 w-10 animate-spin mb-4 text-primary" />
            <p className="font-medium animate-pulse">
              {language === "ar" ? "جاري تحميل المستند..." : "Loading document..."}
            </p>
          </div>
        }
        error={
          <div className="p-20 text-destructive text-center flex flex-col items-center min-h-[400px] justify-center bg-destructive/5 rounded-lg">
            <p className="font-bold mb-2">
              {language === "ar" ? "تعذر تحميل المستند" : "Failed to load document"}
            </p>
            <p className="text-sm opacity-80">
              {language === "ar"
                ? "قد يكون الرابط غير صالح أو هناك مشكلة في الاتصال."
                : "The link might be invalid or there is a connection issue."}
            </p>
          </div>
        }
      >
        {numPages &&
          Array.from({ length: numPages }, (_, i) => (
            <div key={i + 1} className="mb-4 border shadow-xl bg-white rounded-md overflow-hidden pointer-events-none">
              <Page
                pageNumber={i + 1}
                renderTextLayer={false}
                renderAnnotationLayer={false}
                className="max-w-full"
                width={Math.min(windowWidth - 64, 900)}
                scale={scale}
                loading={
                  <div className="flex justify-center items-center p-20 min-h-[600px]">
                    <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
                  </div>
                }
              />
            </div>
          ))}
      </Document>
    </div>
  );
}
