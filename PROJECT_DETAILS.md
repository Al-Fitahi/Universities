# تفاصيل مشروع Queen – نظام قبول الجامعات

> وثيقة شاملة تغطي كل جوانب المشروع من الألف إلى الياء

---

## 1. نظرة عامة

**اسم المشروع:** Queen  
**النوع:** تطبيق ويب متكامل (Full-Stack Web Application)  
**الغرض:** منصة إلكترونية لقبول الطلاب في الجامعات، تتيح للطلاب التقديم للتخصصات الجامعية، وللجامعات إدارة طلبات القبول، وللمشرفين الإداريين إدارة المنصة بالكامل.

---

## 2. المكدس التقني (Technology Stack)

### الواجهة الخلفية (Backend)
| المكوّن | التقنية |
|---------|---------|
| إطار العمل | Laravel 12 |
| لوحة الإدارة | Filament 4 |
| المصادقة | Laravel Fortify |
| المصادقة الثنائية | Email OTP + Google Authenticator (TOTP) |
| معالجة الصور | Intervention Image 3 + Spatie Image Optimizer |
| نظام التقييم | jobmetric/laravel-star |
| محرك القوالب للـ SSR | Inertia.js |

### الواجهة الأمامية (Frontend)
| المكوّن | التقنية |
|---------|---------|
| إطار العمل | React 19 + TypeScript |
| الاتصال بالخادم | Inertia.js |
| التصميم | Tailwind CSS 4 + Radix UI |
| إدارة النماذج | React Hook Form + Zod |
| الحركات والتأثيرات | Framer Motion |
| أدوات البناء | Vite 7 |

### قاعدة البيانات
- MySQL (الإنتاج) / SQLite (التطوير)

---

## 3. جداول قاعدة البيانات والحقول

### 3.1 جدول `users` – المستخدمون
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام (للعرض في URLs) |
| `name` | string | اسم المستخدم |
| `email` | string (unique) | البريد الإلكتروني |
| `email_verified_at` | timestamp | وقت تأكيد البريد |
| `password` | string (hashed) | كلمة المرور |
| `two_factor_secret` | text (nullable) | سر المصادقة الثنائية |
| `two_factor_recovery_codes` | text (nullable) | رموز الاسترداد |
| `two_factor_confirmed_at` | timestamp (nullable) | وقت تأكيد المصادقة الثنائية |
| `remember_token` | string (nullable) | رمز التذكر |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.2 جدول `admins` – المشرفون
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `name` | string | اسم المشرف |
| `email` | string (unique) | البريد الإلكتروني |
| `email_verified_at` | timestamp | وقت تأكيد البريد |
| `password` | string (hashed) | كلمة المرور |
| `has_email_authentication` | boolean | تفعيل OTP عبر البريد |
| `app_authentication_secret` | text (encrypted, nullable) | سر تطبيق المصادقة |
| `remember_token` | string (nullable) | رمز التذكر |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

> ملاحظة: المشرف الوحيد المسموح له بالوصول هو `Jihad@gmail.com` (محدّد في `canAccessPanel`).

---

### 3.3 جدول `universities` – الجامعات
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `name` | string | اسم الجامعة |
| `email` | string (unique) | البريد الإلكتروني |
| `email_verified_at` | timestamp | وقت تأكيد البريد |
| `password` | string (hashed) | كلمة المرور |
| `address` | foreignId → `streets.id` | الموقع/العنوان |
| `phone` | string (nullable) | رقم الهاتف |
| `description` | text (nullable) | وصف الجامعة |
| `status` | enum(`pending`,`approved`,`rejected`) | حالة الجامعة |
| `type` | enum(`public`,`private`) | نوع الجامعة |
| `location` | text (nullable) | الموقع الجغرافي / رابط الخريطة |
| `image_path` | string (nullable) | مسار صورة الشعار |
| `image_background` | string (nullable) | مسار صورة الخلفية |
| `has_email_authentication` | boolean | تفعيل OTP عبر البريد |
| `app_authentication_secret` | text (encrypted, nullable) | سر تطبيق المصادقة |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.4 جدول `streets` – الشوارع/المناطق
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `name` | string (unique) | اسم الشارع/المنطقة |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.5 جدول `colleges` – الكليات
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `name` | string | اسم الكلية |
| `description` | string (nullable) | وصف الكلية |
| `image_path` | string (nullable) | مسار صورة الكلية |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.6 جدول `majors` – التخصصات
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `name` | string | اسم التخصص |
| `description` | text (nullable) | وصف التخصص |
| `designation_jobs` | text (nullable) | الوظائف المرتبطة بالتخصص |
| `study_years` | string (nullable) | عدد سنوات الدراسة (عام) |
| `college_id` | foreignId → `colleges.id` | الكلية التابع لها |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.7 جدول `university_majors` – تخصصات الجامعة
> جدول وسيط بين الجامعات والتخصصات مع بيانات إضافية خاصة بكل جامعة

| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `number_of_seats` | unsignedInt (nullable) | عدد المقاعد المتاحة |
| `admission_rate` | float(5,2) (nullable) | الحد الأدنى لمعدل القبول |
| `study_years` | unsignedInt (nullable) | سنوات الدراسة لهذه الجامعة |
| `tuition_fee` | float(10,2) (nullable) | الرسوم الدراسية |
| `published` | boolean | هل التخصص منشور للعامة |
| `major_id` | foreignId → `majors.id` | التخصص |
| `university_id` | foreignId → `universities.id` | الجامعة |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.8 جدول `students` – الطلاب
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `F_name` | string | الاسم الأول |
| `S_name` | string | الاسم الثاني |
| `Th_name` | string | الاسم الثالث |
| `Su_name` | string | اسم العائلة |
| `phone_number` | string(20) | رقم الهاتف |
| `graduation_date` | date | تاريخ التخرج |
| `graduation_grade` | float(2,2) | معدل التخرج |
| `certificate_image` | string | مسار صورة شهادة التخرج |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.9 جدول `applications` – الطلبات (القبول)
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `application_code` | string (unique) | رمز الطلب (عشوائي 13 حرف) |
| `student_id` | foreignId → `students.id` | الطالب |
| `university_major_id` | foreignId → `university_majors.id` | التخصص الجامعي المطلوب |
| `user_id` | foreignId → `users.id` | المستخدم الذي قدّم الطلب |
| `status` | enum | حالة الطلب (انظر أدناه) |
| `is_active` | boolean | هل الطلب نشط |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

**قيم `status`:**
- `processing` – قيد المعالجة (الافتراضي)
- `accepted` – مقبول
- `registered` – مسجّل
- `rejected` – مرفوض
- `canceled` – ملغى

**انتقالات الحالة المسموح بها:**
```
processing → accepted | rejected
accepted   → registered
registered → (نهائي)
rejected   → (نهائي)
canceled   → (نهائي)
```

---

### 3.10 جدول `university_images` – صور الجامعة
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `university_id` | foreignId → `universities.id` | الجامعة |
| `path_main` | string | المسار الأساسي للصورة |
| `path_thumb` | string (nullable) | مسار الصورة المصغّرة |
| `priority` | unsignedInt | الأولوية في الترتيب |
| `is_active` | boolean | هل الصورة مفعّلة |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.11 جدول `banners` – البنرات الإعلانية
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `title` | string | عنوان البنر |
| `location` | string (indexed) | موقع البنر في الموقع |
| `is_active` | boolean | هل البنر مفعّل |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.12 جدول `banner_images` – صور البنرات
| الحقل | النوع | الوصف |
|-------|-------|-------|
| `id` | bigint (PK) | المعرّف الداخلي |
| `public_id` | string (unique) | المعرّف العام |
| `banner_id` | foreignId → `banners.id` | البنر |
| `path_main` | string | المسار الأساسي للصورة |
| `path_thumb` | string (nullable) | مسار الصورة المصغّرة |
| `priority` | unsignedInt | الأولوية في الترتيب |
| `link_url` | string (nullable) | رابط الضغط على البنر |
| `is_active` | boolean | هل الصورة مفعّلة |
| `created_at` / `updated_at` | timestamps | وقت الإنشاء / التحديث |

