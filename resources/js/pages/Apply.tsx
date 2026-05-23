import { Layout } from "@/components/layout/Layout";
import { useLocation } from "wouter";
import { useForm } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useEffect } from "react";

export interface Major {
  id: string;   // هذا الآن university_major.public_id ✅
  name: string;
  gpa: number;
}

export default function Apply({
  majors = [],
  selectedUniversityId = "",
}: {
  majors: Major[];
  selectedUniversityId: string;
}) {
  const [, setLocation] = useLocation();

  // قراءة university_major_id من query string
  const universityMajorId =
    new URLSearchParams(window.location.search).get("major") || "";

  const selectedMajor = majors.find((m) => m.id === universityMajorId);

  useEffect(() => {
    if (localStorage.getItem("isLoggedIn") !== "true") {
      toast.error("يجب تسجيل الدخول أولاً للتقديم");
      setLocation("/login");
    }
  }, [setLocation]);

  const { data, setData, post, processing, clearErrors } = useForm({
    university_major_id: universityMajorId,   // ✅ حقل واحد فقط
    F_name: "",
    S_name: "",
    Th_name: "",
    Su_name: "",
    phone_number: "",
    graduation_date: "",
    graduation_grade: "",
    certificate_image: null as File | null,
  });

  useEffect(() => {
    setData("university_major_id", universityMajorId);
  }, [universityMajorId, setData]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    clearErrors();

    if (!universityMajorId || !selectedMajor) {
      toast.error("هذا التخصص غير متاح حالياً، اختر تخصصاً متاحاً ثم حاول مرة أخرى.");
      return;
    }

    if (
      selectedMajor &&
      parseFloat(data.graduation_grade) < selectedMajor.gpa
    ) {
      toast.error(
        `عذراً، معدلك (${data.graduation_grade}) أقل من المعدل المطلوب لهذا التخصص (${selectedMajor.gpa})`
      );
      return;
    }

    post("/apply", {
      forceFormData: true,
      onSuccess: () => {
        toast.success("تم إرسال الطلب بنجاح!", {
          description: "سنتواصل معك قريباً.",
        });
        setLocation("/colleges");
      },
      onError: (errors) => {
        const firstError = Object.values(errors).find((value) =>
          Array.isArray(value) ? value.length > 0 : Boolean(value),
        );

        const message = Array.isArray(firstError)
          ? firstError[0]
          : firstError || "تعذر إرسال الطلب، يرجى التحقق من البيانات.";

        toast.error(message);
      },
    });
  };

  return (
    <Layout>
      <div className="bg-muted/30 py-12">
        <div className="container mx-auto px-4 max-w-7xl">
          <h1 className="text-4xl font-bold mb-4">التقديم</h1>
          <p className="text-muted-foreground">ابدأ رحلتك الجامعية اليوم.</p>
        </div>
      </div>

      <div className="container mx-auto py-12 px-4 max-w-3xl">
        <Card>
          <CardHeader>
            <CardTitle>نموذج تقديم الطالب</CardTitle>
            {selectedMajor && (
              <p className="text-sm text-primary font-bold mt-2">
                التقديم لتخصص: {selectedMajor.name}
              </p>
            )}
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid md:grid-cols-4 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="F_name">الاسم الأول</Label>
                  <Input
                    id="F_name"
                    required
                    value={data.F_name}
                    onChange={(e) => setData("F_name", e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="S_name">الاسم الثاني</Label>
                  <Input
                    id="S_name"
                    required
                    value={data.S_name}
                    onChange={(e) => setData("S_name", e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="Th_name">الاسم الثالث</Label>
                  <Input
                    id="Th_name"
                    required
                    value={data.Th_name}
                    onChange={(e) => setData("Th_name", e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="Su_name">اللقب / العائلة</Label>
                  <Input
                    id="Su_name"
                    required
                    value={data.Su_name}
                    onChange={(e) => setData("Su_name", e.target.value)}
                  />
                </div>
              </div>

              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="phone_number">رقم الهاتف</Label>
                  <Input
                    id="phone_number"
                    dir="ltr"
                    className="text-right"
                    required
                    value={data.phone_number}
                    onChange={(e) => setData("phone_number", e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="graduation_date">تاريخ التخرج</Label>
                  <Input
                    id="graduation_date"
                    type="date"
                    required
                    value={data.graduation_date}
                    onChange={(e) => setData("graduation_date", e.target.value)}
                  />
                </div>
              </div>

              <div className="grid md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="graduation_grade">معدل التخرج</Label>
                  <Input
                    id="graduation_grade"
                    type="number"
                    step="0.01"
                    dir="ltr"
                    className="text-right"
                    required
                    value={data.graduation_grade}
                    onChange={(e) =>
                      setData("graduation_grade", e.target.value)
                    }
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="certificate_image">صورة الشهادة</Label>
                  <Input
                    id="certificate_image"
                    type="file"
                    required
                    accept="image/*,.pdf"
                    onChange={(e) =>
                      setData("certificate_image", e.target.files?.[0] || null)
                    }
                  />
                </div>
              </div>

              <Button
                type="submit"
                className="w-full font-bold text-lg h-12 mt-8"
                disabled={processing}
              >
                {processing ? "جاري الإرسال..." : "إرسال طلب التقديم"}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </Layout>
  );
}
