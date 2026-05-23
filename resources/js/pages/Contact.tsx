import { useState } from "react";
import { Layout } from "@/components/layout/Layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { Mail, Phone, MapPin, Send } from "lucide-react";
import { toast } from "sonner";
import { motion } from "framer-motion";

export default function Contact() {
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    // Simulate sending message
    setTimeout(() => {
      setIsSubmitting(false);
      toast.success("تم إرسال رسالتك بنجاح", {
        description: "سنتواصل معك قريباً",
      });
    }, 1500);
  };

  return (
    <Layout>
      <div className="bg-muted/30 py-16">
        <div className="container mx-auto px-4 max-w-4xl text-center">
          <motion.h1
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            className="text-4xl md:text-5xl font-bold mb-6"
          >
            اتصل بنا
          </motion.h1>
          <motion.p
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ delay: 0.2 }}
            className="text-xl text-muted-foreground"
          >
            نحن هنا لمساعدتك والإجابة على جميع استفساراتك
          </motion.p>
        </div>
      </div>

      <div className="container mx-auto py-16 px-4 max-w-7xl">
        <div className="grid md:grid-cols-2 gap-12 items-start">
          {/* Contact Information */}
          <div className="space-y-8">
            <h2 className="text-3xl font-bold mb-6">معلومات التواصل</h2>

            <div className="flex items-start gap-4">
              <div className="bg-primary/10 p-4 rounded-full text-primary">
                <MapPin className="h-6 w-6" />
              </div>
              <div>
                <h3 className="text-xl font-bold mb-2">العنوان</h3>
                <p className="text-muted-foreground leading-relaxed">
                  الرياض، المملكة العربية السعودية
                </p>
              </div>
            </div>

            <div className="flex items-start gap-4">
              <div className="bg-secondary/10 p-4 rounded-full text-secondary">
                <Mail className="h-6 w-6" />
              </div>
              <div>
                <h3 className="text-xl font-bold mb-2">البريد الإلكتروني</h3>
                <p className="text-muted-foreground">info@uniguide.edu.sa</p>
                <p className="text-muted-foreground">support@uniguide.edu.sa</p>
              </div>
            </div>

            <div className="flex items-start gap-4">
              <div className="bg-accent p-4 rounded-full text-primary">
                <Phone className="h-6 w-6" />
              </div>
              <div>
                <h3 className="text-xl font-bold mb-2">رقم الهاتف</h3>
                <p className="text-muted-foreground text-right" dir="ltr">
                  +966 11 123 4567
                </p>
                <p className="text-muted-foreground text-right" dir="ltr">
                  +966 50 123 4567
                </p>
              </div>
            </div>
          </div>

          {/* Contact Form */}
          <Card className="border-border/50 shadow-lg">
            <CardHeader>
              <CardTitle className="text-2xl">أرسل لنا رسالة</CardTitle>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="space-y-2">
                  <Label htmlFor="name">الاسم الكامل</Label>
                  <Input
                    id="name"
                    placeholder="أحمد محمد"
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email">البريد الإلكتروني</Label>
                  <Input
                    id="email"
                    type="email"
                    placeholder="example@email.com"
                    required
                    dir="ltr"
                    className="text-right"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="subject">الموضوع</Label>
                  <Input
                    id="subject"
                    placeholder="استفسار عن القبول"
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="message">الرسالة</Label>
                  <Textarea
                    id="message"
                    placeholder="اكتب رسالتك هنا..."
                    className="min-h-[150px]"
                    required
                  />
                </div>

                <Button
                  type="submit"
                  className="w-full h-12 text-lg font-bold"
                  disabled={isSubmitting}
                >
                  {isSubmitting ? (
                    "جاري الإرسال..."
                  ) : (
                    <>
                      <Send className="h-5 w-5 ml-2" />
                      إرسال الرسالة
                    </>
                  )}
                </Button>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>
    </Layout>
  );
}
