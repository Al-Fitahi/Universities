import { Link, usePage } from "@inertiajs/react";
import { useLocation } from "wouter";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAppearance } from "@/hooks/use-appearance";
import { Moon, Sun, Menu, GraduationCap } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

export function Navbar() {
  const { t } = useLanguage();
  const { resolvedAppearance, updateAppearance } = useAppearance();
  const { props } = usePage<{ auth?: { user?: unknown | null } }>();
  const isLoggedIn =
    Boolean(props?.auth?.user) ||
    (typeof window !== "undefined" &&
      localStorage.getItem("isLoggedIn") === "true");
  const authHref = isLoggedIn ? "/dashboard" : "/login";
  // We still use wouter's location for the mock link active state logic inside the component if needed,
  // but let's try to stick to Inertia patterns where possible.
  // In real Inertia, you check window.location or usePage().url
  const [location] = useLocation();

  const toggleTheme = () => {
    updateAppearance(resolvedAppearance === "dark" ? "light" : "dark");
  };

  const navItems = [
    { key: "home", href: "/" },
    { key: "universities", href: "/universities" },
    { key: "colleges", href: "/colleges" },
    { key: "projects", href: "/projects" },
    { key: "guidance", href: "/guidance" },
    { key: "compare", href: "/compare" },
  ];

  return (
    <nav className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 shadow-sm">
      <div className="container mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8 ">
        <div className="flex items-center gap-2">
          <Link
            href="/"
            className="flex items-center gap-2 font-bold text-xl text-primary"
          >
            <GraduationCap className="h-8 w-8 text-secondary" />
            <span>UniGuide</span>
          </Link>
        </div>

        {/* Desktop Nav */}
        <div className="hidden md:flex items-center gap-6">
          {navItems.map((item) => (
            <Link
              key={item.key}
              href={item.href}
              className={`text-sm font-medium transition-colors hover:text-primary ${
                location === item.href
                  ? "text-primary font-bold"
                  : "text-muted-foreground"
              }`}
            >
              {t(item.key)}
            </Link>
          ))}
        </div>

        <div className="flex items-center gap-2">
          <Button
            variant="ghost"
            size="icon"
            onClick={toggleTheme}
            className="rounded-full"
          >
            {resolvedAppearance === "light" ? (
              <Sun className="h-5 w-5" />
            ) : (
              <Moon className="h-5 w-5" />
            )}
          </Button>

          <Link href={authHref}>
            <Button className="hidden md:flex bg-secondary hover:bg-secondary/90 text-secondary-foreground font-bold">
              {isLoggedIn ? t("dashboard") : t("login")}
            </Button>
          </Link>

          {/* Mobile Menu Trigger */}
          <DropdownMenu>
            <DropdownMenuTrigger asChild className="md:hidden">
              <Button variant="ghost" size="icon">
                <Menu className="h-6 w-6" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-56">
              {navItems.map((item) => (
                <DropdownMenuItem key={item.key} asChild>
                  <Link href={item.href} className="w-full font-medium">
                    {t(item.key)}
                  </Link>
                </DropdownMenuItem>
              ))}
              <DropdownMenuItem asChild>
                <Link
                  href={authHref}
                  className="w-full font-bold text-primary"
                >
                  {isLoggedIn ? t("dashboard") : t("login")}
                </Link>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </nav>
  );
}
