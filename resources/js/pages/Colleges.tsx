import { Layout } from "@/components/layout/Layout";
import { useEffect, useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { router } from "@inertiajs/react";
import { applyUniversityRatingOverrides, subscribeToUniversityRatingUpdates } from "@/lib/universityRatingSync";
import {
  Clock,
  Briefcase,
  GraduationCap,
  MapPin,
  Star,
  Filter,
} from "lucide-react";

export interface Major {
  id: string;
  name: string;
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
  image: string;
  majors?: string[];
  major_offerings?: {
    major_id: string;
    fees: number;
    study_years: number;
    admission_rate: number | null;
  }[];
}

export default function Colleges({ colleges = [], universities = [] }: { colleges: College[]; universities: University[] }) {
  const [collegeUniversities, setCollegeUniversities] = useState<University[]>(() =>
    applyUniversityRatingOverrides(universities),
  );
  const [selectedMajorId, setSelectedMajorId] = useState<string | null>(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [sortBy, setSortBy] = useState("rating");

  useEffect(() => {
    setCollegeUniversities(applyUniversityRatingOverrides(universities));
  }, [universities]);

  useEffect(() => {
    return subscribeToUniversityRatingUpdates((update) => {
      setCollegeUniversities((previous) =>
        previous.map((university) =>
          String(university.id) === String(update.id)
            ? {
                ...university,
                rating: update.rating,
              }
            : university,
        ),
      );
    });
  }, []);

  const selectedMajor = colleges.flatMap((c) => c.majors).find((m) => m.id === selectedMajorId);

  const getSelectedMajorFee = (uni: University): number => {
    if (!selectedMajorId) {
      return 0;
    }

    const offer = (uni.major_offerings ?? []).find((entry) => entry.major_id === selectedMajorId);
    return offer?.fees ?? 0;
  };

  const offeringUniversities = collegeUniversities.filter((u) => {
    if (!selectedMajorId) return false;
    return (u.majors ?? []).includes(selectedMajorId);
  });

  const filteredUnis = offeringUniversities
    .filter((u) =>
      u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      u.location.toLowerCase().includes(searchTerm.toLowerCase()),
    )
    .sort((a, b) => {
      if (sortBy === "rating") {
        return b.rating - a.rating;
      }
      if (sortBy === "fees_low") {
        return getSelectedMajorFee(a) - getSelectedMajorFee(b);
      }
      if (sortBy === "fees_high") {
        return getSelectedMajorFee(b) - getSelectedMajorFee(a);
      }
      return 0;
    });

  return (
    <Layout>
      <div className="bg-muted/30 py-12 px-4 md:px-0">
        <div className="container mx-auto">
          <h1 className="text-3xl md:text-4xl font-bold mb-4">الكليات</h1>
          <p className="text-muted-foreground">استكشف الكليات</p>
        </div>
      </div>

      <div className="container mx-auto py-12 px-4 md:px-0">
        {!selectedMajorId ? (
          <Accordion type="single" collapsible className="w-full space-y-4">
            {colleges.map((college) => (
              <AccordionItem
                key={college.id}
                value={college.id}
                className="border rounded-lg px-4 bg-card shadow-sm"
              >
                <AccordionTrigger className="hover:no-underline py-4">
                  <div className="flex items-center gap-4 text-right rtl:text-right w-full">
                    <div className="h-12 w-12 md:h-16 md:w-16 rounded overflow-hidden bg-muted flex-shrink-0">
                      <img src={college.image} className="w-full h-full object-cover" alt={college.name} />
                    </div>
                    <div className="flex-1">
                      <h3 className="text-lg md:text-xl font-bold">{college.name}</h3>
                      <p className="text-xs md:text-sm text-muted-foreground font-normal">
                        {college.majors.length} تخصصات متاحة
                      </p>
                    </div>
                  </div>
                </AccordionTrigger>
                <AccordionContent className="pt-4 pb-6 border-t mt-2">
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {college.majors.map((major) => (
                      <Card key={major.id} className="p-6 hover:shadow-md transition-all border-border/50">
                        <h4 className="font-bold text-lg mb-2">{major.name}</h4>
                        <p className="text-sm text-muted-foreground mb-4 line-clamp-3">
                          {major.description}
                        </p>

                        <div className="space-y-4 mb-6">
                          <div className="grid grid-cols-2 gap-3">
                            <div className="flex flex-col gap-1 bg-muted/50 p-3 rounded-lg border border-border/50 text-sm font-medium">
                              <div className="flex items-center gap-2 text-muted-foreground text-xs mb-1">
                                <Clock className="h-4 w-4 text-primary" />
                                سنوات الدراسة
                              </div>
                              <span className="font-bold text-lg">{major.years}</span>
                            </div>
                            <div className="flex flex-col gap-1 bg-muted/50 p-3 rounded-lg border border-border/50 text-sm font-medium">
                              <div className="flex items-center gap-2 text-muted-foreground text-xs mb-1">
                                <GraduationCap className="h-4 w-4 text-green-600" />
                                المعدل المطلوب
                              </div>
                              <span className="font-bold text-lg text-green-600">{major.gpa}</span>
                            </div>
                          </div>

                          <div className="space-y-3 pt-2">
                            <div className="flex items-center gap-2 mb-3">
                              <div className="bg-primary/10 p-1.5 rounded-md">
                                <Briefcase className="h-4 w-4 text-primary" />
                              </div>
                              <p className="text-sm font-bold text-foreground">أبرز الفرص الوظيفية</p>
                            </div>
                            <div className="flex flex-wrap gap-2">
                              {(major.careerOpportunities || []).map((job, idx) => (
                                <span
                                  key={idx}
                                  className="bg-muted text-foreground text-xs font-medium px-3 py-1.5 rounded-md border border-border shadow-sm"
                                >
                                  {job}
                                </span>
                              ))}
                            </div>
                          </div>
                        </div>

                        <Button
                          onClick={() => setSelectedMajorId(major.id)}
                          className="w-full bg-secondary text-secondary-foreground font-bold hover:bg-secondary/90"
                        >
                          عرض الجامعات والتقديم
                        </Button>
                      </Card>
                    ))}
                  </div>
                </AccordionContent>
              </AccordionItem>
            ))}
          </Accordion>
        ) : (
          <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <Button variant="ghost" onClick={() => setSelectedMajorId(null)} className="mb-4">
              ← العودة للكليات
            </Button>

            <div className="bg-card p-6 rounded-2xl border border-primary/10 shadow-sm mb-12">
              <h2 className="text-3xl font-bold mb-2 text-primary">{selectedMajor?.name}</h2>
              <p className="text-muted-foreground max-w-3xl mb-4">{selectedMajor?.description}</p>
              <div className="flex flex-wrap gap-4 mt-6">
                <div className="flex flex-col justify-center items-center gap-1 bg-muted px-6 py-3 rounded-xl border shadow-sm min-w-[120px]">
                  <div className="flex items-center gap-1 text-xs text-muted-foreground uppercase tracking-wider font-semibold">
                    <Clock className="h-3.5 w-3.5 text-primary" />
                    <span>مدة الدراسة</span>
                  </div>
                  <div className="text-2xl font-black text-primary">
                    {selectedMajor?.years} <span className="text-sm font-normal text-muted-foreground">سنوات</span>
                  </div>
                </div>
                <div className="flex flex-col justify-center items-center gap-1 bg-muted px-6 py-3 rounded-xl border shadow-sm min-w-[120px]">
                  <div className="flex items-center gap-1 text-xs text-muted-foreground uppercase tracking-wider font-semibold">
                    <GraduationCap className="h-3.5 w-3.5 text-green-600" />
                    <span>المعدل المطلوب</span>
                  </div>
                  <div className="text-2xl font-black text-green-600">
                    {selectedMajor?.gpa} <span className="text-sm font-normal text-muted-foreground">+</span>
                  </div>
                </div>
              </div>
            </div>

            <div className="flex flex-col md:flex-row gap-8">
              <aside className="w-full md:w-64 space-y-6">
                <div className="bg-card p-6 rounded-xl border shadow-sm sticky top-24">
                  <div className="flex items-center gap-2 font-bold mb-4">
                    <Filter className="h-4 w-4" />
                    <span>تصفية</span>
                  </div>
                  <div className="space-y-4">
                    <div className="space-y-2">
                      <label className="text-xs font-medium uppercase text-muted-foreground">بحث</label>
                      <Input
                        placeholder="بحث..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                      />
                    </div>
                    <div className="space-y-2">
                      <label className="text-xs font-medium uppercase text-muted-foreground">ترتيب حسب</label>
                      <Select value={sortBy} onValueChange={setSortBy}>
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="rating">الأعلى تقييماً</SelectItem>
                          <SelectItem value="fees_low">الأقل رسوماً</SelectItem>
                          <SelectItem value="fees_high">الأعلى رسوماً</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                  </div>
                </div>
              </aside>

              <div className="flex-1 space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {filteredUnis.map((uni) => (
                    <Card key={uni.id} className="overflow-hidden hover:shadow-lg transition-all border-border/50 group">
                      <div className="aspect-video relative overflow-hidden">
                        <img src={uni.image} className="w-full h-full object-cover transition-transform group-hover:scale-105" />
                        <div className="absolute top-3 right-3 bg-card/90 backdrop-blur-md px-3 py-1.5 rounded-full text-sm font-bold flex items-center gap-1.5 shadow-sm border border-border text-foreground">
                          <Star className="h-4 w-4 fill-yellow-500 text-yellow-500" />
                          {uni.rating}
                        </div>
                      </div>
                      <CardContent className="p-6">
                        <h3 className="text-xl font-bold mb-1">{uni.name}</h3>
                        <div className="flex items-center gap-1 text-sm text-muted-foreground mb-4">
                          <MapPin className="h-3.3 w-3.3" />
                          {uni.location}
                        </div>
                        <div className="flex justify-between items-center mt-4 pt-4 border-t">
                          <div className="text-primary font-bold">
                            {getSelectedMajorFee(uni) === 0 ? "مجاني" : `${getSelectedMajorFee(uni).toLocaleString()} SAR`}
                          </div>
                          <Button
                            size="sm"
                            onClick={() => router.visit(`/apply/${uni.id}?major=${selectedMajorId}`)}
                            className="bg-secondary text-secondary-foreground font-bold"
                          >
                            التقديم
                          </Button>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
                {filteredUnis.length === 0 && (
                  <div className="text-center py-12 text-muted-foreground border rounded-xl bg-muted/20">
                    لا توجد جامعات متاحة لهذا التخصص حالياً
                  </div>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </Layout>
  );
}