---

### 3.13 جداول النظام (System Tables)
| الجدول | الوصف |
|--------|-------|
| `password_reset_tokens` | رموز إعادة تعيين كلمة المرور |
| `sessions` | جلسات المستخدمين |
| `cache` / `cache_locks` | ذاكرة التخزين المؤقت |
| `jobs` / `job_batches` / `failed_jobs` | قائمة انتظار المهام (Queue) |
| `stars` | تقييمات الجامعات (من مكتبة laravel-star) |

---

## 4. النماذج (Models) والعلاقات

### `User` – المستخدم
- `hasMany(Application::class)` → الطلبات المقدَّمة
- يستخدم `TwoFactorAuthenticatable` (Fortify)
- يستخدم `CanStar` → يمكنه تقييم الجامعات
- يستخدم `HasPublicId` Trait

### `Admin` – المشرف
- يُطبّق `FilamentUser`, `HasEmailAuthentication`, `HasAppAuthentication`
- دوال: `HasEmailAuthentication()`, `toggleEmailAuthentication()`, `canAccessPanel()`, `getAppAuthenticationSecret()`, `saveAppAuthenticationSecret()`, `getAppAuthenticationHolderName()`

### `University` – الجامعة
- `belongsTo(Street::class, 'address')` → المنطقة
- `hasMany(UniversityMajor::class)` → تخصصاتها
- `hasMany(UniversityImage::class)` → صورها
- يُطبّق `HasEmailAuthentication`, `HasAppAuthentication`
- يستخدم `HasStar` → قابل للتقييم
- خاصية محسوبة (Accessor): `avatar` (اسم بديل لـ `image_path`)
- خاصية محسوبة: `avatar_url` (رابط URL كامل للصورة)
- دوال: `getAvatarAttribute()`, `setAvatarAttribute()`, `getAvatarUrlAttribute()`, `HasEmailAuthentication()`, `toggleEmailAuthentication()`, `getAppAuthenticationSecret()`, `saveAppAuthenticationSecret()`, `getAppAuthenticationHolderName()`

### `Street` – المنطقة/الشارع
- `hasMany(University::class)` (علاقة ضمنية)

### `College` – الكلية
- `hasMany(Major::class)` → تخصصاتها
- يستخدم `HasPublicId` Trait
- حدث `booted`: عند حفظ الكلية يُعاد معالجة الصورة إلى WebP تلقائيًا

### `Major` – التخصص
- `belongsTo(College::class)` → الكلية
- `hasMany(UniversityMajor::class)` → تخصصات الجامعات
- `belongsToMany(University::class, 'university_majors')` → الجامعات التي تقدمه

### `UniversityMajor` – تخصص جامعة
- `belongsTo(Major::class)` → التخصص
- `belongsTo(University::class)` → الجامعة
- `hasMany(Application::class)` → الطلبات المقدَّمة

### `Student` – الطالب
- `hasMany(Application::class)` → طلباته
- خاصية محسوبة: `full_name` = `F_name S_name Th_name Su_name`
- دالة ثابتة: `findOrCreateByFullName(array $data)` → إيجاد أو إنشاء طالب بناءً على الاسم الرباعي

### `Application` – طلب القبول
- `belongsTo(Student::class)` → الطالب
- `belongsTo(UniversityMajor::class)` → التخصص الجامعي
- `belongsTo(User::class)` → المستخدم
- دوال: `canChangeStatusTo(string $newStatus)`, `changeStatus(string $newStatus)`
- حدث `booted`: التحقق من صحة انتقالات الحالة عند الحفظ

### `UniversityImage` – صورة الجامعة
- `belongsTo(University::class)` → الجامعة

### `Banner` / `BannerImage` – البنر وصوره
- `Banner` → `hasMany(BannerImage::class)`
- `BannerImage` → `belongsTo(Banner::class)`

---

## 5. المتحكمات (Controllers)

