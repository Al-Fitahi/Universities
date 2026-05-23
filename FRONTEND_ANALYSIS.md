# Frontend (React) Analysis — UniGuide

A comprehensive analysis of the React/Inertia.js frontend of the **UniGuide** application.

---

## Table of Contents

1. [All Pages / Views / Screens](#1-all-pages--views--screens)
2. [All Components](#2-all-components)
3. [Layouts](#3-layouts)
4. [Routing System](#4-routing-system)
5. [UI Structure for Each Page](#5-ui-structure-for-each-page)
6. [All Forms and Their Fields](#6-all-forms-and-their-fields)
7. [User Flows Between Pages](#7-user-flows-between-pages)
8. [State Management & Context](#8-state-management--context)
9. [Tech Stack Summary](#9-tech-stack-summary)

---

## 1. All Pages / Views / Screens

### Public Pages

| Page | File | Purpose |
|------|------|---------|
| **Home** | `resources/js/pages/Home.tsx` | Landing page — hero section, feature highlights, popular universities |
| **Universities** | `resources/js/pages/Universities.tsx` | Browse all universities with search, rating slider, and location filter |
| **University Details** | `resources/js/pages/UniversityDetails.tsx` | Full detail page for a university — gallery, majors, rating widget, map embed |
| **Colleges** | `resources/js/pages/Colleges.tsx` | Two-stage view: browse colleges/majors → filter universities by selected major |
| **Articles** | `resources/js/pages/Articles.tsx` | Read articles from universities — search, filter by university, sort, like, modal reader |
| **Guidance** | `resources/js/pages/Guidance.tsx` | Multi-step GPA + interest wizard that recommends majors |
| **Apply** | `resources/js/pages/Apply.tsx` | Application form to apply to a university |
| **Not Found** | `resources/js/pages/not-found.tsx` | 404 error page |

### Auth Pages (Laravel Fortify)

| Page | File | Purpose |
|------|------|---------|
| **Login** | `resources/js/pages/auth/login.tsx` | Sign in with email + password |
| **Register** | `resources/js/pages/auth/register.tsx` | Create a new account |
| **Forgot Password** | `resources/js/pages/auth/forgot-password.tsx` | Request a password-reset link via email |
| **Reset Password** | `resources/js/pages/auth/reset-password.tsx` | Set a new password using the reset token |
| **Verify Email** | `resources/js/pages/auth/verify-email.tsx` | Prompt to verify the user's email address |
| **Confirm Password** | `resources/js/pages/auth/confirm-password.tsx` | Re-confirm password before sensitive actions |
| **Two-Factor Challenge** | `resources/js/pages/auth/two-factor-challenge.tsx` | Enter a 6-digit 2FA code during login |

### Authenticated Pages

| Page | File | Purpose |
|------|------|---------|
| **Dashboard** | `resources/js/pages/dashboard.tsx` | Authenticated user home — placeholder pattern cards |
| **Profile Settings** | `resources/js/pages/settings/profile.tsx` | Edit name, email; trigger email verification; delete account |
| **Password Settings** | `resources/js/pages/settings/password.tsx` | Change the current password |
| **Appearance Settings** | `resources/js/pages/settings/appearance.tsx` | Choose theme (Light / Dark / System) |
| **Two-Factor Settings** | `resources/js/pages/settings/two-factor.tsx` | Enable / disable 2FA, view recovery codes |

---

## 2. All Components

### Layout Components

| Component | File | Purpose |
|-----------|------|---------|
| `Layout` | `components/layout/Layout.tsx` | Main wrapper — renders Navbar → `<main>` → Footer |
| `Navbar` | `components/layout/Navbar.tsx` | Sticky top bar with logo, nav links, theme/language switcher, Apply button, mobile menu |
| `Footer` | `components/layout/Footer.tsx` | 4-column footer — brand, quick links, resources, social media |

### Dashboard / App Shell Components

| Component | File | Purpose |
|-----------|------|---------|
| `AppShell` | `components/app-shell.tsx` | Top-level container for the authenticated app view |
| `AppHeader` | `components/app-header.tsx` | Top bar with breadcrumbs and user info |
| `AppContent` | `components/app-content.tsx` | Content-area wrapper inside the app layout |
| `AppSidebar` | `components/app-sidebar.tsx` | Left sidebar with main navigation items |
| `AppSidebarHeader` | `components/app-sidebar-header.tsx` | Sidebar header / logo area |
| `AppLogo` | `components/app-logo.tsx` | Full logo (icon + text) |
| `AppLogoIcon` | `components/app-logo-icon.tsx` | Icon-only logo variant |

### Navigation Components

| Component | File | Purpose |
|-----------|------|---------|
| `NavMain` | `components/nav-main.tsx` | Main navigation menu item list |
| `NavUser` | `components/nav-user.tsx` | User avatar with dropdown menu |
| `NavFooter` | `components/nav-footer.tsx` | Navigation items pinned to the sidebar bottom |
| `Breadcrumbs` | `components/breadcrumbs.tsx` | Breadcrumb trail in the app header |

### User / Account Components

| Component | File | Purpose |
|-----------|------|---------|
| `UserInfo` | `components/user-info.tsx` | Displays user name and avatar |
| `UserMenuContent` | `components/user-menu-content.tsx` | Dropdown menu content (profile, settings, logout) |
| `DeleteUser` | `components/delete-user.tsx` | Confirmation dialog for permanent account deletion |

### Feature / Utility Components

| Component | File | Purpose |
|-----------|------|---------|
| `AppearanceDropdown` | `components/appearance-dropdown.tsx` | Theme-switcher dropdown button |
| `AppearanceTabs` | `components/appearance-tabs.tsx` | Tabbed theme selector (Light / Dark / System) |
| `TwoFactorSetupModal` | `components/two-factor-setup-modal.tsx` | Step-by-step 2FA enable wizard (QR → verify → recovery codes) |
| `TwoFactorRecoveryCodes` | `components/two-factor-recovery-codes.tsx` | Displays recovery codes with copy/download/print actions |
| `Heading` | `components/heading.tsx` | Large section heading |
| `HeadingSmall` | `components/heading-small.tsx` | Smaller section heading |
| `TextLink` | `components/text-link.tsx` | Styled anchor/Inertia link |
| `AlertError` | `components/alert-error.tsx` | Error alert block |

### UI Component Library (60+ shadcn/ui primitives — `components/ui/`)

#### Form Inputs
| Component | File |
|-----------|------|
| `Button` | `ui/button.tsx` |
| `Input` | `ui/input.tsx` |
| `Label` | `ui/label.tsx` |
| `Textarea` | `ui/textarea.tsx` |
| `Checkbox` | `ui/checkbox.tsx` |
| `InputOTP` | `ui/input-otp.tsx` |
| `RadioGroup` | `ui/radio-group.tsx` |
| `Switch` | `ui/switch.tsx` |
| `Slider` | `ui/slider.tsx` |
| `Select` | `ui/select.tsx` |
| `Form` (+ `FormField`, `FormItem`, etc.) | `ui/form.tsx` |
| `Field` | `ui/field.tsx` |
| `InputError` | `ui/input-error.tsx` |
| `InputGroup` | `ui/input-group.tsx` |

#### Layout / Containers
| Component | File |
|-----------|------|
| `Card` (+ `CardHeader`, `CardContent`, etc.) | `ui/card.tsx` |
| `Separator` | `ui/separator.tsx` |
| `Sidebar` | `ui/sidebar.tsx` |
| `Resizable` | `ui/resizable.tsx` |
| `Sheet` | `ui/sheet.tsx` |
| `ScrollArea` | `ui/scroll-area.tsx` |
| `AspectRatio` | `ui/aspect-ratio.tsx` |
| `Carousel` | `ui/carousel.tsx` |

#### Navigation
| Component | File |
|-----------|------|
| `Tabs` | `ui/tabs.tsx` |
| `Accordion` | `ui/accordion.tsx` |
| `NavigationMenu` | `ui/navigation-menu.tsx` |
| `Breadcrumb` | `ui/breadcrumb.tsx` |
| `Pagination` | `ui/pagination.tsx` |

#### Dialogs / Overlays
| Component | File |
|-----------|------|
| `Dialog` | `ui/dialog.tsx` |
| `AlertDialog` | `ui/alert-dialog.tsx` |
| `Drawer` | `ui/drawer.tsx` |
| `Popover` | `ui/popover.tsx` |
| `HoverCard` | `ui/hover-card.tsx` |
| `Tooltip` | `ui/tooltip.tsx` |

#### Menus / Dropdowns
| Component | File |
|-----------|------|
| `DropdownMenu` | `ui/dropdown-menu.tsx` |
| `Command` | `ui/command.tsx` |
| `ContextMenu` | `ui/context-menu.tsx` |
| `Menubar` | `ui/menubar.tsx` |

#### Feedback / Status
| Component | File |
|-----------|------|
| `Toast` | `ui/toast.tsx` |
| `Sonner` | `ui/sonner.tsx` |
| `Alert` | `ui/alert.tsx` |
| `Spinner` | `ui/spinner.tsx` |
| `Progress` | `ui/progress.tsx` |
| `Skeleton` | `ui/skeleton.tsx` |

#### Data Display
| Component | File |
|-----------|------|
| `Table` | `ui/table.tsx` |
| `Avatar` | `ui/avatar.tsx` |
| `Badge` | `ui/badge.tsx` |
| `Empty` | `ui/empty.tsx` |
| `Icon` | `ui/icon.tsx` |
| `Chart` | `ui/chart.tsx` |
| `Calendar` | `ui/calendar.tsx` |
| `PlaceholderPattern` | `ui/placeholder-pattern.tsx` |

#### Other Primitives
| Component | File |
|-----------|------|
| `Toggle` | `ui/toggle.tsx` |
| `ToggleGroup` | `ui/toggle-group.tsx` |
| `ButtonGroup` | `ui/button-group.tsx` |
| `Kbd` | `ui/kbd.tsx` |

---

## 3. Layouts

### `AppLayout` (`layouts/AppLayout.tsx`) — Root Provider Wrapper

Wraps the **entire** application. Provides:
- `QueryClientProvider` (React Query)
- `LanguageProvider` (i18n — English / Arabic)
- `TooltipProvider` (Radix UI)
- `Toaster` (Sonner toast notifications)

### `Layout` (`components/layout/Layout.tsx`) — Public Page Layout

Used by all public pages (Home, Universities, Colleges, Articles, Guidance, Apply).

```
Layout
├── Navbar   (sticky, z-50)
├── <main>   (page content)
└── Footer
```

### `AppSidebarLayout` (`layouts/app/app-sidebar-layout.tsx`) — Dashboard Layout

Used by the authenticated dashboard and settings pages.

```
AppSidebarLayout
├── AppSidebar   (left sidebar with nav items)
└── AppContent
    ├── AppHeader  (breadcrumbs + user info)
    └── <main>     (page content)
```

### Auth Layouts (`layouts/auth/`)

Three variants for authentication pages:

| Layout | File | Usage |
|--------|------|-------|
| `AuthCardLayout` | `auth/auth-card-layout.tsx` | Card-centered form (login, register) |
| `AuthSimpleLayout` | `auth/auth-simple-layout.tsx` | Minimal centered form |
| `AuthSplitLayout` | `auth/auth-split-layout.tsx` | Split view — form on left, image on right |

### Settings Layout (`layouts/settings/layout.tsx`)

Used by all `/settings/*` pages.

```
SettingsLayout
├── Settings Sidebar nav  (Profile / Password / Appearance / Two-Factor)
└── <section>             (settings form content)
```

### Navbar Details (`components/layout/Navbar.tsx`)

- Logo: `GraduationCap` icon + "UniGuide" text
- Desktop nav links: Home · Universities · Colleges · Articles · Smart Guidance
- Actions: Theme toggle (Sun/Moon) · Language selector (Globe → English / العربية) · Apply button
- Mobile: hamburger icon → slide-out menu
- Features: sticky, RTL-aware, dark/light mode, active-link highlighting

### Footer Details (`components/layout/Footer.tsx`)

4-column responsive grid:

1. **Brand** — logo, description, company info
2. **Quick Links** — Home, Universities, Colleges, Articles
3. **Resources** — About, Contact
4. **Social Media** — Facebook, Twitter, Instagram, LinkedIn

---

## 4. Routing System

### Framework: **Inertia.js** (server-driven SPA) with **Laravel** backend

Routing is defined entirely in PHP (`routes/web.php`). Inertia resolves the React component by name on the server and renders it client-side.

**Entry point** (`resources/js/app.tsx`):

```tsx
createInertiaApp({
  resolve: (name) =>
    resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
  setup({ el, App, props }) {
    root.render(
      <StrictMode>
        <AppLayout>
          <App {...props} />
        </AppLayout>
      </StrictMode>
    );
  },
});
```

### Public Routes

| Method | URL | Inertia Page |
|--------|-----|-------------|
| `GET` | `/` | `Home` |
| `GET` | `/universities` | `Universities` |
| `GET` | `/universities/{university}` | `UniversityDetails` |
| `GET` | `/colleges` | `Colleges` |
| `GET` | `/guidance` | `Guidance` |
| `GET` | `/articles` | `Articles` |
| `GET` | `/apply` | `Apply` |
| `POST` | `/applications` | (form submission) |
| `POST` | `/universities/{id}/rate` | (authenticated) |

### Auth Routes (Laravel Fortify)

| Method | URL | Inertia Page |
|--------|-----|-------------|
| `GET` | `/login` | `auth/login` |
| `GET` | `/register` | `auth/register` |
| `GET` | `/forgot-password` | `auth/forgot-password` |
| `GET` | `/reset-password/{token}` | `auth/reset-password` |
| `GET` | `/verify-email` | `auth/verify-email` |
| `GET` | `/confirm-password` | `auth/confirm-password` |
| `POST` | `/two-factor-challenge` | `auth/two-factor-challenge` |

### Authenticated Routes (middleware: `auth`, `verified`)

| Method | URL | Inertia Page |
|--------|-----|-------------|
| `GET` | `/dashboard` | `dashboard` |
| `GET` | `/settings/profile` | `settings/profile` |
| `GET` | `/settings/password` | `settings/password` |
| `GET` | `/settings/appearance` | `settings/appearance` |
| `GET` | `/settings/two-factor` | `settings/two-factor` |

### Client-side Navigation

```tsx
// Inertia Link — SPA navigation (no full page reload)
<Link href="/universities">Universities</Link>

// Programmatic navigation
import { router } from '@inertiajs/react';
router.get('/path');
router.post('/path', data, { onSuccess: () => { /* ... */ } });
```

---

## 5. UI Structure for Each Page

### `Home.tsx`

```
AppLayout → Layout
└── Hero Section                        (framer-motion fade-in)
│   ├── Headline + subtitle             (bilingual via LanguageContext)
│   ├── Button "Explore Universities"   → /universities
│   └── Button "Explore Colleges"       → /colleges
├── Features Section                    (3-column card grid)
│   ├── Card: Find Universities         → /universities
│   ├── Card: Smart Guidance            → /guidance
│   └── Card: Articles & Insights       → /articles
└── Popular Universities Section        (3-column grid)
    └── University Card ×3
        ├── Image
        ├── Name, description, location
        ├── Rating badge
        ├── Fees display
        └── Button "View Details"       → /universities/{id}
```

**Dependencies:** framer-motion, LanguageContext, mockData

---

### `Universities.tsx`

```
AppLayout → Layout
└── Page Header
├── Sidebar Filters
│   ├── Rating slider   (0–5 stars)
│   └── Location select dropdown
└── Main Content
    ├── Search input    (real-time filtering)
    ├── Sort dropdown   (Rating / Fees / Name)
    └── University Cards (2-column grid)
        ├── Image
        ├── Name + star rating
        ├── Description
        ├── Address with MapPin icon
        ├── Fees
        └── Button "View Details"       → /universities/{id}
```

**Backend props:** `universitiesData: University[]`

---

### `UniversityDetails.tsx`

```
AppLayout → Layout
└── University Header
│   ├── Background image banner
│   ├── Logo avatar
│   ├── Name, type badge, rating
│   ├── Contact info (phone, email, address)
│   ├── Interactive 5-star rating widget  (auth required)
│   ├── Button "Apply"                    → /apply
│   └── Button "Contact"
├── Image Gallery / Carousel
├── Colleges & Majors (Accordion)
│   └── College Item (expandable)
│       └── Major Cards
│           ├── Name, description
│           ├── Duration (years)
│           ├── Min GPA requirement
│           ├── Career opportunities
│           └── Button "Apply"            → /apply
├── About Section
├── Admissions Info
├── Map Embed
└── Articles / Reviews Section
```

**Backend props:** `universityData: UniversityData` (includes `images[]`, `universityMajors[]`)

---

### `Colleges.tsx`

**Stage 1 — Browse Colleges**

```
AppLayout → Layout
└── Accordion
    └── College Item (expandable) ×N
        ├── College image
        ├── College name + major count
        └── Major Cards (3-column grid) ×N
            ├── Major name
            ├── Description
            ├── Duration / GPA / careers
            └── Button "View Unis & Apply"  → Stage 2
```

**Stage 2 — Universities for Selected Major**

```
└── Back button                           → Stage 1
├── Selected Major Info Card
│   ├── Major name, description
│   └── Duration, min GPA
├── Filters Sidebar
│   ├── Search input
│   └── Sort: Lowest Fees / Highest Rating
└── University Cards (2-column grid)
    ├── Image with rating badge
    ├── University name + location
    ├── Fees for this major
    └── Button "Apply"                    → /apply/{uniId}?major={majorId}
```

---

### `Guidance.tsx` — Multi-Step Wizard

**Step 1 — GPA Input**

```
Card
├── Title "What is your High School GPA?"
├── Number input (range 0–5, step 0.1)
└── Button "Next"   (disabled until GPA > 0)
```

**Step 2 — Interests**

```
Card
├── Title "What are you interested in?"
├── Checkbox grid (2 columns)
│   ├── Technology & Coding
│   ├── Healthcare & Medicine
│   ├── Design & Arts
│   ├── Business & Management
│   └── Engineering & Construction
├── Button "Back"                         → Step 1
└── Button "See Results"   (disabled until ≥ 1 selected)
```

**Step 3 — Recommendations**

```
├── GPA + Interests summary
├── Recommended Major Cards (2-column grid)
│   ├── Name, description
│   ├── Badge "Recommended"
│   ├── Badge: duration in years
│   └── Button "View Offering Universities"
├── Empty state (if no matches)
└── Button "Start Over"                   → Step 1
```

Animated transitions via **framer-motion** between steps.

---

### `Apply.tsx`

```
AppLayout → Layout
└── Page Header
└── Card "Student Application Form"
    └── React Hook Form (Zod validation)
        ├── First Name input
        ├── Last Name input
        ├── Email input
        ├── Phone input
        ├── University select dropdown
        ├── GPA number input
        └── Button "Submit Application"
```

---

### `Articles.tsx`

```
AppLayout → Layout
└── Page Header
├── Filter/Search Toolbar
│   ├── Search input
│   ├── University filter dropdown
│   └── Sort dropdown (Newest / Oldest / Popular)
└── Article Cards (3-column grid)
    ├── University avatar + name + date
    ├── Article image
    ├── Title
    ├── Content preview
    └── Footer
        ├── Like button + count
        └── Button "Read More"            → Modal dialog

Modal Dialog
├── Article cover image
├── Article title
└── Article content (scrollable)
```

**Backend props:** `articlesData: Article[]`

---

### `dashboard.tsx`

```
AppSidebarLayout
├── AppSidebar (navigation)
└── AppContent
    ├── AppHeader (breadcrumbs)
    └── Main content area
        └── PlaceholderPattern cards  (placeholder UI)
```

---

### Settings Pages

All settings pages share the `SettingsLayout` with sidebar navigation.

```
SettingsLayout
├── Settings Sidebar
│   ├── Profile          → /settings/profile
│   ├── Password         → /settings/password
│   ├── Appearance       → /settings/appearance
│   └── Two-Factor Auth  → /settings/two-factor
└── Content Area  (see forms below)
```

---

## 6. All Forms and Their Fields

### Login Form

```
Action:  POST /login  (Laravel Fortify)
Layout:  AuthCardLayout

Fields:
  email     <Input type="email"    name="email"    placeholder="email@example.com" />
  password  <Input type="password" name="password" placeholder="Password" />
  remember  <Checkbox name="remember" />

Submit: Button "Log in"
Links:  "Forgot your password?" → /forgot-password
        "Sign up" → /register
```

---

### Register Form

```
Action:  POST /register  (Laravel Fortify)
Layout:  AuthCardLayout

Fields:
  name                  <Input type="text"     name="name"     placeholder="Full name" />
  email                 <Input type="email"    name="email"    placeholder="email@example.com" />
  password              <Input type="password" name="password" placeholder="Password" />
  password_confirmation <Input type="password" name="password_confirmation" placeholder="Confirm password" />

Submit: Button "Create account"
Links:  "Already have an account?" → /login
```

---

### Forgot Password Form

```
Action:  POST /forgot-password

Fields:
  email  <Input type="email" name="email" placeholder="email@example.com" />

Submit: Button "Email password reset link"
Links:  "Return to log in" → /login
```

---

### Reset Password Form

```
Action:  POST /reset-password

Fields:
  email                 <Input type="email"    value={email} readOnly />
  password              <Input type="password" name="password" placeholder="Password" />
  password_confirmation <Input type="password" name="password_confirmation" placeholder="Confirm password" />

Submit: Button "Reset password"
```

---

### Two-Factor Challenge Form

```
Action:  POST /two-factor-challenge

Fields:
  code  <InputOTP maxLength={6} />  (6-digit one-time code)

Submit: Button "Verify"
```

---

### Confirm Password Form

```
Action:  POST /confirm-password

Fields:
  password  <Input type="password" name="password" placeholder="Password" />

Submit: Button "Confirm"
```

---

### Application Form (`Apply.tsx`)

```
Action:  POST /applications  (Inertia router)
Validation: Zod schema

Fields:
  firstName    <Input placeholder="John" />                     min 2 chars
  lastName     <Input placeholder="Doe" />                      min 2 chars
  email        <Input type="email" placeholder="john.doe@example.com" />  valid email
  phone        <Input placeholder="+966 5X XXX XXXX" />         min 10 digits
  universityId <Select>                                          required
                 <SelectItem> per university
               </Select>
  gpa          <Input type="number" step="0.01" placeholder="4.5" />  0–5 range

Layout: 2-column grid for firstName / lastName; full-width for other fields

Submit: Button "Submit Application" (full-width)
Error display: <FormMessage> under each field
Success: Toast notification + form reset
```

---

### Profile Settings Form

```
Action:  PUT /profile  (ProfileController.update)

Fields:
  name   <Input id="name"  defaultValue={user.name}  placeholder="Full name" />
  email  <Input type="email" id="email" defaultValue={user.email} placeholder="email@example.com" />

Extras:
  - "Resend verification email" button (shown when email is unverified)
  - "Save" button
  - "Recently saved" indicator after success

Error display: per-field error messages from Laravel validation
```

---

### Password Settings Form

```
Action:  PUT /password  (PasswordController.update)

Fields:
  current_password      <Input type="password" placeholder="Current password" />
  password              <Input type="password" placeholder="New password" />
  password_confirmation <Input type="password" placeholder="Confirm password" />

Submit: Button "Update password"
Error display: per-field error messages from Laravel validation
```

---

### Appearance Settings

```
Storage: localStorage (no server submission)

Options:
  theme  Radio / tab selector: "light" | "dark" | "system"

Real-time preview, system-preference detection, persisted to localStorage.
```

---

### Two-Factor Setup (Modal)

```
Step 1 — QR Code
  Display QR code (or manual secret key)

Step 2 — Verify
  code  <InputOTP maxLength={6} />

Step 3 — Recovery Codes
  Read-only list of one-time recovery codes
  Actions: Copy to clipboard, Download, Print
```

---

## 7. User Flows Between Pages

### Unauthenticated / Public Flow

```
/ (Home)
├─ Navbar → /universities
├─ Navbar → /colleges
├─ Navbar → /articles
├─ Navbar → /guidance
├─ Hero "Explore Universities" → /universities
├─ Hero "Explore Colleges" → /colleges
├─ Feature card → /universities
├─ Feature card → /guidance
├─ Feature card → /articles
└─ University card "View Details" → /universities/{id}

/universities
├─ Filters (search, rating slider, location) — in-page only
└─ University card "View Details" → /universities/{id}

/universities/{id}
├─ "Apply" button → /apply
├─ Major "Apply" button → /apply
└─ Rating widget  (requires login → redirect to /login)

/colleges
├─ Stage 1: Major card "View Unis & Apply" → Stage 2 (in-page)
├─ Stage 2: "Back" button → Stage 1 (in-page)
└─ Stage 2: University "Apply" → /apply?universityId=X&majorId=Y

/guidance
├─ Step 1 → Step 2 (in-page, framer-motion)
├─ Step 2 → Step 1 (Back)
├─ Step 2 → Step 3 (See Results)
├─ Step 3 → /colleges (View Offering Universities)
└─ Step 3 → Step 1 (Start Over)

/articles
├─ Filter / search — in-page only
├─ Like — in-page toggle
└─ "Read More" → Modal dialog (in-page)

/apply
└─ Submit → Toast + form reset  (stays on /apply)
```

---

### Authentication Flow

```
/login
├─ Submit → /dashboard (on success)
├─ 2FA enabled → /two-factor-challenge
├─ "Forgot password?" → /forgot-password
└─ "Sign up" → /register

/register
├─ Submit → /verify-email (if email verification required)
└─ "Log in" → /login

/forgot-password
├─ Submit → Success message (email sent)
└─ "Return to log in" → /login

/reset-password/{token}
└─ Submit → /login (on success)

/two-factor-challenge
└─ Submit → /dashboard (on success)

/verify-email
└─ "Resend link" → re-sends verification email
   Verified → /dashboard
```

---

### Authenticated Flow

```
/dashboard
├─ Sidebar → /settings/profile
├─ Sidebar → /settings/password
├─ Sidebar → /settings/appearance
├─ Sidebar → /settings/two-factor
└─ Sidebar → Logout → /login

/settings/profile
├─ Save → stays on /settings/profile (success toast)
└─ "Delete account" → confirmation dialog → /login

/settings/password
└─ Update → stays on /settings/password (success toast)

/settings/appearance
└─ Toggle theme → instant preview (localStorage)

/settings/two-factor
├─ Enable 2FA → TwoFactorSetupModal
│   ├─ Scan QR / enter secret
│   ├─ Verify OTP code
│   └─ Save recovery codes
└─ Disable 2FA → confirmation dialog
```

---

## 8. State Management & Context

### `LanguageContext` (`contexts/LanguageContext.tsx`)

Manages the global language and text direction.

```tsx
interface LanguageContextType {
  language: 'en' | 'ar';
  direction: 'ltr' | 'rtl';
  setLanguage: (lang: 'en' | 'ar') => void;
  t: (key: string) => string;  // translation function
}
```

- Sets `document.documentElement.dir` to `'rtl'`/`'ltr'`
- Sets `document.documentElement.lang`
- Provides 34+ bilingual translation keys
- Used in: `Navbar`, `Footer`, `Home`, `Universities`, `Colleges`, `Guidance`, `Apply`, `Articles`

### React Query (`lib/queryClient.ts`)

```tsx
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      refetchOnReconnect: false,
    },
  },
});
```

Wrapped in `QueryClientProvider` inside `AppLayout`.

### Custom Hooks

| Hook | File | Purpose |
|------|------|---------|
| `useLanguage()` | `contexts/LanguageContext.tsx` | Language, direction, `t()` function |
| `useToast()` | `hooks/use-toast.ts` | Show toast notifications |
| `useAppearance()` | `hooks/use-appearance.tsx` | Theme management (localStorage) |
| `useMobileNavigation()` | `hooks/use-mobile-navigation.ts` | Mobile menu open/close state |
| `useClipboard()` | `hooks/use-clipboard.ts` | Copy text to clipboard |
| `useInitials()` | `hooks/use-initials.tsx` | Generate initials from a name string |
| `useActiveUrl()` | `hooks/use-active-url.ts` | Detect current active route |
| `useMobile()` | `hooks/use-mobile.tsx` | Detect mobile viewport breakpoint |
| `useTwoFactorAuth()` | `hooks/use-two-factor-auth.ts` | 2FA enable/disable/verify actions |

### Inertia.js Shared Props

Backend passes these props via `HandleInertiaRequests` middleware:

```tsx
auth: {
  user: {
    id, name, email,
    email_verified_at,
    two_factor_enabled,
  } | null
}
```

---

## 9. Tech Stack Summary

| Category | Library / Tool | Version |
|----------|---------------|---------|
| **UI Framework** | React | ^19.2.0 |
| **SPA Adapter** | @inertiajs/react | ^2.3.7 |
| **Styling** | Tailwind CSS | ^4.0.0 |
| **UI Primitives** | Radix UI (17+ packages) | latest |
| **Form Management** | react-hook-form | ^7.71.1 |
| **Validation** | Zod | ^4.3.5 |
| **Form Resolvers** | @hookform/resolvers | ^5.2.2 |
| **Server State** | @tanstack/react-query | ^5.90.19 |
| **Animations** | framer-motion | ^12.28.1 |
| **Icons** | lucide-react | ^0.475.0 |
| **Toasts** | sonner | ^2.0.7 |
| **OTP Input** | input-otp | ^1.4.2 |
| **Classnames** | clsx + tailwind-merge | ^2.1.1 / ^3.0.1 |
| **Build Tool** | Vite | ^7.0.4 |
| **Language** | TypeScript | ^5.7.2 |
| **Linter** | ESLint | ^9.17.0 |
| **Formatter** | Prettier | ^3.4.2 |
