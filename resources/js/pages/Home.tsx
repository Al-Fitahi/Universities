import { useEffect, useState } from "react";
import { Layout } from "@/components/layout/Layout";
import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/react";
import {
  ArrowRight,
  Star,
  GraduationCap,
  BookOpen,
  Search,
} from "lucide-react";
import heroImage from "@assets/generated_images/modern_university_campus_hero.png";
import { motion } from "framer-motion";
import { applyUniversityRatingOverrides, subscribeToUniversityRatingUpdates } from "@/lib/universityRatingSync";

export interface University {
  id: string;
  name: string;
  location: string;
  rating: number;
  fees: number;
  image: string;
  logo: string;
  description: string;
}

export interface GraduationProject {
  id: string;
  name: string;
  universityId: string;
  universityName: string;
  collegeName: string;
  majorName: string;
  date: string;
  projectType: string;
  description: string;
}

export default function Home({ universities = [] }: { universities: University[]; graduationProjects: GraduationProject[] }) {
  const [homeUniversities, setHomeUniversities] = useState<University[]>(() =>
    applyUniversityRatingOverrides(universities),
  );
  const isRtl = true;

  useEffect(() => {
    setHomeUniversities(applyUniversityRatingOverrides(universities));
  }, [universities]);

  useEffect(() => {
    return subscribeToUniversityRatingUpdates((update) => {
      setHomeUniversities((previous) =>
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

  const container = {
    hidden: { opacity: 0 },
    show: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  };

  const item = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0 },
  };

  return (
    <Layout>
      <section className="relative h-[600px] flex items-center overflow-hidden">
        <motion.div
          initial={{ x: isRtl ? 100 : -100, opacity: 0 }}
          animate={{ x: 0, opacity: 1 }}
          transition={{ duration: 1, ease: "easeOut" }}
          className="absolute inset-0 z-0"
        >
          <div
            className="absolute inset-0 bg-cover bg-center"
            style={{ backgroundImage: `url(${heroImage})` }}
          >
            <div className="absolute inset-0 bg-primary/70 mix-blend-multiply" />
            <div className="absolute inset-0 bg-gradient-to-t from-background to-transparent" />
          </div>
        </motion.div>

        <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-white">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="max-w-2xl mx-auto md:mx-0 text-center md:text-start"
          >
            <h1 className="text-4xl md:text-6xl font-bold mb-6 leading-tight">
              اكتشف مستقبلك
            </h1>
            <p className="text-lg md:text-xl mb-8 text-white/90">
              بوابتك لأفضل الجامعات والمسارات الأكاديمية.
            </p>
            <div className="flex flex-wrap gap-4">
              <Link href="/universities">
                <Button
                  size="lg"
                  className="bg-secondary text-secondary-foreground hover:bg-secondary/90 font-bold text-lg px-8"
                >
                  استكشف الجامعات
                </Button>
              </Link>
              <Link href="/colleges">
                <Button
                  size="lg"
                  variant="outline"
                  className="bg-white/10 text-white border-white/20 hover:bg-white/20 backdrop-blur-sm"
                >
                  استكشف الكليات
                </Button>
              </Link>
            </div>
          </motion.div>
        </div>
      </section>

      <section className="py-16 md:py-24 bg-muted/30">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-3 gap-8">
            <motion.div
              className="bg-card p-8 rounded-2xl shadow-sm border border-border/50 hover:shadow-md transition-all"
              whileHover={{ y: -5 }}
            >
              <div className="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                <GraduationCap className="h-6 w-6 text-primary" />
              </div>
              <h3 className="text-xl font-bold mb-3">الجامعات</h3>
              <p className="text-muted-foreground mb-4">
                استعرض أفضل الجامعات المناسبة لاهتماماتك ومسارك الأكاديمي.
              </p>
              <Link
                href="/universities"
                className="text-primary font-medium flex items-center gap-2 hover:gap-3 transition-all"
              >
                اقرأ المزيد <ArrowRight className="rotate-180 h-4 w-4" />
              </Link>
            </motion.div>

            <motion.div
              className="bg-card p-8 rounded-2xl shadow-sm border border-border/50 hover:shadow-md transition-all"
              whileHover={{ y: -5 }}
            >
              <div className="h-12 w-12 rounded-full bg-secondary/10 flex items-center justify-center mb-6">
                <Search className="h-6 w-6 text-secondary-foreground" />
              </div>
              <h3 className="text-xl font-bold mb-3">التوجيه</h3>
              <p className="text-muted-foreground mb-4">
                احصل على ترشيحات ذكية بناءً على معدلك واهتماماتك وأهدافك.
              </p>
              <Link
                href="/guidance"
                className="text-primary font-medium flex items-center gap-2 hover:gap-3 transition-all"
              >
                اقرأ المزيد <ArrowRight className="rotate-180 h-4 w-4" />
              </Link>
            </motion.div>

            <motion.div
              className="bg-card p-8 rounded-2xl shadow-sm border border-border/50 hover:shadow-md transition-all"
              whileHover={{ y: -5 }}
            >
              <div className="h-12 w-12 rounded-full bg-accent flex items-center justify-center mb-6">
                <BookOpen className="h-6 w-6 text-primary" />
              </div>
              <h3 className="text-xl font-bold mb-3">المشاريع</h3>
              <p className="text-muted-foreground mb-4">
                اطلع على أحدث مشاريع التخرج واستلهم أفكارًا لمشروعك القادم.
              </p>
              <Link
                href="/projects"
                className="text-primary font-medium flex items-center gap-2 hover:gap-3 transition-all"
              >
                اقرأ المزيد <ArrowRight className="rotate-180 h-4 w-4" />
              </Link>
            </motion.div>
          </div>
        </div>
      </section>

      <section className="py-16 md:py-24">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h2 className="text-3xl font-bold mb-4">الجامعات الشائعة</h2>
              <div className="h-1 w-20 bg-secondary rounded-full" />
            </div>
            <Link href="/universities">
              <Button variant="outline">استكشف الجامعات</Button>
            </Link>
          </div>

          <motion.div
            variants={container}
            initial="hidden"
            whileInView="show"
            viewport={{ once: true }}
            className="grid md:grid-cols-3 gap-8"
          >
            {homeUniversities.slice(0, 3).map((uni) => (
              <motion.div
                key={uni.id}
                variants={item}
                className="group relative overflow-hidden rounded-2xl border bg-card shadow-sm hover:shadow-xl transition-all duration-300"
              >
                <div className="aspect-[4/3] overflow-hidden">
                  <img
                    src={uni.image}
                    alt={uni.name}
                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                  />
                </div>
                <div className="p-6">
                  <div className="flex justify-between items-start mb-2">
                    <h3 className="font-bold text-lg line-clamp-1">{uni.name}</h3>
                    <div className="absolute top-4 right-4 bg-background/95 backdrop-blur-md px-3 py-1.5 rounded-xl text-sm font-black flex items-center gap-1.5 shadow-lg border border-yellow-400/50 text-foreground">
                      <Star className="h-4 w-4 fill-yellow-500 text-yellow-500" />
                      {uni.rating}
                    </div>
                  </div>
                  <p className="text-sm text-muted-foreground mb-4 line-clamp-2">
                    {uni.description}
                  </p>
                  <div className="flex items-center justify-between text-sm text-muted-foreground">
                    <span>{uni.location}</span>
                  </div>
                  <div className="mt-4 pt-4 border-t flex justify-between items-center">
                    <span className="font-bold text-primary">
                      {uni.fees === 0 ? "مجاني" : `${uni.fees.toLocaleString()} SAR`}
                    </span>
                    <Link href={`/universities/${uni.id}`}>
                      <Button
                        size="sm"
                        variant="ghost"
                        className="hover:bg-primary/10 hover:text-primary"
                      >
                        عرض التفاصيل
                      </Button>
                    </Link>
                  </div>
                </div>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>
    </Layout>
  );
}