### `UniversityController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `index(Request $request)` | GET | `/universities` | عرض قائمة الجامعات المعتمدة مع دعم الفلترة (بالموقع، النوع، البحث) |
| `show(University $university)` | GET | `/universities/{university}` | عرض تفاصيل جامعة محددة مع تخصصاتها وصورها |
| `fileUrl(?string $path)` | - | - | تحويل مسار الملف إلى URL كامل |

### `CollegeController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `index()` | GET | `/colleges` | عرض قائمة الكليات |

### `ApplicationController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `store(ApplicationRequest $request)` | POST | - | إنشاء طلب قبول جديد |

### `UniversityRatingController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `store(Request $request, $university)` | POST | `/universities/{university}/rate` | تقييم جامعة (يتطلب تسجيل الدخول) |

### `HomeController`
- متحكم الصفحة الرئيسية (غير مفعّل حاليًا في الراوتر)

### `MajorController`
- متحكم التخصصات (مُعرَّف لكن الراوتر لا يربطه بعد)

### `Settings\ProfileController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `edit()` | GET | `/settings/profile` | عرض صفحة تعديل الملف الشخصي |
| `update(ProfileUpdateRequest)` | PATCH | `/settings/profile` | تحديث بيانات الملف الشخصي |
| `destroy()` | DELETE | `/settings/profile` | حذف الحساب |

### `Settings\PasswordController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `edit()` | GET | `/settings/password` | عرض صفحة تغيير كلمة المرور |
| `update()` | PUT | `/settings/password` | تغيير كلمة المرور (مقيّد: 6 محاولات/دقيقة) |

### `Settings\TwoFactorAuthenticationController`
| الدالة | الطريقة | المسار | الوصف |
|--------|---------|--------|-------|
| `show()` | GET | `/settings/two-factor` | عرض صفحة المصادقة الثنائية |

---

## 6. الخدمات (Services)

### `ApplicationService`
**الملف:** `app/Services/ApplicationService.php`

| الدالة | الوصف |
|--------|-------|
| `createApplication(array $data): Application` | إنشاء طلب قبول جديد داخل transaction؛ يُنشئ أو يسترجع الطالب، يطبّق قواعد المجال، ثم يُنشئ الطلب |
| `generateUniqueCode(): string` | توليد رمز فريد من 13 حرفًا عشوائيًا للطلب |

### `ImageProcessor`
**الملف:** `app/Services/Image/ImageProcessor.php`

يُعيد معالجة الصور المرفوعة وتحويلها إلى WebP مع إنشاء نسخة مصغّرة (thumbnail).
| الدالة | الوصف |
|--------|-------|
| `reprocessFromDisk(string $disk, string $path, string $folder)` | قراءة صورة من القرص وإعادة معالجتها |

---

## 7. قواعد المجال (Domain Rules)

### `ApplicationRules`
**الملف:** `app/Domain/ApplicationRules.php`

| الدالة | الوصف |
|--------|-------|
| `ensureNoActiveOrBlocked(int $studentId)` | يمنع إنشاء طلب جديد إذا كان الطالب لديه طلب سابق نشط أو غير منتهٍ |
| `ensureNotRejectedRecently(int $studentId, int $majorId)` | يمنع إعادة التقديم لنفس التخصص خلال 9 أشهر من الرفض الأخير |
| `ensureMeetsAdmissionRate(Student $student, UniversityMajor $major)` | يتحقق أن معدل الطالب ≥ الحد الأدنى لمعدل القبول للتخصص |

---

## 8. Traits

### `HasPublicId`
**الملف:** `app/Models/Traits/HasPublicId.php`

يُولّد `public_id` فريدًا عشوائيًا تلقائيًا لكل نموذج عند الإنشاء. يُستخدم كمعرّف في URLs بدلًا من `id`.

---

## 9. الـ Middleware

