import { Layout } from "@/components/layout/Layout";
import { Card, CardContent } from "@/components/ui/card";
import { GraduationCap, Users, Globe, Target } from "lucide-react";
import { motion } from "framer-motion";

export default function About() {
  return (
    <Layout>
      <div className="bg-muted/30 py-16 md:py-24">
        <div className="container mx-auto px-4 max-w-4xl text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
          >
            <GraduationCap className="h-16 w-16 mx-auto mb-6 text-primary" />
            <h1 className="text-4xl md:text-5xl font-bold mb-6">من نحن</h1>
            <p className="text-xl text-muted-foreground leading-relaxed">
              منصتك الشاملة لاستكشاف الجامعات، ومشاريع التخرج، ومساراتك الأكاديمية نحو مستقبل مشرق.
            </p>
          </motion.div>
        </div>
      </div>

      <div className="container mx-auto py-16 px-4 max-w-7xl">
        <div className="grid md:grid-cols-3 gap-8">
          <Card className="border-primary/10">
            <CardContent className="pt-8 text-center">
              <div className="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                <Target className="h-8 w-8 text-primary" />
              </div>
              <h3 className="text-2xl font-bold mb-4">رؤيتنا</h3>
              <p className="text-muted-foreground leading-relaxed">
                أن نكون المنصة الرائدة الأولى في الشرق الأوسط التي تربط الطلاب بأفضل الفرص التعليمية وتمكنهم من اتخاذ قرارات دراسية صائبة.
              </p>
            </CardContent>
          </Card>

          <Card className="border-secondary/10">
            <CardContent className="pt-8 text-center">
              <div className="h-16 w-16 rounded-full bg-secondary/10 flex items-center justify-center mx-auto mb-6">
                <Globe className="h-8 w-8 text-secondary" />
              </div>
              <h3 className="text-2xl font-bold mb-4">مهمتنا</h3>
              <p className="text-muted-foreground leading-relaxed">
                توفير معلومات دقيقة وشاملة عن الجامعات والتخصصات ومشاريع التخرج، وتسهيل عملية القبول الأكاديمي بخطوات ذكية ومبسطة.
              </p>
            </CardContent>
          </Card>

          <Card className="border-accent">
            <CardContent className="pt-8 text-center">
              <div className="h-16 w-16 rounded-full bg-accent flex items-center justify-center mx-auto mb-6">
                <Users className="h-8 w-8 text-primary" />
              </div>
              <h3 className="text-2xl font-bold mb-4">مجتمعنا</h3>
              <p className="text-muted-foreground leading-relaxed">
                نبني مجتمعاً متكاملاً من الطلاب والأكاديميين لتبادل المعرفة والخبرات وعرض الإنجازات العلمية ومشاريع التخرج المتميزة.
              </p>
            </CardContent>
          </Card>
        </div>
      </div>
    </Layout>
  );
}
