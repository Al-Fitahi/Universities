import { useState } from "react";
import { Layout } from "@/components/layout/Layout";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  CheckCircle2,
  Clock,
  GraduationCap,
  X,
  ArrowLeftRight,
} from "lucide-react";
import { useLocation } from "wouter";

export interface Major {
  id: string;
  name: string;
  description: string;
  years: number;
  gpa: number;
  careerOpportunities?: string[];
}

export default function Compare({ majors = [] }: { majors: Major[] }) {
  const searchParams = new URLSearchParams(window.location.search);

  const [major1Id, setMajor1Id] = useState<string>(
    searchParams.get("m1") || "",
  );
  const [major2Id, setMajor2Id] = useState<string>(
    searchParams.get("m2") || "",
  );

  const major1 = majors.find((m) => m.id === major1Id);
  const major2 = majors.find((m) => m.id === major2Id);

  return (
    <Layout>
      <div className="bg-primary/5 py-12">
        <div className="container mx-auto px-4 text-center max-w-3xl">
          <h1 className="text-4xl font-bold mb-4 flex items-center justify-center gap-3">
            <ArrowLeftRight className="h-8 w-8 text-primary" />
            مقارنة التخصصات
          </h1>
          <p className="text-muted-foreground">
            قارن بين التخصصات المختلفة لاختيار المسار الأكاديمي الأنسب لك بناءً على المتطلبات والفرص الوظيفية.
          </p>
        </div>
      </div>

      <div className="container mx-auto py-12 px-4">
        <div className="grid md:grid-cols-2 gap-8 mb-12">
          {/* Major 1 Selection */}
          <div className="space-y-4">
            <label className="font-bold text-lg">التخصص الأول</label>
            <Select value={major1Id} onValueChange={setMajor1Id}>
              <SelectTrigger className="h-14 text-lg bg-card">
                <SelectValue
                  placeholder="اختر التخصص الأول"
                />
              </SelectTrigger>
              <SelectContent>
                {majors.map((m) => (
                  <SelectItem
                    key={m.id}
                    value={m.id}
                    disabled={m.id === major2Id}
                  >
                    {m.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Major 2 Selection */}
          <div className="space-y-4">
            <label className="font-bold text-lg">التخصص الثاني</label>
            <Select value={major2Id} onValueChange={setMajor2Id}>
              <SelectTrigger className="h-14 text-lg bg-card">
                <SelectValue
                  placeholder="اختر التخصص الثاني"
                />
              </SelectTrigger>
              <SelectContent>
                {majors.map((m) => (
                  <SelectItem
                    key={m.id}
                    value={m.id}
                    disabled={m.id === major1Id}
                  >
                    {m.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        {/* Comparison Grid */}
        {major1 || major2 ? (
          <div className="grid md:grid-cols-2 gap-8">
            {/* Column 1 */}
            <div className="relative">
              {major1 ? (
                <Card className="h-full border-primary/20 shadow-md">
                  <div className="absolute top-4 right-4 rtl:left-4 rtl:right-auto">
                    <Button
                      variant="ghost"
                      size="icon"
                      className="h-8 w-8 rounded-full text-muted-foreground hover:text-destructive"
                      onClick={() => setMajor1Id("")}
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                  <CardHeader className="bg-muted/30 border-b pb-8 pt-8 text-center">
                    <div className="mx-auto w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                      <GraduationCap className="h-8 w-8 text-primary" />
                    </div>
                    <CardTitle className="text-2xl">
                      {major1.name}
                    </CardTitle>
                    <p className="text-sm text-muted-foreground mt-2 px-4">
                      {major1.description}
                    </p>
                  </CardHeader>
                  <CardContent className="p-0">
                    <div className="divide-y">
                      <div className="p-6 flex flex-col items-center justify-center text-center hover:bg-muted/10 transition-colors">
                        <span className="text-muted-foreground text-sm mb-1">
                          سنوات الدراسة
                        </span>
                        <div className="text-3xl font-bold flex items-center gap-2">
                          <Clock className="h-6 w-6 text-primary" />{" "}
                          {major1.years}
                        </div>
                      </div>
                      <div className="p-6 flex flex-col items-center justify-center text-center hover:bg-muted/10 transition-colors">
                        <span className="text-muted-foreground text-sm mb-1">
                          المعدل المطلوب للقبول
                        </span>
                        <div className="text-3xl font-bold text-green-600">
                          {major1.gpa}
                        </div>
                      </div>
                      <div className="p-6">
                        <span className="text-muted-foreground text-sm block mb-4 text-center">
                          الفرص الوظيفية
                        </span>
                        <div className="space-y-3">
                          {major1.careerOpportunities?.map((career, i) => (
                            <div
                              key={i}
                              className="flex items-center gap-3 bg-secondary/10 p-3 rounded-lg"
                            >
                              <CheckCircle2 className="h-5 w-5 text-secondary" />
                              <span className="font-medium text-sm">
                                {career}
                              </span>
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ) : (
                <div className="h-full border-2 border-dashed rounded-xl flex flex-col items-center justify-center p-12 text-center text-muted-foreground bg-muted/10">
                  <div className="w-16 h-16 rounded-full bg-muted flex items-center justify-center mb-4">
                    <span className="text-2xl font-bold opacity-50">1</span>
                  </div>
                  <p>
                    الرجاء اختيار التخصص الأول للمقارنة
                  </p>
                </div>
              )}
            </div>

            {/* Column 2 */}
            <div className="relative">
              {major2 ? (
                <Card className="h-full border-primary/20 shadow-md">
                  <div className="absolute top-4 right-4 rtl:left-4 rtl:right-auto">
                    <Button
                      variant="ghost"
                      size="icon"
                      className="h-8 w-8 rounded-full text-muted-foreground hover:text-destructive"
                      onClick={() => setMajor2Id("")}
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                  <CardHeader className="bg-muted/30 border-b pb-8 pt-8 text-center">
                    <div className="mx-auto w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mb-4">
                      <GraduationCap className="h-8 w-8 text-secondary" />
                    </div>
                    <CardTitle className="text-2xl">
                      {major2.name}
                    </CardTitle>
                    <p className="text-sm text-muted-foreground mt-2 px-4">
                      {major2.description}
                    </p>
                  </CardHeader>
                  <CardContent className="p-0">
                    <div className="divide-y">
                      <div className="p-6 flex flex-col items-center justify-center text-center hover:bg-muted/10 transition-colors">
                        <span className="text-muted-foreground text-sm mb-1">
                          سنوات الدراسة
                        </span>
                        <div className="text-3xl font-bold flex items-center gap-2">
                          <Clock className="h-6 w-6 text-secondary" />{" "}
                          {major2.years}
                        </div>
                      </div>
                      <div className="p-6 flex flex-col items-center justify-center text-center hover:bg-muted/10 transition-colors">
                        <span className="text-muted-foreground text-sm mb-1">
                          المعدل المطلوب للقبول
                        </span>
                        <div className="text-3xl font-bold text-green-600">
                          {major2.gpa}
                        </div>
                      </div>
                      <div className="p-6">
                        <span className="text-muted-foreground text-sm block mb-4 text-center">
                          الفرص الوظيفية
                        </span>
                        <div className="space-y-3">
                          {major2.careerOpportunities?.map((career, i) => (
                            <div
                              key={i}
                              className="flex items-center gap-3 bg-primary/5 p-3 rounded-lg"
                            >
                              <CheckCircle2 className="h-5 w-5 text-primary" />
                              <span className="font-medium text-sm">
                                {career}
                              </span>
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ) : (
                <div className="h-full border-2 border-dashed rounded-xl flex flex-col items-center justify-center p-12 text-center text-muted-foreground bg-muted/10">
                  <div className="w-16 h-16 rounded-full bg-muted flex items-center justify-center mb-4">
                    <span className="text-2xl font-bold opacity-50">2</span>
                  </div>
                  <p>
                    الرجاء اختيار التخصص الثاني للمقارنة
                  </p>
                </div>
              )}
            </div>
          </div>
        ) : (
          <div className="text-center py-24 bg-muted/30 rounded-2xl border border-dashed">
            <ArrowLeftRight className="h-16 w-16 text-muted-foreground/30 mx-auto mb-4" />
            <h3 className="text-2xl font-bold text-muted-foreground">
              جاهز للمقارنة
            </h3>
            <p className="text-muted-foreground mt-2 max-w-md mx-auto">
              اختر تخصصين من القوائم المنسدلة بالأعلى لرؤية المقارنة التفصيلية بينهما.
            </p>
          </div>
        )}
      </div>
    </Layout>
  );
}