| الـ Middleware | الوصف |
|----------------|-------|
| `CheckAdminStatus` | يتحقق من صلاحية الوصول للوحة الإدارة |
| `CheckUniversityStatus` | يتحقق من أن الجامعة معتمدة (`approved`) قبل السماح لها بالدخول للوحتها |
| `HandleAppearance` | يُدير مظهر الواجهة (dark/light mode) |
| `HandleInertiaRequests` | يُمرّر بيانات مشتركة لصفحات Inertia (بيانات المستخدم، إلخ) |

---

## 10. المسارات (Routes)

### مسارات عامة (بدون مصادقة)
| المسار | الطريقة | الصفحة | الوصف |
|--------|---------|---------|-------|
| `/` | GET | `Home` | الصفحة الرئيسية |
| `/universities` | GET | `Universities` | قائمة الجامعات |
| `/universities/{university}` | GET | `UniversityDetails` | تفاصيل جامعة |
| `/colleges` | GET | `Colleges` | قائمة الكليات |
| `/guidance` | GET | `Guidance` | صفحة الإرشاد |

### مسارات تتطلب تسجيل الدخول والتحقق
| المسار | الطريقة | الصفحة | الوصف |
|--------|---------|---------|-------|
| `/dashboard` | GET | `dashboard` | لوحة تحكم المستخدم |
| `/universities/{university}/rate` | POST | - | تقييم جامعة |

### مسارات الإعدادات (تتطلب تسجيل الدخول)
| المسار | الطريقة | الصفحة | الوصف |
|--------|---------|---------|-------|
| `/settings` | GET | (يُعيد توجيه) | → `/settings/profile` |
| `/settings/profile` | GET/PATCH/DELETE | `settings/profile` | تعديل الملف الشخصي |
| `/settings/password` | GET/PUT | `settings/password` | تغيير كلمة المرور |
| `/settings/appearance` | GET | `settings/appearance` | إعدادات المظهر |
| `/settings/two-factor` | GET | `settings/two-factor` | المصادقة الثنائية |

### مسارات المصادقة (Fortify – تلقائية)
`/login`, `/register`, `/logout`, `/forgot-password`, `/reset-password`, `/email/verify`, `/two-factor-challenge`, `/confirm-password`

### لوحة إدارة النظام (Filament Admin)
**المسار الأساسي:** `/admin`

| المورد | المسارات | الوصف |
|--------|----------|-------|
| المستخدمون | `/admin/users` | إدارة المستخدمين |
| الجامعات | `/admin/universities` | إدارة الجامعات |
| الكليات | `/admin/colleges` | إدارة الكليات |
| التخصصات | `/admin/majors` | إدارة التخصصات |
| الطلاب | `/admin/students` | إدارة الطلاب |
| البنرات | `/admin/banners` | إدارة البنرات الإعلانية |
| سجلات النظام | `/admin/logs` | عرض سجلات النظام (Filament Log Viewer) |

### لوحة تحكم الجامعة (Filament University)
**المسار الأساسي:** `/university`

| الصفحة/المورد | المسار | الوصف |
|---------------|--------|-------|
| لوحة التحكم | `/university` | Dashboard |
| طلبات القبول | `/university/applications` | إدارة طلبات القبول |
| صور الجامعة | `/university/university-images` | رفع وإدارة الصور |
| اختيار التخصصات | `/university/select-majors` | اختيار التخصصات التي تقدمها الجامعة |
| تحديد شروط القبول | `/university/set-conditions` | تحديد معدلات ورسوم القبول |
| تسجيل جامعة جديدة | `/university/register` | تسجيل حساب جامعة |
| تسجيل الدخول | `/university/login` | تسجيل الدخول للجامعة |
| الملف الشخصي | `/university/profile` | تعديل ملف الجامعة |

---

## 11. صفحات الواجهة الأمامية (Frontend Pages)

