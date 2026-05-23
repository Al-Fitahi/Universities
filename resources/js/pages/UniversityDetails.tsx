import { useState } from "react";
import { router, Link, usePage } from "@inertiajs/react";
import { Layout } from "@/components/layout/Layout";
import { Button } from "@/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  MapPin,
  Star,
  GraduationCap,
  Clock,
  DollarSign,
  BookOpen,
  ArrowLeft,
  ArrowRight,
  CheckCircle2,
  Search,
  Filter,
  X,
  Briefcase,
  Calendar,
} from "lucide-react";
import NotFound from "./not-found";
import { motion } from "framer-motion";
import { PdfViewer } from "@/components/PdfViewer";
import { Input } from "@/components/ui/input";
import { useInView } from "react-intersection-observer";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { toast } from "sonner";
import { applyUniversityRatingOverrides, publishUniversityRatingUpdate } from "@/lib/universityRatingSync";

export interface Major {
  id: string;
  name: string;
  collegeId: string;
  description: string;
  years: number;
  fees: number;
  gpa: number;
  careerOpportunities?: string[];
}

export interface College {
  id: string;
  name: string;
  image: string;
  majors: Major[];
}

export interface University {
  id: string;
  name: string;
  location: string;
  rating: number;
  ratingCount?: number;
  userRating?: number | null;
  fees: number;
  image: string;
  logo: string;
  description: string;
  mapUrl?: string;
  streetName?: string;
  landmark?: string;
}

export interface GraduationProject {
  id: string;
  name: string;
  universityId: string;
  universityName: string;
  collegeName: string;
  majorName: string;
  supervisorName: string;
  memberNames: string[];
  date: string;
  projectType: string;
  projectname: string;
  description: string;
  pdfLink: string;
}

