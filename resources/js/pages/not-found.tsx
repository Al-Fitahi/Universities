import { Card, CardContent } from "@/components/ui/card";
import { AlertCircle, Home } from "lucide-react";
import { Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";

export default function NotFound() {
  return (
    <div className="min-h-screen w-full flex items-center justify-center bg-muted/30">
      <Card className="w-full max-w-md mx-4 shadow-xl border-primary/20">
        <CardContent className="pt-8 text-center space-y-6">
          <div className="flex justify-center">
            <div className="h-16 w-16 bg-destructive/10 rounded-full flex items-center justify-center">
              <AlertCircle className="h-8 w-8 text-destructive" />
            </div>
          </div>

          <div>
            <h1 className="text-3xl font-bold text-foreground mb-2">404</h1>
            <h2 className="text-xl font-semibold text-foreground/80">الصفحة غير موجودة</h2>
          </div>

          <p className="text-muted-foreground">
            الصفحة التي تبحث عنها قد تكون حُذفت أو تغير اسمها أو أنها غير متاحة مؤقتًا.
          </p>

          <Link href="/">
            <Button className="w-full font-bold">
              <Home className="mr-2 h-4 w-4" />
              العودة للرئيسية
            </Button>
          </Link>
        </CardContent>
      </Card>
    </div>
  );
}