### الصفحات العامة
| الملف | المسار | الوصف |
|-------|--------|-------|
| `pages/Home.tsx` | `/` | الصفحة الرئيسية |
| `pages/Universities.tsx` | `/universities` | قائمة الجامعات مع فلترة وبحث |
| `pages/UniversityDetails.tsx` | `/universities/{id}` | تفاصيل الجامعة (صور، تخصصات، تقييم) |
| `pages/Colleges.tsx` | `/colleges` | قائمة الكليات |
| `pages/Guidance.tsx` | `/guidance` | صفحة الإرشاد الجامعي |
| `pages/Apply.tsx` | - | صفحة التقديم للقبول |
| `pages/Articles.tsx` | - | المقالات |
| `pages/dashboard.tsx` | `/dashboard` | لوحة تحكم المستخدم |
| `pages/not-found.tsx` | - | صفحة 404 |
| `pages/welcome.tsx` | - | صفحة الترحيب |

### صفحات المصادقة
| الملف | المسار | الوصف |
|-------|--------|-------|
| `pages/auth/login.tsx` | `/login` | تسجيل الدخول |
| `pages/auth/register.tsx` | `/register` | إنشاء حساب |
| `pages/auth/forgot-password.tsx` | `/forgot-password` | نسيان كلمة المرور |
| `pages/auth/reset-password.tsx` | `/reset-password` | إعادة تعيين كلمة المرور |
| `pages/auth/verify-email.tsx` | `/email/verify` | تأكيد البريد الإلكتروني |
| `pages/auth/confirm-password.tsx` | `/confirm-password` | تأكيد كلمة المرور |
| `pages/auth/two-factor-challenge.tsx` | `/two-factor-challenge` | تحدي المصادقة الثنائية |

### صفحات الإعدادات
| الملف | المسار | الوصف |
|-------|--------|-------|
| `pages/settings/profile.tsx` | `/settings/profile` | تعديل الملف الشخصي |
| `pages/settings/password.tsx` | `/settings/password` | تغيير كلمة المرور |
| `pages/settings/appearance.tsx` | `/settings/appearance` | إعدادات المظهر (dark/light) |
| `pages/settings/two-factor.tsx` | `/settings/two-factor` | إعداد المصادقة الثنائية |

---

## 12. المكوّنات (Components)

### مكوّنات التخطيط
| المكوّن | الوصف |
|---------|-------|
| `components/layout/Layout.tsx` | التخطيط العام للموقع |
| `components/layout/Navbar.tsx` | شريط التنقل |
| `components/layout/Footer.tsx` | التذييل |
| `components/app-header.tsx` | رأس التطبيق الداخلي |
| `components/app-sidebar.tsx` | الشريط الجانبي |
| `components/app-shell.tsx` | الهيكل الأساسي |

### مكوّنات المصادقة والمستخدم
| المكوّن | الوصف |
|---------|-------|
| `components/user-info.tsx` | عرض معلومات المستخدم |
| `components/nav-user.tsx` | قائمة المستخدم في التنقل |
| `components/delete-user.tsx` | حذف الحساب |
| `components/two-factor-setup-modal.tsx` | نافذة إعداد المصادقة الثنائية |
| `components/two-factor-recovery-codes.tsx` | رموز الاسترداد |

### مكوّنات الواجهة (UI)
> جميعها في `components/ui/` مبنية على Radix UI + shadcn/ui:

`accordion`, `alert`, `alert-dialog`, `aspect-ratio`, `avatar`, `badge`, `breadcrumb`, `button`, `button-group`, `calendar`, `card`, `carousel`, `chart`, `checkbox`, `collapsible`, `command`, `context-menu`, `dialog`, `drawer`, `dropdown-menu`, `empty`, `field`, `form`, `hover-card`, `icon`, `input`, `input-group`, `input-otp`, `item`, `kbd`, `label`, `menubar`, `navigation-menu`, `pagination`, `placeholder-pattern`, `popover`, `progress`, `radio-group`, `resizable`, `scroll-area`, `select`, `separator`, `sheet`, `sidebar`, `skeleton`, `slider`, `sonner`, `spinner`, `switch`, `table`, `tabs`, `textarea`, `toast`, `toaster`, `toggle`, `toggle-group`, `tooltip`

---

## 13. Hooks (خطّافات React المخصّصة)