export default function UniversityDetails({ uni, universityProjects = [], colleges = [] }: { uni: University, universityProjects: GraduationProject[], colleges: College[] }) {
  const syncedUniversity = applyUniversityRatingOverrides([uni])[0] ?? uni;
  const { auth } = usePage().props as { auth?: { user?: { id?: string | number } | null } };
  const isAuthenticated = Boolean(auth?.user);
  const [userRating, setUserRating] = useState(syncedUniversity?.userRating ?? 0);
  const [hoverRating, setHoverRating] = useState(0);
  const [isRated, setIsRated] = useState(Boolean(syncedUniversity?.userRating));
  const [currentUniRating, setCurrentUniRating] = useState<number | null>(syncedUniversity?.rating ?? null);
  const [ratingCount, setRatingCount] = useState(syncedUniversity?.ratingCount ?? 0);
  const [isSubmittingRating, setIsSubmittingRating] = useState(false);
  const [pdfProject, setPdfProject] = useState<GraduationProject | null>(null);

  // Filter States for Projects
  const [searchProject, setSearchProject] = useState("");
  const [selectedCollege, setSelectedCollege] = useState("all");
  const [selectedMajor, setSelectedMajor] = useState("all");
  const [selectedDate, setSelectedDate] = useState("all");
  const [selectedType, setSelectedType] = useState("all");



  const handleRate = (rating: number) => {
    if (!uni || isSubmittingRating || !isAuthenticated) {
      return;
    }

    const isEditing = isRated;
    const previousUserRating = userRating;
    const previousIsRated = isRated;
    const previousRatingCount = ratingCount;
    const previousAverage = currentUniRating ?? uni.rating;

    const nextRatingCount = previousIsRated
      ? previousRatingCount
      : previousRatingCount + 1;

    const nextAverageRaw = previousIsRated
      ? previousRatingCount > 0
        ? ((previousAverage * previousRatingCount) - previousUserRating + rating) /
          previousRatingCount
        : rating
      : nextRatingCount > 0
        ? ((previousAverage * previousRatingCount) + rating) / nextRatingCount
        : rating;

    const nextAverage = Number(nextAverageRaw.toFixed(1));

    setUserRating(rating);
    setIsRated(true);
    setCurrentUniRating(nextAverage);
    setRatingCount(nextRatingCount);
    setIsSubmittingRating(true);

    publishUniversityRatingUpdate({
      id: uni.id,
      rating: nextAverage,
      ratingCount: nextRatingCount,
      userRating: rating,
    });

    router.post(
      `/universities/${uni?.id}/rate`,
      {
        rating: rating,
      },
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          toast.success(isEditing ? "تم تحديث تقييمك بنجاح" : "شكراً لتقييمك!");
        },
        onError: () => {
          setUserRating(previousUserRating);
          setIsRated(previousIsRated);
          setCurrentUniRating(previousAverage);
          setRatingCount(previousRatingCount);

          publishUniversityRatingUpdate({
            id: uni.id,
            rating: previousAverage,
            ratingCount: previousRatingCount,
            userRating: previousUserRating,
          });

          toast.error("تعذر إرسال التقييم. تأكد من تسجيل الدخول ثم حاول مرة أخرى.");
        },
        onFinish: () => {
          setIsSubmittingRating(false);
        },
      },
    );
  };

  // Extract unique filter options from the university's projects
  const uniqueColleges = Array.from(
    new Set(
      universityProjects.map((p) => p.collegeName),
    ),
  );
  const uniqueMajors = Array.from(
    new Set(
      universityProjects.map((p) => p.majorName),
    ),
  );
  const uniqueDates = Array.from(
    new Set(universityProjects.map((p) => p.date.substring(0, 4))),
  ).sort((a, b) => b.localeCompare(a));
  const uniqueProjectTypes = Array.from(
    new Map(
      universityProjects
        .map((p) => ({
          value: (p.projectType || "").trim(),
          label: p.projectType,
        }))
        .filter((type) => type.value.length > 0)
        .map((type) => [type.value, type]),
    ).values(),
  );

  // Projects Pagination State
  const [visibleProjects, setVisibleProjects] = useState(6);
  const projectsPerPage = 6;
  const { ref } = useInView({
    threshold: 0.1,
    onChange: (isInView) => {
      if (isInView) {
        setVisibleProjects((prev) => prev + projectsPerPage);
      }
    },
  });

  // Apply Filters
  const filteredProjects = universityProjects.filter((project) => {
    const matchesSearch =
      project.name
        .toLowerCase()
        .includes(searchProject.toLowerCase()) ||
      project.supervisorName
        .toLowerCase()
        .includes(searchProject.toLowerCase()) ||
      project.memberNames.join(" ")
        .toLowerCase()
        .includes(searchProject.toLowerCase());

    const matchesCollege =
      selectedCollege === "all" ||
      project.collegeName === selectedCollege;
    const matchesMajor =
      selectedMajor === "all" ||
      project.majorName === selectedMajor;
    const matchesDate =
      selectedDate === "all" || project.date.startsWith(selectedDate);
    const matchesType =
      selectedType === "all" || project.projectType === selectedType;

    return (
      matchesSearch &&
      matchesCollege &&
      matchesMajor &&
      matchesDate &&
      matchesType
    );
  });

  const displayedProjects = filteredProjects.slice(0, visibleProjects);
  const hasMoreProjects = visibleProjects < filteredProjects.length;

  const resetFilters = () => {
    setSearchProject("");
    setSelectedCollege("all");
    setSelectedMajor("all");
    setSelectedDate("all");
    setSelectedType("all");
    setVisibleProjects(projectsPerPage);
  };

  const hasActiveFilters =
    searchProject ||
    selectedCollege !== "all" ||
    selectedMajor !== "all" ||
    selectedDate !== "all" ||
    selectedType !== "all";

  const handleBackToUniversities = () => {
    if (typeof window !== "undefined" && window.history.length > 1) {
      window.history.back();
      return;
    }

    router.visit("/universities", {
      preserveScroll: true,
    });
  };

  if (!uni) return <NotFound />;

  const uniColleges = colleges;
  const displayedUniRating = currentUniRating ?? uni.rating;

  return (
    <Layout>
      {/* Hero Header */}
      <div className="relative h-[300px] md:h-[400px] bg-muted overflow-hidden">
        <img
          src={uni.image}
          alt={uni.name}
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-background via-background/60 to-transparent" />
        <div className="absolute bottom-0 left-0 right-0 container mx-auto pb-6 md:pb-8 px-4 md:px-0">
          <div className="flex flex-col sm:flex-row items-center sm:items-end text-center sm:text-start gap-4 sm:gap-6 mb-4 sm:mb-6">
            <div className="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-background overflow-hidden bg-white shadow-xl flex-shrink-0 relative z-10 -mt-12 sm:mt-0">
              <img
                src={uni.logo}
                alt="Logo"
                className="w-full h-full object-contain p-2"
              />
            </div>
            <div className="flex-1 pb-0 sm:pb-2">
              <Button
                variant="ghost"
                size="sm"
                onClick={handleBackToUniversities}
                className="mb-2 sm:mb-4 text-white hover:text-white hover:bg-white/20"
              >
                <ArrowLeft className="h-4 w-4 mr-2 rtl:ml-2 rtl:mr-0" />{" "}
                العودة للقائمة
              </Button>
              <h1 className="text-2xl sm:text-3xl md:text-5xl font-bold text-white mb-2 shadow-sm drop-shadow-md">
                {uni.name}
              </h1>
            </div>
          </div>
          <div className="flex flex-col md:flex-row justify-between items-center sm:items-end gap-6 sm:gap-4">
            <div className="flex flex-col sm:flex-row items-center gap-4 text-white/90">
              <div className="flex items-center gap-1.5 text-sm md:text-base font-medium bg-black/20 px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                <MapPin className="h-4 w-4" />
                <span>{uni.location}</span>
              </div>
              <div className="flex items-center w-full">
                <div className="w-full">
                  <div className="flex flex-col sm:flex-row items-center sm:justify-start gap-3 bg-white/10 backdrop-blur-md rounded-2xl px-5 py-3 border border-white/20 shadow-xl group/rate transition-all hover:bg-white/20">
                    <div className="flex items-center gap-2">
                      <div className="bg-yellow-500/20 p-2 rounded-full flex items-center justify-center">
                        <Star className="h-4 w-4 md:h-5 md:w-5 fill-yellow-500 text-yellow-500" />
                      </div>
                      <div className="flex flex-col text-white">
                        <span className="text-xs font-medium text-white/80 leading-none mb-1">
                          التقييم العام
                        </span>
                        <span className="text-xl md:text-2xl font-black leading-none flex items-center gap-2 text-yellow-400">
                          {displayedUniRating}{" "}
                          <span className="text-sm font-medium text-white/70">
                            / 5.0
                          </span>
                        </span>
                      </div>
                    </div>

                    <div className="w-full sm:w-px h-px sm:h-10 bg-white/20 my-2 sm:my-0 sm:mx-2 hidden sm:block"></div>

                    <div className="flex flex-col items-center sm:items-start gap-1.5">
                      <div className="flex items-center gap-1">
                        {[1, 2, 3, 4, 5].map((star) => (
                          <button
                            key={star}
                            onClick={() => handleRate(star)}
                            onMouseEnter={() => setHoverRating(star)}
                            onMouseLeave={() => setHoverRating(0)}
                            disabled={isSubmittingRating || !isAuthenticated}
                            className="transition-all duration-300 transform hover:scale-125 focus:outline-none cursor-pointer active:scale-95"
                            aria-label={
                              `قيم ${star} نجوم`
                            }
                          >
                            <Star
                              className={`h-6 w-6 md:h-7 md:w-7 transition-all duration-300 ${
                                star <= (hoverRating || userRating)
                                  ? "fill-yellow-400 text-yellow-400 drop-shadow-[0_0_8px_rgba(250,204,21,0.5)] scale-110"
                                  : "text-white/30 hover:text-white/60"
                              }`}
                            />
                          </button>
                        ))}
                      </div>
                      <div className="h-4 flex items-center justify-center sm:justify-start w-full">
                        {isSubmittingRating ? (
                          <span className="text-xs font-bold text-white/80 tracking-tight">
                            جاري حفظ التقييم...
                          </span>
                        ) : !isAuthenticated ? (
                          <Link href="/login" className="text-xs font-bold text-white/80 tracking-tight underline underline-offset-4">
                            سجل دخولك للتقييم
                          </Link>
                        ) : isRated ? (
                          <div className="flex items-center gap-1.5 text-green-400 animate-in slide-in-from-bottom-2 duration-500">
                            <CheckCircle2 className="h-3 w-3" />
                            <span className="text-xs font-bold tracking-tight">
                              تم التقييم بنجاح
                            </span>
                          </div>
                        ) : (
                          <span className="text-xs font-bold text-white/80 tracking-tight">
                            اضغط للتقييم
                          </span>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="container mx-auto py-8 px-4 sm:px-6 lg:px-8 ">
        <Tabs defaultValue="overview" className="space-y-8">
          <TabsList className="w-full justify-start border-b rounded-none h-auto p-0 bg-transparent flex overflow-x-auto scrollbar-hide">
            <TabsTrigger
              value="overview"
              className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent px-4 md:px-6 py-3 text-sm md:text-base whitespace-nowrap"
            >
              نظرة عامة
            </TabsTrigger>
            <TabsTrigger
              value="colleges"
              className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent px-4 md:px-6 py-3 text-sm md:text-base whitespace-nowrap"
            >
              الكليات والتخصصات
            </TabsTrigger>
            <TabsTrigger
              value="projects"
              className="rounded-none border-b-2 border-transparent data-[state=active]:border-primary data-[state=active]:bg-transparent px-4 md:px-6 py-3 text-sm md:text-base whitespace-nowrap"
            >
              مشاريع التخرج
            </TabsTrigger>
          </TabsList>

          <TabsContent value="overview" className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="md:col-span-2 space-y-6">
                <section>
                  <h2 className="text-2xl font-bold mb-4">
                    عن الجامعة
                  </h2>
                  <p className="text-base md:text-lg text-muted-foreground leading-relaxed">
                    {uni.description}
                  </p>
                </section>

                <section>
                  <h2 className="text-2xl font-bold mb-4">
                    معرض الصور
                  </h2>
                  <div className="relative overflow-hidden group/gallery">
                    <motion.div
                      className="flex gap-4 cursor-grab active:cursor-grabbing"
                      drag="x"
                      dragConstraints={{ right: 0, left: -400 }}
                      animate={{
                        x: [0, -200, 0],
                      }}
                      transition={{
                        duration: 20,
                        repeat: Infinity,
                        ease: "linear",
                        repeatType: "reverse",
                      }}
                    >
                      <div className="min-w-[300px] md:min-w-[400px] aspect-video bg-muted rounded-lg overflow-hidden border">
                        <img
                          src={uni.image}
                          className="w-full h-full object-cover hover:scale-105 transition-transform"
                        />
                      </div>
                      <div className="min-w-[300px] md:min-w-[400px] aspect-video bg-muted rounded-lg overflow-hidden border">
                        <img
                          src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&auto=format&fit=crop&q=60"
                          className="w-full h-full object-cover hover:scale-105 transition-transform"
                        />
                      </div>
                      <div className="min-w-[300px] md:min-w-[400px] aspect-video bg-muted rounded-lg overflow-hidden border">
                        <img
                          src="https://images.unsplash.com/photo-1541339907198-e08756ebafe3?w=800&auto=format&fit=crop&q=60"
                          className="w-full h-full object-cover hover:scale-105 transition-transform"
                        />
                      </div>
                    </motion.div>
                    <div className="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-background to-transparent pointer-events-none z-10" />
                    <div className="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-background to-transparent pointer-events-none z-10" />
                  </div>
                </section>
              </div>

              <div className="space-y-6">
                <Card className="border-primary/10">
                  <CardContent className="pt-6 space-y-4">
                    <h3 className="font-bold text-lg border-b pb-2">
                      حقائق سريعة
                    </h3>
                    <div className="space-y-3">
                      <div className="flex justify-between items-center py-2 border-b border-dashed">
                        <span className="text-muted-foreground flex items-center gap-2 text-sm">
                          <DollarSign className="h-4 w-4" />{" "}
                          الرسوم
                        </span>
                        <span className="font-medium text-sm">
                          {uni.fees === 0
                            ? "مجاني"
                            : `${uni.fees} SAR`}
                        </span>
                      </div>
                      <div className="flex justify-between items-center py-2 border-b border-dashed">
                        <span className="text-muted-foreground flex items-center gap-2 text-sm">
                          <GraduationCap className="h-4 w-4" />{" "}
                          تأسست
                        </span>
                        <span className="font-medium text-sm">1957</span>
                      </div>
                      <div className="flex justify-between items-center py-2 border-b border-dashed">
                        <span className="text-muted-foreground flex items-center gap-2 text-sm">
                          <BookOpen className="h-4 w-4" />{" "}
                          البرامج
                        </span>
                        <span className="font-medium text-sm">120+</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <Card className="border-primary/10">
                  <CardContent className="pt-6 space-y-4">
                    <h3 className="font-bold text-lg border-b pb-2">
                      معلومات الموقع
                    </h3>
                    <div className="space-y-4">
                      <div>
                        <span className="text-xs text-muted-foreground block mb-1">
                          المدينة
                        </span>
                        <span className="font-medium text-sm">
                          {uni.location}
                        </span>
                      </div>
                      {uni.streetName && (
                        <div>
                          <span className="text-xs text-muted-foreground block mb-1">
                            الشارع
                          </span>
                          <span className="font-medium text-sm">
                            {uni.streetName}
                          </span>
                        </div>
                      )}
                      {uni.landmark && (
                        <div>
                          <span className="text-xs text-muted-foreground block mb-1">
                            أقرب معلم
                          </span>
                          <span className="font-medium text-sm">
                            {uni.landmark}
                          </span>
                        </div>
                      )}
                      {uni.mapUrl && (
                        <Button
                          variant="outline"
                          className="w-full gap-2 mt-2"
                          onClick={() => window.open(uni.mapUrl, "_blank")}
                        >
                          <MapPin className="h-4 w-4" />
                          عرض على الخريطة
                        </Button>
                      )}
                    </div>
                  </CardContent>
                </Card>
              </div>
            </div>
          </TabsContent>

          <TabsContent value="colleges">
            <div className="space-y-8">
              {uniColleges.map((college) => (
                <Card
                  key={college.id}
                  className="overflow-hidden border-primary/10"
                >
                  <div className="bg-muted/30 p-4 border-b flex items-center gap-4">
                    <div className="h-10 w-10 md:h-12 md:w-12 rounded bg-white overflow-hidden border">
                      <img
                        src={college.image}
                        className="h-full w-full object-cover"
                      />
                    </div>
                    <h3 className="text-lg md:text-xl font-bold">
                      {college.name}
                    </h3>
                  </div>
                  <div className="p-4 md:p-6 bg-muted/10">
                    <div className="grid gap-6">
                      {college.majors.map((major) => (
                        <div
                          key={major.id}
                          className="bg-card rounded-xl p-5 border shadow-sm hover:shadow-md transition-all"
                        >
                          <div className="flex flex-col md:flex-row justify-between md:items-start gap-4 mb-4">
                            <div>
                              <h4 className="text-lg font-bold text-primary mb-1">
                                {major.name}
                              </h4>
                              <p className="text-sm text-muted-foreground">
                                {major.description}
                              </p>
                            </div>
                            <Link href={`/apply/${uni.id}?major=${major.id}`}>
                              <Button
                                size="sm"
                                className="bg-secondary text-secondary-foreground hover:bg-secondary/90 font-bold w-full md:w-auto"
                              >
                                التقديم
                              </Button>
                            </Link>
                          </div>

                          <div className="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                            <div className="bg-muted/50 p-3 rounded-lg border border-border/50 flex flex-col justify-center items-center text-center">
                              <Clock className="h-4 w-4 text-primary mb-1" />
                              <span className="text-xs text-muted-foreground mb-1">
                                سنوات الدراسة
                              </span>
                              <span className="font-bold">
                                {major.years}{" "}
                                <span className="text-xs font-normal">
                                  سنوات
                                </span>
                              </span>
                            </div>
                            <div className="bg-muted/50 p-3 rounded-lg border border-border/50 flex flex-col justify-center items-center text-center">
                              <GraduationCap className="h-4 w-4 text-green-600 mb-1" />
                              <span className="text-xs text-muted-foreground mb-1">
                                المعدل المطلوب
                              </span>
                              <span className="font-bold text-green-600">
                                {major.gpa}
                              </span>
                            </div>
                            <div className="bg-muted/50 p-3 rounded-lg border border-border/50 flex flex-col justify-center items-center text-center col-span-2 md:col-span-1">
                              <DollarSign className="h-4 w-4 text-muted-foreground mb-1" />
                              <span className="text-xs text-muted-foreground mb-1">
                                الرسوم السنوية
                              </span>
                              <span className="font-bold">
                                {major.fees === 0
                                  ? "مجاني"
                                  : `${major.fees.toLocaleString()} SAR`}
                              </span>
                            </div>
                          </div>

                          {(major.careerOpportunities || []).length > 0 && (
                            <div className="pt-4 border-t">
                              <p className="text-sm font-bold text-foreground flex items-center gap-2 mb-3">
                                <span className="bg-primary/10 p-1 rounded-md inline-block">
                                  <Briefcase className="h-3.5 w-3.5 text-primary" />
                                </span>
                                الفرص الوظيفية
                              </p>
                              <div className="flex flex-wrap gap-2">
                                {major.careerOpportunities?.map((job, idx) => (
                                  <span
                                    key={idx}
                                    className="bg-muted text-foreground text-xs font-medium px-2.5 py-1 rounded-md border border-border shadow-sm"
                                  >
                                    {job}
                                  </span>
                                ))}
                              </div>
                            </div>
                          )}
                        </div>
                      ))}
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="projects">
            <div className="space-y-6" dir="rtl">
              {/* Filters Section */}
              <div className="bg-card border rounded-lg p-4 shadow-sm space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="font-bold flex items-center gap-2">
                    <Filter className="h-4 w-4" /> فرز حسب
                  </h3>
                  {hasActiveFilters && (
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={resetFilters}
                      className="h-8 text-muted-foreground hover:text-foreground"
                    >
                      <X className="h-4 w-4 mr-1 rtl:ml-1 rtl:mr-0" />{" "}
                      إعادة ضبط الفرز
                    </Button>
                  )}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                  {/* Search Input */}
                  <div className="relative col-span-1 lg:col-span-1">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground rtl:left-auto rtl:right-3" />
                    <Input
                      placeholder="بحث..."
                      className="pl-9 rtl:pl-3 rtl:pr-9"
                      value={searchProject}
                      onChange={(e) => {
                        setSearchProject(e.target.value);
                        setVisibleProjects(projectsPerPage);
                      }}
                    />
                  </div>

                  {/* College Select */}
                  <Select
                    value={selectedCollege}
                    onValueChange={(val) => {
                      setSelectedCollege(val);
                      setSelectedMajor("all");
                      setVisibleProjects(projectsPerPage);
                    }}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="جميع الكليات" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">جميع الكليات</SelectItem>
                      {uniqueColleges.map((college) => (
                        <SelectItem key={college} value={college}>
                          {college}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>

                  {/* Major Select */}
                  <Select
                    value={selectedMajor}
                    onValueChange={(val) => {
                      setSelectedMajor(val);
                      setVisibleProjects(projectsPerPage);
                    }}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="جميع التخصصات" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">جميع التخصصات</SelectItem>
                      {uniqueMajors
                        .filter((major) => {
                          if (selectedCollege === "all") return true;
                          return universityProjects.some(
                            (p) =>
                              p.majorName === major &&
                              p.collegeName === selectedCollege,
                          );
                        })
                        .map((major) => (
                          <SelectItem key={major} value={major}>
                            {major}
                          </SelectItem>
                        ))}
                    </SelectContent>
                  </Select>

                  {/* Date Select */}
                  <Select
                    value={selectedDate}
                    onValueChange={(val) => {
                      setSelectedDate(val);
                      setVisibleProjects(projectsPerPage);
                    }}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="كل التواريخ" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">كل التواريخ</SelectItem>
                      {uniqueDates.map((date) => (
                        <SelectItem key={date} value={date}>
                          {date}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>

                  {/* Type Select */}
                  <Select
                    value={selectedType}
                    onValueChange={(val) => {
                      setSelectedType(val);
                      setVisibleProjects(projectsPerPage);
                    }}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="جميع الأنواع" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">جميع الأنواع</SelectItem>
                      {uniqueProjectTypes.map((type) => (
                        <SelectItem key={type.value} value={type.value}>
                          {type.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              {/* Projects Grid */}
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {displayedProjects.length > 0 ? (
                  displayedProjects.map((project, index) => (
                    <motion.div
                      key={project.id}
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: (index % projectsPerPage) * 0.1 }}
                    >
                      <Card className="overflow-hidden hover:shadow-lg transition-all h-full flex flex-col group border-border/50">
                        <CardHeader>
                          <div className="flex items-center justify-between mb-2">
                            <Link
                              href={`/universities/${project.universityId}`}
                              className="bg-primary/10 text-primary text-xs px-2 py-1 rounded-full font-bold hover:bg-primary/20 transition-colors"
                            >
                              {project.universityName}
                            </Link>
                            <span className="bg-secondary/10 text-secondary text-xs px-2 py-1 rounded-full font-bold">
                              {project.projectType}
                            </span>
                          </div>
                          <CardTitle className="text-xl leading-tight group-hover:text-primary transition-colors mt-2">
                            {project.name}
                          </CardTitle>
                        </CardHeader>
                        <CardContent className="flex-1">
                          <p className="text-sm text-muted-foreground line-clamp-3 mb-4">
                            {project.description}
                          </p>
                          <div className="text-sm text-muted-foreground flex flex-col gap-2">
                            <span className="flex items-center gap-2">
                              <BookOpen className="h-4 w-4" />{" "}
                              {project.collegeName}
                            </span>
                            <span className="flex items-center gap-2">
                              <GraduationCap className="h-4 w-4" />{" "}
                              {project.majorName}
                            </span>
                          </div>
                        </CardContent>
                        <CardFooter className="border-t pt-4 text-xs text-muted-foreground flex justify-between items-center">
                          <div className="flex items-center gap-4">
                            <div className="flex items-center gap-1">
                              <Calendar className="h-3 w-3" />
                              {project.date}
                            </div>
                          </div>
                          <Dialog>
                            <DialogTrigger asChild>
                              <Button
                                variant="link"
                                size="sm"
                                className="p-0 h-auto font-bold text-primary"
                              >
                                عرض التفاصيل{" "}
                                <ArrowRight className="ml-1 h-3 w-3 rtl:rotate-180 rtl:ml-0 rtl:mr-1" />
                              </Button>
                            </DialogTrigger>
                            <DialogContent className="max-w-3xl">
                              <DialogHeader>
                                <div className="flex items-center gap-2 text-xs text-primary font-bold bg-primary/10 w-fit px-2 py-1 rounded-full mb-2">
                                  {project.projectType}
                                </div>
                                <DialogTitle className="text-2xl font-bold">
                                  {project.name}
                                </DialogTitle>
                              </DialogHeader>
                              <div className="mt-6 space-y-2">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 bg-muted/30 p-3 rounded-lg border">
                                  <div>
                                    <span className="text-sm text-muted-foreground block mb-1">
                                      الكلية
                                    </span>
                                    <span className="font-medium">
                                      {project.collegeName}
                                    </span>
                                  </div>
                                  <div>
                                    <span className="text-sm text-muted-foreground block mb-1">
                                      التخصص
                                    </span>
                                    <span className="font-medium">
                                      {project.majorName}
                                    </span>
                                  </div>
                                  <div>
                                    <span className="text-sm text-muted-foreground block mb-1">
                                      المشرف
                                    </span>
                                    <span className="font-medium">
                                      {project.supervisorName}
                                    </span>
                                  </div>
                                  <div>
                                    <span className="text-sm text-muted-foreground block mb-1">
                                      أعضاء الفريق
                                    </span>
                                    <div className="flex flex-wrap gap-1">
                                      {project.memberNames.map((member, i) => (
                                        <span
                                          key={i}
                                          className="bg-background border px-2 py-0.5 rounded text-sm"
                                        >
                                          {member}
                                        </span>
                                      ))}
                                    </div>
                                  </div>
                                </div>

                                <div>
                                  <h4 className="font-bold text-lg mb-2">
                                    وصف المشروع
                                  </h4>
                                  <p className="text-muted-foreground leading-relaxed">
                                    {project.description}
                                  </p>
                                </div>

                                <div className="pt-4 flex flex-col sm:flex-row gap-4 justify-between items-center border-t">
                                  <Link
                                    href={`/universities/${project.universityId}`}
                                  >
                                    <Button
                                      variant="outline"
                                      className="gap-2 w-full sm:w-auto"
                                    >
                                      <GraduationCap className="h-4 w-4" />
                                      صفحة الجامعة
                                    </Button>
                                  </Link>
                                  <Button
                                    variant="default"
                                    className="gap-2 w-full sm:w-auto"
                                    onClick={() => setPdfProject(project)}
                                  >
                                    <span className="flex items-center gap-2">
                                      <BookOpen className="h-4 w-4" />
                                      عرض وثيقة المشروع
                                    </span>
                                  </Button>
                                </div>
                              </div>
                            </DialogContent>
                          </Dialog>
                        </CardFooter>
                      </Card>
                    </motion.div>
                  ))
                ) : (
                  <div className="col-span-full text-center py-16 px-4 bg-muted/20 border border-dashed rounded-2xl">
                    <GraduationCap className="h-12 w-12 text-muted-foreground/50 mx-auto mb-4" />
                    <h3 className="text-xl font-bold mb-2">
                      لا توجد نتائج
                    </h3>
                    <p className="text-muted-foreground mb-4">
                      لم نتمكن من العثور على أي مشاريع تطابق خيارات البحث الحالية.
                    </p>
                    <Button variant="outline" onClick={resetFilters}>
                      إعادة ضبط الفرز
                    </Button>
                  </div>
                )}
              </div>

              {hasMoreProjects && (
                <div
                  ref={ref}
                  className="flex justify-center items-center py-8"
                >
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                </div>
              )}
            </div>
          </TabsContent>
        </Tabs>
      </div>

      {/* Full-screen PDF Viewer Dialog */}
      <Dialog
        open={!!pdfProject}
        onOpenChange={(open) => !open && setPdfProject(null)}
      >
        <DialogContent className="max-w-5xl w-[95vw] h-[90vh] flex flex-col p-0 overflow-hidden sm:rounded-2xl">
          <DialogHeader className="p-4 border-b bg-card shadow-sm z-20 flex-shrink-0">
            <DialogTitle className="text-lg md:text-xl font-bold px-2">
              {pdfProject?.name}
            </DialogTitle>
          </DialogHeader>
          <div className="flex-1 overflow-y-auto bg-muted/30 p-2 sm:p-6">
            {pdfProject && <PdfViewer url={pdfProject.pdfLink} />}
          </div>
        </DialogContent>
      </Dialog>
    </Layout>
  );
}

