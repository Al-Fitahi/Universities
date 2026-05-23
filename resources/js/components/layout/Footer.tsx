import { useLanguage } from "@/contexts/LanguageContext";
import {
  GraduationCap,
  Facebook,
  Twitter,
  Instagram,
  Linkedin,
} from "lucide-react";
import { Link } from "@inertiajs/react";

export function Footer() {
  const { t } = useLanguage();

  return (
    <footer className="border-t bg-muted/40">
      <div className="container mx-auto py-12 md:py-10 px-4 sm:px-6 lg:px-8 ">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="space-y-4">
            <div className="flex items-center gap-2 font-bold text-xl text-primary">
              <GraduationCap className="h-6 w-6 text-secondary" />
              <span>UniGuide</span>
            </div>
            <p className="text-sm text-muted-foreground">{t("heroSubtitle")}</p>
          </div>

          <div>
            <h3 className="font-bold mb-4">{t("home")}</h3>
            <ul className="space-y-2 text-sm text-muted-foreground">
              <li>
                <Link href="/universities" className="hover:text-primary">
                  {t("universities")}
                </Link>
              </li>
              <li>
                <Link href="/colleges" className="hover:text-primary">
                  {t("colleges")}
                </Link>
              </li>
              <li>
                <Link href="/projects" className="hover:text-primary">
                  {t("projects")}
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="font-bold mb-4">{t("about")}</h3>
            <ul className="space-y-2 text-sm text-muted-foreground">
              <li>
                <Link href="/about" className="hover:text-primary">
                  {t("about")}
                </Link>
              </li>
              <li>
                <Link href="/contact" className="hover:text-primary">
                  {t("contact")}
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="font-bold mb-4">Connect</h3>
            <div className="flex gap-4">
              <a href="#" className="text-muted-foreground hover:text-primary">
                <Facebook className="h-5 w-5" />
              </a>
              <a href="#" className="text-muted-foreground hover:text-primary">
                <Twitter className="h-5 w-5" />
              </a>
              <a href="#" className="text-muted-foreground hover:text-primary">
                <Instagram className="h-5 w-5" />
              </a>
              <a href="#" className="text-muted-foreground hover:text-primary">
                <Linkedin className="h-5 w-5" />
              </a>
            </div>
          </div>
        </div>
        <div className="mt-8 pt-8 border-t text-center text-sm text-muted-foreground">
          {t("copyright")}
        </div>
      </div>
    </footer>
  );
}