| الـ Hook | الوصف |
|---------|-------|
| `use-active-url.ts` | تحديد الرابط النشط في التنقل |
| `use-appearance.tsx` | إدارة المظهر (dark/light/system) |
| `use-clipboard.ts` | نسخ النص للحافظة |
| `use-initials.tsx` | استخراج الأحرف الأولى من الاسم |
| `use-mobile-navigation.ts` | التنقل على الأجهزة المحمولة |
| `use-mobile.tsx` | كشف الأجهزة المحمولة |
| `use-toast.ts` | إدارة الإشعارات (Toast) |
| `use-two-factor-auth.ts` | إدارة تدفق المصادقة الثنائية |

---

## 14. السياقات (Contexts)

| السياق | الوصف |
|--------|-------|
| `contexts/LanguageContext.tsx` | إدارة اللغة (دعم التعريب) |

---

## 15. موارد لوحة إدارة النظام (Admin Filament Resources)

### `UserResource` – المستخدمون
- **الجدول:** `users`
- **الصفحات:** `ListUsers`, `CreateUser`, `EditUser`, `ViewUser`
- **المخططات:** `UserForm`, `UserInfolist`, `UsersTable`

### `UniversityResource` – الجامعات
- **الجدول:** `universities`
- **الصفحات:** `ListUniversities`, `CreateUniversity`, `EditUniversity`, `ViewUniversity`
- **المخططات:** `UniversityForm`, `UniversityInfolist`, `UniversitiesTable`

### `CollegeResource` – الكليات
- **الجدول:** `colleges`
- **الصفحات:** `ListColleges`, `CreateCollege`, `EditCollege`, `ViewCollege`
- **المخططات:** `CollegeForm`, `CollegeInfolist`, `CollegesTable`
- **العلاقات:** `MajorsRelationManager`

### `MajorResource` – التخصصات
- **الجدول:** `majors`
- **الصفحات:** `ListMajors`, `CreateMajor`, `EditMajor`, `ViewMajor`
- **المخططات:** `MajorForm`, `MajorInfolist`, `MajorsTable`

### `StudentResource` – الطلاب
- **الجدول:** `students`
- **الصفحات:** `ListStudents`, `CreateStudent`, `EditStudent`, `ViewStudent`
- **المخططات:** `StudentForm`, `StudentInfolist`, `StudentsTable`

### `BannerResource` – البنرات
- **الجدول:** `banners`
- **الصفحات:** `ListBanners`, `CreateBanner`, `EditBanner`, `ViewBanner`
- **العلاقات:** `BannerImagesRelationManager`

---

## 16. موارد لوحة تحكم الجامعة (University Filament Resources)

### `ApplicationResource` – طلبات القبول
- **الجدول:** `applications`
- **الصفحات:** `ListApplications`, `CreateApplication`, `EditApplication`, `ViewApplication`
- **المخططات:** `ApplicationForm`, `ApplicationInfolist`, `ApplicationsTable`

### `UniversityImageResource` – صور الجامعة
- **الجدول:** `university_images`
- **الصفحات:** `ListUniversityImages`, `CreateUniversityImage`, `EditUniversityImage`

### صفحات مخصصة في لوحة الجامعة
| الصفحة | الوصف |
|--------|-------|
| `UniversitySelectMajors` | اختيار التخصصات التي تقدمها الجامعة |
| `UniversitySetConditions` | تحديد شروط القبول (معدل، رسوم، مقاعد) |

---

## 17. صفحات مصادقة لوحة الجامعة (Filament Auth Pages)

| الصفحة | الوصف |
|--------|-------|
| `LoginUniversity` | تسجيل الدخول للجامعة (مخصّص) |
| `RegisterUniversity` | تسجيل جامعة جديدة (مخصّص) |
| `EditUniversityProfile` | تعديل ملف الجامعة الشخصي (مخصّص) |

---

## 18. ميزات المشروع الرئيسية

