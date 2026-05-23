import { useEffect, useMemo, useState } from "react";
import { Layout } from "@/components/layout/Layout";
import { Link } from "@inertiajs/react";
import { applyUniversityRatingOverrides, subscribeToUniversityRatingUpdates } from "@/lib/universityRatingSync";
import { motion } from "framer-motion";
import { Search, MapPin, Star, Filter } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Slider } from "@/components/ui/slider";
import {
  Card,
  CardContent,
  CardFooter,
  CardHeader,
} from "@/components/ui/card";

export interface University {
  id: string;
  name: string;
  location: string;
  rating: number;
  fees: number;
  image: string;
  logo: string;
  description: string;
  streetName?: string;
}

export default function Universities({ universities = [] }: { universities: University[] }) {
  const [universityList, setUniversityList] = useState<University[]>(() =>
    applyUniversityRatingOverrides(universities),
  );
  const [searchTerm, setSearchTerm] = useState("");
  const [minRating, setMinRating] = useState(0);
  const [selectedStreet, setSelectedStreet] = useState<string>("all");

  const [sortBy, setSortBy] = useState("rating");

  useEffect(() => {
    setUniversityList(applyUniversityRatingOverrides(universities));
  }, [universities]);

  useEffect(() => {
    return subscribeToUniversityRatingUpdates((update) => {
      setUniversityList((previous) =>
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

  // Extract unique streets for the dropdown
  const uniqueStreets = useMemo(() => {
    const streets = new Set<string>();
    universityList.forEach((uni) => {
      const street = uni.streetName;
      if (street) streets.add(street);
    });
    return Array.from(streets);
  }, [universityList]);

  const filteredUniversities = universityList
    .filter((uni) => {
      const name = uni.name;
      const location = uni.location;
      const street = uni.streetName || "";

      const matchesSearch =
        name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        location.toLowerCase().includes(searchTerm.toLowerCase());

      const matchesRating = uni.rating >= minRating;
      const matchesStreet =
        selectedStreet === "all" || street === selectedStreet;

      return matchesSearch && matchesRating && matchesStreet;
    })
    .sort((a, b) => {
      if (sortBy === "rating") {
        return b.rating - a.rating;
      }
      return 0;
    });

  return (
    <Layout>
      <div className="bg-muted/30 py-12">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
          <h1 className="text-4xl font-bold mb-4">الجامعات</h1>
          <p className="text-muted-foreground">استكشف الجامعات</p>
        </div>
      </div>

      <div className="container mx-auto py-12 px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div className="flex flex-col md:flex-row gap-8">
          {/* Filters Sidebar */}
          <aside className="w-full md:w-64 space-y-6">
            <div className="bg-card p-6 rounded-xl border shadow-sm space-y-6">
              <div className="flex items-center gap-2 font-bold text-lg">
                <Filter className="h-5 w-5" />
                <span>تصفية</span>
              </div>

              <div className="space-y-2">
                <label className="text-sm font-medium">التقييم</label>
                <Slider
                  defaultValue={[0]}
                  max={5}
                  step={0.5}
                  onValueChange={(vals) => setMinRating(vals[0])}
                />
                <div className="text-xs text-muted-foreground flex justify-between">
                  <span>0</span>
                  <span>5 نجوم</span>
                </div>
              </div>

              <div className="space-y-2">
                <label className="text-sm font-medium">الشارع/الحي</label>
                <Select
                  value={selectedStreet}
                  onValueChange={setSelectedStreet}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="اختر الشارع..." />
                  </SelectTrigger>
                  <SelectContent>
                    <div className="p-2 border-b">
                      <Input
                        placeholder="بحث..."
                        className="h-8"
                        onChange={(e) => {
                          // This is a simple client-side search for the dropdown items
                          const searchVal = e.target.value.toLowerCase();
                          const items =
                            document.querySelectorAll('[role="option"]');
                          items.forEach((item) => {
                            if (item.getAttribute("data-value") === "all")
                              return;
                            const text = item.textContent?.toLowerCase() || "";
                            if (text.includes(searchVal)) {
                              (item as HTMLElement).style.display = "";
                            } else {
                              (item as HTMLElement).style.display = "none";
                            }
                          });
                        }}
                      />
                    </div>
                    <SelectItem value="all" data-value="all">
                      الكل
                    </SelectItem>
                    {uniqueStreets.map((street) => (
                      <SelectItem
                        key={street}
                        value={street}
                        data-value={street}
                      >
                        {street}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </aside>

          {/* Main Content */}
          <div className="flex-1 space-y-6">
            <div className="flex gap-4">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground rtl:left-auto rtl:right-3" />
                <Input
                  placeholder="بحث..."
                  className="pl-10 rtl:pl-3 rtl:pr-10"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>
              <Select value={sortBy} onValueChange={setSortBy}>
                <SelectTrigger className="w-[180px]">
                  <SelectValue placeholder="ترتيب حسب" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="rating">الأعلى تقييماً</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
              {filteredUniversities.map((uni) => (
                <motion.div
                  key={uni.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  layout
                >
                  <Link href={`/universities/${uni.id}`}>
                    <Card className="overflow-hidden hover:shadow-lg transition-all cursor-pointer h-full flex flex-col group">
                      <div className="aspect-video overflow-hidden relative">
                        <img
                          src={uni.image}
                          alt={uni.name}
                          className="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110"
                        />
                        <div className="absolute -bottom-6 left-4 rtl:left-auto rtl:right-4 z-10">
                          <div className="w-16 h-16 rounded-full border-4 border-background overflow-hidden bg-white shadow-md">
                            <img
                              src={uni.logo}
                              alt="Logo"
                              className="w-full h-full object-contain p-1"
                            />
                          </div>
                        </div>
                        <div className="absolute top-4 right-4 bg-background/95 backdrop-blur-md px-4 py-2 rounded-xl text-lg font-black flex items-center gap-2 shadow-lg border-2 border-yellow-400/50 text-foreground">
                          <Star className="h-5 w-5 fill-yellow-500 text-yellow-500" />
                          {uni.rating}
                        </div>
                      </div>
                      <CardHeader className="pt-8">
                        <h3 className="text-xl font-bold">{uni.name}</h3>
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                          <MapPin className="h-4 w-4" />
                          {uni.location}
                        </div>
                      </CardHeader>
                      <CardContent className="flex-1">
                        <p className="text-sm text-muted-foreground line-clamp-3">
                          {uni.description}
                        </p>
                      </CardContent>
                      <CardFooter className="border-t pt-4 pb-4 flex justify-end items-center bg-muted/20">
                        <Button
                          variant="ghost"
                          size="sm"
                          className="hover:bg-primary hover:text-white transition-colors"
                        >
                          عرض التفاصيل
                        </Button>
                      </CardFooter>
                    </Card>
                  </Link>
                </motion.div>
              ))}
            </div>

            {filteredUniversities.length === 0 && (
              <div className="text-center py-12 text-muted-foreground">
                لم يتم العثور على جامعات تطابق بحثك.
              </div>
            )}
          </div>
        </div>
      </div>
    </Layout>
  );
}

