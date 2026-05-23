import { useEffect, useRef, useState } from "react";
import { Layout } from "@/components/layout/Layout";
import { Link, router } from "@inertiajs/react";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
  CardFooter,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Search,
  Calendar,
  ArrowRight,
  BookOpen,
  GraduationCap,
  Filter,
  X,
} from "lucide-react";
import { motion } from "framer-motion";
import { useInView } from "react-intersection-observer";
import { PdfViewer } from "@/components/PdfViewer";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";

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
  description: string;
  pdfLink: string;
}

interface ProjectTypeOption {
  id: string;
  name: string;
}

interface ProjectsFilters {
  type?: string;
  search?: string;
}

interface ProjectsPagination {
  currentPage: number;
  hasMorePages: boolean;
  nextPage: number;
  perPage: number;
}

interface ProjectsPageProps {
  graduationProjects: GraduationProject[];
  projectTypes?: ProjectTypeOption[];
  filters?: ProjectsFilters;
  pagination?: ProjectsPagination;
}

interface SearchSuggestion {
  value: string;
  label: string;
  meta: string;
  project: GraduationProject;
}

function ProjectSkeleton() {
  return (
    <Card className="overflow-hidden h-full flex flex-col border-border/50">
      <CardHeader>
        <div className="flex items-center justify-between mb-2">
          <Skeleton className="h-6 w-24 rounded-full" />
          <Skeleton className="h-6 w-20 rounded-full" />
        </div>
        <Skeleton className="h-6 w-4/5 mt-2" />
      </CardHeader>
      <CardContent className="flex-1">
        <div className="space-y-2 mb-4">
          <Skeleton className="h-4 w-full" />
          <Skeleton className="h-4 w-11/12" />
          <Skeleton className="h-4 w-3/4" />
        </div>
        <div className="space-y-2">
          <Skeleton className="h-4 w-2/3" />
          <Skeleton className="h-4 w-1/2" />
        </div>
      </CardContent>
      <CardFooter className="border-t pt-4 flex justify-between items-center">
        <Skeleton className="h-4 w-16" />
        <Skeleton className="h-4 w-24" />
      </CardFooter>
    </Card>
  );
}