1. **نظام متعدد الأطراف:** ثلاثة أنواع من المستخدمين (مستخدم عادي، جامعة، مشرف)
2. **دورة حياة طلبات القبول:** من `processing` → `accepted` → `registered` أو `rejected`/`canceled`
3. **قواعد القبول الذكية:**
   - منع تعدد الطلبات النشطة
   - منع إعادة التقديم خلال 9 أشهر من الرفض
   - التحقق من المعدل الأكاديمي
4. **المصادقة الثنائية:** عبر بريد إلكتروني (OTP) أو تطبيق المصادقة (TOTP/Google Authenticator)
5. **معالجة الصور:** تحويل تلقائي إلى WebP + إنشاء نسخ مصغّرة
6. **تقييم الجامعات:** نظام نجوم مع إمكانية التقييم لكل مستخدم
7. **البنرات الإعلانية:** نظام بنرات مرن مع دعم أولويات العرض
8. **الفلترة والبحث:** في قوائم الجامعات (بالاسم، النوع، الموقع)
9. **دعم اللغة العربية:** واجهة وسياق اللغة
10. **دعم Dark/Light Mode**
11. **تحكم كامل للجامعة:** اختيار تخصصاتها + تحديد شروط القبول + إدارة الطلبات
12. **عرض سجلات النظام** في لوحة الإدارة

---

## 19. بنية مجلدات المشروع

```
QueenLastVersion/
├── app/
│   ├── Actions/Fortify/         # إجراءات Fortify (إنشاء مستخدم، إعادة تعيين كلمة المرور)
│   ├── Domain/                   # قواعد المجال (ApplicationRules)
│   ├── Exceptions/               # استثناءات مخصصة (ApplicationRuleException)
│   ├── Filament/
│   │   ├── Pages/Auth/           # صفحات مصادقة Admin
│   │   ├── Resources/            # موارد لوحة إدارة النظام
│   │   └── University/           # لوحة تحكم الجامعة (موارد + صفحات)
│   ├── Http/
│   │   ├── Controllers/          # متحكمات الويب
│   │   ├── Middleware/           # الـ Middleware
│   │   └── Requests/             # طلبات التحقق (Form Requests)
│   ├── Models/                   # نماذج Eloquent
│   │   └── Traits/               # HasPublicId Trait
│   ├── Providers/                # مزودو الخدمات (Filament Panels, Fortify)
│   └── Services/                 # خدمات الأعمال (ApplicationService, ImageProcessor)
├── database/
│   ├── migrations/               # ملفات إنشاء الجداول
│   ├── seeders/                  # بيانات أولية
│   └── factories/                # مصانع بيانات الاختبار
├── resources/
│   ├── js/
│   │   ├── components/           # مكوّنات React
│   │   ├── contexts/             # سياقات React
│   │   ├── hooks/                # Hooks مخصصة
│   │   ├── layouts/              # تخطيطات الصفحات
│   │   ├── lib/                  # مساعدات (utils, queryClient, mockData)
│   │   ├── pages/                # صفحات Inertia
│   │   └── types/                # تعريفات TypeScript
│   └── views/                    # قوالب Blade (للـ SSR)
├── routes/
│   ├── web.php                   # المسارات الرئيسية
│   └── settings.php              # مسارات الإعدادات
├── public/                       # الملفات العامة
├── storage/                      # التخزين (الصور، اللوغز)
├── config/                       # ملفات الإعداد
├── tests/                        # اختبارات PHPUnit
└── tools/                        # أدوات مساعدة
```

---

## 20. كيفية تشغيل المشروع

```bash
# 1. تثبيت التبعيات
composer install
npm install

# 2. إعداد ملف البيئة
cp .env.example .env
php artisan key:generate

# 3. إنشاء قاعدة البيانات وتشغيل الـ Migrations
php artisan migrate --seed

# 4. تشغيل الخادم
php artisan serve

# 5. تشغيل Vite (في نافذة منفصلة)
npm run dev

# 6. بناء للإنتاج
npm run build
php artisan config:cache
php artisan route:cache
```

**لوحة الإدارة:** `http://localhost:8000/admin`  
**لوحة الجامعة:** `http://localhost:8000/university`  
**الموقع الرئيسي:** `http://localhost:8000`