export default function Projects({
  graduationProjects = [],
  projectTypes = [],
  filters = {},
  pagination,
}: ProjectsPageProps) {
  // const [likedProjects, setLikedProjects] = useState<Set<string>>(new Set());
  const [pdfProject, setPdfProject] = useState<GraduationProject | null>(null);
  const [items, setItems] = useState<GraduationProject[]>(graduationProjects);
  const [isLoadingMore, setIsLoadingMore] = useState(false);
  const [isSearching, setIsSearching] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [allSuggestions, setAllSuggestions] = useState<SearchSuggestion[]>([]);
  const [searchSuggestions, setSearchSuggestions] = useState<SearchSuggestion[]>([]);
  const [isLoadingSuggestions, setIsLoadingSuggestions] = useState(false);
  const latestProjectsRequestRef = useRef(0);

  // Filter States
  const [searchQuery, setSearchQuery] = useState(filters.search || "");
  const [appliedSearch, setAppliedSearch] = useState(filters.search || "");
  const [selectedUniversity, setSelectedUniversity] = useState("all");
  const [selectedCollege, setSelectedCollege] = useState("all");
  const [selectedMajor, setSelectedMajor] = useState("all");
  const selectedType = filters.type || "all";

  // const toggleLike = (id: string, e: React.MouseEvent) => {
  //   e.stopPropagation();
  //   const newLiked = new Set(likedProjects);
  //   if (newLiked.has(id)) newLiked.delete(id);
  //   else newLiked.add(id);
  //   setLikedProjects(newLiked);
  // };

  // Extract unique options for filters
  const uniqueUniversities = Array.from(
    new Set(
      items.map((p) =>
        p.universityName,
      ),
    ),
  );
  const uniqueColleges = Array.from(
    new Set(
      items.map((p) => p.collegeName),
    ),
  );
  const uniqueMajors = Array.from(
    new Set(
      items.map((p) => p.majorName),
    ),
  );

  useEffect(() => {
    const controller = new AbortController();

    const loadSuggestions = async () => {
      setIsLoadingSuggestions(true);

      try {
        const response = await fetch("/projects/suggestions", {
          headers: {
            Accept: "application/json",
          },
          signal: controller.signal,
        });

        if (!response.ok) {
          throw new Error("Failed to load project suggestions");
        }

        const payload = (await response.json()) as SearchSuggestion[];
        setAllSuggestions(Array.isArray(payload) ? payload : []);
      } catch (error) {
        if ((error as Error).name !== "AbortError") {
          setAllSuggestions([]);
        }
      } finally {
        if (!controller.signal.aborted) {
          setIsLoadingSuggestions(false);
        }
      }
    };

    void loadSuggestions();

    return () => controller.abort();
  }, []);

  useEffect(() => {
    const query = searchQuery.trim().toLowerCase();

    if (query.length === 0) {
      setSearchSuggestions([]);
      return;
    }

    const filteredSuggestions = allSuggestions.filter((suggestion) => {
      const value = suggestion.value.trim().toLowerCase();
      const label = suggestion.label.trim().toLowerCase();
      const meta = suggestion.meta.trim().toLowerCase();

      return value.includes(query) || label.includes(query) || meta.includes(query);
    });

    setSearchSuggestions(filteredSuggestions.slice(0, 8));
  }, [searchQuery, allSuggestions]);

  const clearSearch = () => {
    const requestId = ++latestProjectsRequestRef.current;

    setSearchOpen(false);
    setIsLoadingMore(false);
    setSearchSuggestions([]);
    setIsSearching(true);

    router.get(
      "/projects",
      {
        page: undefined,
        type: selectedType === "all" ? undefined : selectedType,
        per_page: pagination?.perPage,
      },
      {
        preserveScroll: true,
        replace: true,
        showProgress: false,
        only: ["graduationProjects", "projectTypes", "filters", "pagination"],
        onSuccess: (page) => {
          if (requestId !== latestProjectsRequestRef.current) {
            return;
          }

          const incoming = (page.props as { graduationProjects?: GraduationProject[] }).graduationProjects;

          setItems(incoming || []);
          setAppliedSearch("");
        },
        onFinish: () => {
          if (requestId === latestProjectsRequestRef.current) {
            setIsSearching(false);
          }
        },
        onError: () => {
          if (requestId === latestProjectsRequestRef.current) {
            setIsSearching(false);
          }
        },
      },
    );
  };

  const submitSearch = (nextSearch?: string) => {
    const normalizedSearch = (nextSearch ?? searchQuery).trim();

    if (normalizedSearch === "") {
      clearSearch();
      return;
    }

    setAppliedSearch(normalizedSearch);
    setSearchOpen(false);
    setIsLoadingMore(false);
    setIsSearching(true);

    const requestId = ++latestProjectsRequestRef.current;

    router.get(
      "/projects",
      {
        page: undefined,
        type: selectedType === "all" ? undefined : selectedType,
        search: normalizedSearch || undefined,
        per_page: pagination?.perPage,
      },
      {
        preserveScroll: true,
        replace: true,
        showProgress: false,
        only: ["graduationProjects", "projectTypes", "filters", "pagination"],
        onSuccess: (page) => {
          if (requestId !== latestProjectsRequestRef.current) {
            return;
          }

          const incoming = (page.props as { graduationProjects?: GraduationProject[] }).graduationProjects;
          setItems(incoming || []);
        },
        onFinish: () => {
          if (requestId === latestProjectsRequestRef.current) {
            setIsSearching(false);
          }
        },
        onError: () => {
          if (requestId === latestProjectsRequestRef.current) {
            setIsSearching(false);
          }
        },
      },
    );
  };

  const applySuggestion = (suggestion: SearchSuggestion) => {
    setSearchQuery(suggestion.value);
    setAppliedSearch(suggestion.value);
    setSearchOpen(false);
    setItems([suggestion.project]);
    submitSearch(suggestion.value);
  };

  // Apply filters
  const filteredProjects = items.filter((project) => {
    const matchesUniversity =
      selectedUniversity === "all" ||
      project.universityName === selectedUniversity;
    const matchesCollege =
      selectedCollege === "all" ||
      project.collegeName === selectedCollege;
    const matchesMajor =
      selectedMajor === "all" ||
      project.majorName === selectedMajor;
    return (
      matchesUniversity &&
      matchesCollege &&
      matchesMajor
    );
  });

  const currentProjects = filteredProjects;

  const resetFilters = () => {
    setSearchQuery("");
    setAppliedSearch("");
    setSearchOpen(false);
    setSelectedUniversity("all");
    setSelectedCollege("all");
    setSelectedMajor("all");
    setIsLoadingMore(false);

    router.get(
      "/projects",
      {},
      {
        preserveScroll: true,
        replace: true,
        showProgress: false,
        only: ["graduationProjects", "projectTypes", "filters", "pagination"],
        onSuccess: (page) => {
          const incoming = (page.props as { graduationProjects?: GraduationProject[] }).graduationProjects;
          setItems(incoming || []);
        },
      },
    );
  };

  const handleTypeChange = (value: string) => {
    setIsLoadingMore(false);

    router.get(
      "/projects",
      {
        page: undefined,
        type: value === "all" ? undefined : value,
        search: appliedSearch.trim() || undefined,
        per_page: pagination?.perPage,
      },
      {
        preserveScroll: true,
        replace: true,
        showProgress: false,
        only: ["graduationProjects", "projectTypes", "filters", "pagination"],
        onSuccess: (page) => {
          const incoming = (page.props as { graduationProjects?: GraduationProject[] }).graduationProjects;
          setItems(incoming || []);
        },
      },
    );
  };

  function goToNextPage() {
    if (!pagination?.hasMorePages || isLoadingMore) {
      return;
    }

    setIsLoadingMore(true);

    router.get(
      "/projects",
      {
        page: pagination.nextPage,
        type: selectedType === "all" ? undefined : selectedType,
        search: appliedSearch.trim() || undefined,
        per_page: pagination.perPage,
      },
      {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        showProgress: false,
        only: ["graduationProjects", "projectTypes", "filters", "pagination"],
        onSuccess: (page) => {
          const incoming = (page.props as { graduationProjects?: GraduationProject[] }).graduationProjects || [];

          setItems((previous) => {
            const known = new Set(previous.map((project) => project.id));
            const nextChunk = incoming.filter((project) => !known.has(project.id));

            return [...previous, ...nextChunk];
          });
        },
        onFinish: () => {
          setIsLoadingMore(false);
        },
        onError: () => {
          setIsLoadingMore(false);
        },
      },
    );
  }

  const { ref } = useInView({
    threshold: 0,
    rootMargin: "280px 0px",
    onChange: (isInView) => {
      if (isInView && pagination?.hasMorePages && !isLoadingMore) {
        goToNextPage();
      }
    },
  });

  const hasActiveFilters =
    appliedSearch ||
    selectedUniversity !== "all" ||
    selectedCollege !== "all" ||
    selectedMajor !== "all" ||
    selectedType !== "all";

  return (
    <Layout>
      <div className="bg-muted/30 py-12">
        <div className="container mx-auto px-4 ">
          <h1 className="text-4xl font-bold mb-4">مشاريع التخرج</h1>
          <p className="text-muted-foreground">أحدث مشاريع التخرج</p>
        </div>
      </div>

      <div className="container mx-auto py-12 px-4 ">
        {/* Advanced Filters Section */}
        <div className="bg-card border rounded-2xl p-6 shadow-sm mb-10 space-y-6">
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-bold flex items-center gap-2">
              <Filter className="h-5 w-5 text-primary" /> فرز حسب
            </h3>
            {hasActiveFilters && (
              <Button
                variant="ghost"
                size="sm"
                onClick={resetFilters}
                className="text-muted-foreground hover:text-foreground"
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
                placeholder="ابحث..."
                className="pl-9 rtl:pl-3 rtl:pr-9"
                value={searchQuery}
                onChange={(e) => {
                  const nextValue = e.target.value;

                  setSearchQuery(nextValue);
                  setSearchOpen(true);

                  if (nextValue.trim().length === 0 && appliedSearch.trim().length > 0) {
                    clearSearch();
                  }
                }}
                onFocus={() => setSearchOpen(searchQuery.trim().length > 0)}
                onBlur={() => {
                  window.setTimeout(() => setSearchOpen(false), 120);
                }}
                onKeyDown={(event) => {
                  if (event.key === "Enter") {
                    event.preventDefault();
                    submitSearch(event.currentTarget.value);
                  }

                  if (event.key === "Escape") {
                    setSearchOpen(false);
                  }
                }}
              />
              {searchOpen && (searchSuggestions.length > 0 || isLoadingSuggestions) && (
                <div className="absolute left-0 right-0 top-full z-50 mt-2 overflow-hidden rounded-2xl border bg-popover/95 shadow-xl backdrop-blur-md">
                  <div className="flex items-center justify-between border-b px-4 py-2 text-xs text-muted-foreground">
                    <span>اقتراحات البحث</span>
                    <span>Enter للتأكيد</span>
                  </div>
                  <div className="max-h-80 overflow-y-auto p-2">
                    {isLoadingSuggestions && searchSuggestions.length === 0 ? (
                      <div className="px-3 py-4 text-sm text-muted-foreground">
                        جاري البحث...
                      </div>
                    ) : searchSuggestions.length === 0 ? (
                      <div className="px-3 py-4 text-sm text-muted-foreground">
                        لا توجد اقتراحات مطابقة
                      </div>
                    ) : (
                      searchSuggestions.map((suggestion) => {
                        return (
                          <button
                            key={suggestion.value}
                            type="button"
                            onMouseDown={(event) => event.preventDefault()}
                            onClick={() => applySuggestion(suggestion)}
                            className="flex w-full items-start gap-3 rounded-xl px-3 py-3 text-start transition-colors hover:bg-accent/80"
                          >
                            <div className="mt-0.5 rounded-full bg-primary/10 px-2 py-1 text-[10px] font-semibold text-primary">
                              مشروع
                            </div>
                            <div className="min-w-0 flex-1">
                              <div className="truncate text-sm font-medium text-foreground">
                                {suggestion.label}
                              </div>
                              <div className="truncate text-xs text-muted-foreground">
                                {suggestion.meta}
                              </div>
                            </div>
                          </button>
                        );
                      })
                    )}
                  </div>
                </div>
              )}
            </div>

            {/* University Select */}
            <Select
              value={selectedUniversity}
              onValueChange={setSelectedUniversity}
            >
              <SelectTrigger>
                <SelectValue placeholder="كل الجامعات" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">كل الجامعات</SelectItem>
                {uniqueUniversities.map((uni) => (
                  <SelectItem key={uni} value={uni}>
                    {uni}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            {/* College Select */}
            <Select
              value={selectedCollege}
              onValueChange={(val) => {
                setSelectedCollege(val);
                setSelectedMajor("all"); // Reset major when college changes
              }}
            >
              <SelectTrigger>
                <SelectValue placeholder="كل الكليات" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">كل الكليات</SelectItem>
                {uniqueColleges.map((college) => (
                  <SelectItem key={college} value={college}>
                    {college}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            {/* Major Select */}
            <Select value={selectedMajor} onValueChange={setSelectedMajor}>
              <SelectTrigger>
                <SelectValue placeholder="كل التخصصات" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">كل التخصصات</SelectItem>
                {uniqueMajors
                  // Filter majors based on selected college if one is selected
                  .filter((major) => {
                    if (selectedCollege === "all") return true;
                    // Find a project that has this major and the selected college
                    return items.some(
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

            {/* Type Select */}
            <Select value={selectedType} onValueChange={handleTypeChange}>
              <SelectTrigger>
                <SelectValue placeholder="كل الأنواع" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">كل الأنواع</SelectItem>
                {projectTypes.map((type) => (
                  <SelectItem key={type.id} value={type.id}>
                    {type.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {isSearching ? (
            Array.from({ length: 6 }).map((_, index) => (
              <motion.div
                key={`searching-skeleton-${index}`}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.04 }}
              >
                <ProjectSkeleton />
              </motion.div>
            ))
          ) : currentProjects.length > 0 ? (
            currentProjects.map((project, index) => (
              <motion.div
                key={project.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: (index % 12) * 0.06 }}
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
                          <div className="flex items-center gap-4 text-sm text-muted-foreground mt-2">
                            <span className="font-bold text-primary">
                              {project.universityName}
                            </span>
                            <span>{project.date}</span>
                          </div>
                        </DialogHeader>
                        <div className="mt-6 space-y-6">
                          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 bg-muted/30 p-4 rounded-lg border">
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
          {isLoadingMore &&
            pagination?.hasMorePages &&
            Array.from({ length: 3 }).map((_, index) => (
              <ProjectSkeleton key={`project-skeleton-${index}`} />
            ))}
        </div>
        {pagination?.hasMorePages && (
          <div ref={ref} className="h-px w-full" aria-hidden="true" />
        )}
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

