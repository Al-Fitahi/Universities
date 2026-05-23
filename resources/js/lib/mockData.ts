export interface University {
  id: string;
  name: string;
  nameAr: string;
  location: string;
  locationAr: string;
  rating: number;
  fees: number;
  image: string;
  logo: string;
  description: string;
  descriptionAr: string;
  mapUrl?: string;
  streetName?: string;
  streetNameAr?: string;
  landmark?: string;
  landmarkAr?: string;
}

export interface College {
  id: string;
  name: string;
  nameAr: string;
  image: string;
  majors: Major[];
}

export interface Major {
  id: string;
  name: string;
  nameAr: string;
  collegeId: string;
  description: string;
  descriptionAr: string;
  years: number;
  fees: number;
  gpa: number;
  careerOpportunities?: string[];
  careerOpportunitiesAr?: string[];
}

export interface GraduationProject {
  id: string;
  name: string;
  nameAr: string;
  collegeName: string;
  collegeNameAr: string;
  majorName: string;
  majorNameAr: string;
  supervisorName: string;
  supervisorNameAr: string;
  memberNames: string[];
  memberNamesAr: string[];
  description: string;
  descriptionAr: string;
  date: string;
  pdfLink: string;
  universityId: string;
  universityName: string;
  universityNameAr: string;
  projectType: "Research" | "Application" | "Hardware";
  projectTypeAr: "بحث" | "تطبيق" | "عتاد";
}

import uniImage1 from "@assets/generated_images/classic_university_building.png";
import uniImage2 from "@assets/generated_images/high-tech_engineering_campus.png";
import uniImage3 from "@assets/generated_images/modern_university_campus_hero.png";

export const universities: University[] = [
  {
    id: "1",
    name: "King Saud University",
    nameAr: "جامعة الملك سعود",
    location: "Riyadh, Saudi Arabia",
    locationAr: "الرياض، المملكة العربية السعودية",
    streetName: "King Khalid Road",
    streetNameAr: "طريق الملك خالد",
    landmark: "Near Diplomatic Quarter",
    landmarkAr: "بالقرب من الحي الدبلوماسي",
    rating: 4.8,
    fees: 0,
    image: uniImage1,
    logo: "https://images.unsplash.com/photo-1599305090598-fe179d501227?w=200&h=200&fit=crop",
    description:
      "A premier public university in Riyadh, known for its extensive research programs.",
    descriptionAr:
      "جامعة حكومية رائدة في الرياض، تشتهر ببرامجها البحثية المكثفة.",
    mapUrl:
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m13!1d3623.238622147317!2d46.61904127595204!3d24.717013350222033!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f1cd8f8373f73%3A0xc665e12815197f26!2sKing%20Saud%20University!5e0!3m2!1sen!2ssa!4v1705574400000!5m2!1sen!2ssa",
  },
  {
    id: "2",
    name: "King Fahd University of Petroleum & Minerals",
    nameAr: "جامعة الملك فهد للبترول والمعادن",
    location: "Dhahran, Saudi Arabia",
    locationAr: "الظهران، المملكة العربية السعودية",
    streetName: "Academic Campus Road",
    streetNameAr: "طريق الحرم الأكاديمي",
    landmark: "Next to Saudi Aramco",
    landmarkAr: "بجوار شركة أرامكو السعودية",
    rating: 4.9,
    fees: 0,
    image: uniImage2,
    logo: "https://images.unsplash.com/photo-1599305090598-fe179d501227?w=200&h=200&fit=crop",
    description:
      "Specialized in science, engineering, and management. A hub for innovation.",
    descriptionAr: "متخصصة في العلوم والهندسة والإدارة. مركز للابتكار.",
    mapUrl:
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m12!1m3!1d3579.526274474327!2d50.1346083!3d26.3117407!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e49e917d1217f25%3A0xa976b9f298816f0e!2sKing%20Fahd%20University%20of%20Petroleum%20and%20Minerals!5e0!3m2!1sen!2ssa!4v1705574500000!5m2!1sen!2ssa",
  },
  {
    id: "3",
    name: "Alfaisal University",
    nameAr: "جامعة الفيصل",
    location: "Riyadh, Saudi Arabia",
    locationAr: "الرياض، المملكة العربية السعودية",
    streetName: "Al Takhassusi Street",
    streetNameAr: "شارع التخصصي",
    landmark: "Near King Faisal Specialist Hospital",
    landmarkAr: "بالقرب من مستشفى الملك فيصل التخصصي",
    rating: 4.5,
    fees: 80000,
    image: uniImage3,
    logo: "https://images.unsplash.com/photo-1599305090598-fe179d501227?w=200&h=200&fit=crop",
    description:
      "A private non-profit university offering world-class education.",
    descriptionAr: "جامعة خاصة غير ربحية تقدم تعليماً بمستوى عالمي.",
    mapUrl:
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m12!1m3!1d3624.471556858102!2d46.6713847!3d24.6896264!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f036720e3d555%3A0x6b490f29637c35a8!2sAlfaisal%20University!5e0!3m2!1sen!2ssa!4v1705574600000!5m2!1sen!2ssa",
  },
  {
    id: "4",
    name: "King Abdulaziz University",
    nameAr: "جامعة الملك عبدالعزيز",
    location: "Jeddah, Saudi Arabia",
    locationAr: "جدة، المملكة العربية السعودية",
    streetName: "Al Ehtifalat St",
    streetNameAr: "شارع الاحتفالات",
    landmark: "Near Al-Sulaymaniyah",
    landmarkAr: "بالقرب من السليمانية",
    rating: 4.7,
    fees: 0,
    image:
      "https://images.unsplash.com/photo-1562774053-701939374585?w=800&auto=format&fit=crop",
    logo: "https://images.unsplash.com/photo-1599305090598-fe179d501227?w=200&h=200&fit=crop",
    description:
      "A leading public university located in Jeddah, offering a wide range of programs.",
    descriptionAr:
      "جامعة حكومية رائدة تقع في جدة، تقدم مجموعة واسعة من البرامج.",
    mapUrl:
      "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d118933.27976865807!2d39.30907157644265!3d21.494793540250005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x15c3d01fb1137e59%3A0xe059579737b118ab!2sKing%20Abdulaziz%20University!5e0!3m2!1sen!2ssa!4v1705574700000!5m2!1sen!2ssa",
  },
  {
    id: "5",
    name: "Prince Sultan University",
    nameAr: "جامعة الأمير سلطان",
    location: "Riyadh, Saudi Arabia",
    locationAr: "الرياض، المملكة العربية السعودية",
    streetName: "Rafha Street",
    streetNameAr: "شارع رفحاء",
    landmark: "Salah Ad Din",
    landmarkAr: "صلاح الدين",
    rating: 4.6,
    fees: 75000,
    image:
      "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&auto=format&fit=crop",
    logo: "https://images.unsplash.com/photo-1599305090598-fe179d501227?w=200&h=200&fit=crop",
    description:
      "A prominent private university in Riyadh focused on business and technology.",
    descriptionAr: "جامعة خاصة بارزة في الرياض تركز على الأعمال والتكنولوجيا.",
  },
];

export const colleges: College[] = [
  {
    id: "c1",
    name: "College of Engineering",
    nameAr: "كلية الهندسة",
    image: uniImage2,
    majors: [],
  },
  {
    id: "c2",
    name: "College of Medicine",
    nameAr: "كلية الطب",
    image: uniImage3,
    majors: [],
  },
  {
    id: "c3",
    name: "College of Computer Science",
    nameAr: "كلية علوم الحاسب",
    image: uniImage2,
    majors: [],
  },
];

export const majors: Major[] = [
  {
    id: "m1",
    name: "Software Engineering",
    nameAr: "هندسة البرمجيات",
    collegeId: "c3",
    description: "Learn to design and build complex software systems.",
    descriptionAr: "تعلم تصميم وبناء أنظمة البرمجيات المعقدة.",
    years: 4,
    fees: 80000,
    gpa: 4.5,
    careerOpportunities: [
      "Software Developer",
      "Systems Architect",
      "Project Manager",
      "Data Scientist",
      "DevOps Engineer",
      "Cybersecurity Analyst",
    ],
    careerOpportunitiesAr: [
      "مطور برمجيات",
      "معماري أنظمة",
      "مدير مشاريع",
      "عالم بيانات",
      "مهندس عمليات التطوير",
      "محلل أمن سيبراني",
    ],
  },
  {
    id: "m2",
    name: "Civil Engineering",
    nameAr: "الهندسة المدنية",
    collegeId: "c1",
    description: "Design and oversee construction of infrastructure projects.",
    descriptionAr: "تصميم والإشراف على بناء مشاريع البنية التحتية.",
    years: 4,
    fees: 75000,
    gpa: 4.0,
    careerOpportunities: [
      "Civil Engineer",
      "Structural Designer",
      "Site Manager",
      "Urban Planner",
      "Geotechnical Engineer",
    ],
    careerOpportunitiesAr: [
      "مهندس مدني",
      "مصمم إنشائي",
      "مدير موقع",
      "مخطط حضري",
      "مهندس جيوتقني",
    ],
  },
  {
    id: "m3",
    name: "Medicine",
    nameAr: "الطب البشري",
    collegeId: "c2",
    description: "Prepare for a career in healthcare and patient treatment.",
    descriptionAr: "الاستعداد لمهنة في مجال الرعاية الصحية وعلاج المرضى.",
    years: 7,
    fees: 120000,
    gpa: 4.8,
    careerOpportunities: [
      "Physician",
      "Surgeon",
      "Medical Researcher",
      "Hospital Administrator",
      "Medical Consultant",
    ],
    careerOpportunitiesAr: [
      "طبيب عام",
      "جراح",
      "باحث طبي",
      "مدير مستشفى",
      "مستشار طبي",
    ],
  },
  {
    id: "m4",
    name: "Computer Science",
    nameAr: "علوم الحاسب",
    collegeId: "c3",
    description: "Study of computation, algorithms, and data structures.",
    descriptionAr: "دراسة الحوسبة والخوارزميات وهياكل البيانات.",
    years: 4,
    fees: 75000,
    gpa: 4.2,
    careerOpportunities: [
      "Software Engineer",
      "Data Analyst",
      "Database Administrator",
      "Network Engineer",
    ],
    careerOpportunitiesAr: [
      "مهندس برمجيات",
      "محلل بيانات",
      "مسؤول قواعد بيانات",
      "مهندس شبكات",
    ],
  },
  {
    id: "m5",
    name: "Artificial Intelligence",
    nameAr: "الذكاء الاصطناعي",
    collegeId: "c3",
    description: "Advanced studies in machine learning and AI systems.",
    descriptionAr: "دراسات متقدمة في تعلم الآلة وأنظمة الذكاء الاصطناعي.",
    years: 4,
    fees: 85000,
    gpa: 4.6,
    careerOpportunities: [
      "AI Engineer",
      "Machine Learning Researcher",
      "Robotics Engineer",
      "NLP Specialist",
    ],
    careerOpportunitiesAr: [
      "مهندس ذكاء اصطناعي",
      "باحث تعلم آلة",
      "مهندس روبوتات",
      "أخصائي معالجة لغات طبيعية",
    ],
  },
  {
    id: "m6",
    name: "Electrical Engineering",
    nameAr: "الهندسة الكهربائية",
    collegeId: "c1",
    description:
      "Study of electronics, electromagnetism, and electrical systems.",
    descriptionAr: "دراسة الإلكترونيات والكهرومغناطيسية والأنظمة الكهربائية.",
    years: 4,
    fees: 78000,
    gpa: 4.3,
    careerOpportunities: [
      "Electrical Engineer",
      "Electronics Designer",
      "Power Systems Engineer",
      "Control Systems Engineer",
    ],
    careerOpportunitiesAr: [
      "مهندس كهربائي",
      "مصمم إلكترونيات",
      "مهندس أنظمة طاقة",
      "مهندس أنظمة تحكم",
    ],
  },
  {
    id: "m7",
    name: "Dentistry",
    nameAr: "طب الأسنان",
    collegeId: "c2",
    description: "Diagnosis, prevention, and treatment of oral diseases.",
    descriptionAr: "تشخيص والوقاية وعلاج أمراض الفم والأسنان.",
    years: 6,
    fees: 110000,
    gpa: 4.7,
    careerOpportunities: [
      "Dentist",
      "Orthodontist",
      "Oral Surgeon",
      "Dental Researcher",
    ],
    careerOpportunitiesAr: [
      "طبيب أسنان",
      "أخصائي تقويم",
      "جراح فم",
      "باحث في طب الأسنان",
    ],
  },
  {
    id: "m8",
    name: "Architecture",
    nameAr: "العمارة",
    collegeId: "c1",
    description: "Art and science of designing buildings and structures.",
    descriptionAr: "فن وعلم تصميم المباني والهياكل.",
    years: 5,
    fees: 80000,
    gpa: 4.4,
    careerOpportunities: [
      "Architect",
      "Interior Designer",
      "Urban Designer",
      "Landscape Architect",
    ],
    careerOpportunitiesAr: [
      "مهندس معماري",
      "مصمم داخلي",
      "مصمم حضري",
      "مهندس مناظر طبيعية",
    ],
  },
  {
    id: "m9",
    name: "Information Systems",
    nameAr: "نظم المعلومات",
    collegeId: "c3",
    description:
      "Study of software and hardware systems to support business operations.",
    descriptionAr: "دراسة أنظمة البرامج والأجهزة لدعم العمليات التجارية.",
    years: 4,
    fees: 70000,
    gpa: 4.0,
    careerOpportunities: [
      "Systems Analyst",
      "IT Consultant",
      "Business Analyst",
      "IT Project Manager",
    ],
    careerOpportunitiesAr: [
      "محلل نظم",
      "مستشار تقنية معلومات",
      "محلل أعمال",
      "مدير مشاريع تقنية",
    ],
  },
  {
    id: "m10",
    name: "Pharmacy",
    nameAr: "الصيدلة",
    collegeId: "c2",
    description: "Science of preparing and dispensing medical drugs.",
    descriptionAr: "علم تحضير وصرف الأدوية الطبية.",
    years: 5,
    fees: 95000,
    gpa: 4.6,
    careerOpportunities: [
      "Pharmacist",
      "Clinical Pharmacist",
      "Pharmaceutical Researcher",
      "Medical Writer",
    ],
    careerOpportunitiesAr: ["صيدلي", "صيدلي سريري", "باحث صيدلاني", "كاتب طبي"],
  },
];

// Link majors to colleges
colleges[0].majors = [majors[1], majors[5], majors[7]]; // Engineering: Civil, Electrical, Architecture
colleges[1].majors = [majors[2], majors[6], majors[9]]; // Medicine: Medicine, Dentistry, Pharmacy
colleges[2].majors = [majors[0], majors[3], majors[4], majors[8]]; // CS: SE, CS, AI, IS

export const graduationProjects: GraduationProject[] = [
  {
    id: "gp1",
    name: "AI-Powered University Guidance System",
    nameAr: "نظام توجيه جامعي مدعوم بالذكاء الاصطناعي",
    collegeName: "College of Computer Science",
    collegeNameAr: "كلية علوم الحاسب",
    majorName: "Software Engineering",
    majorNameAr: "هندسة البرمجيات",
    supervisorName: "Dr. Ahmed Al-Malki",
    supervisorNameAr: "د. أحمد المالكي",
    memberNames: ["Mohammed Al-Otaibi", "Omar Al-Ghamdi"],
    memberNamesAr: ["محمد العتيبي", "عمر الغامدي"],
    description:
      "A platform that uses AI to recommend suitable majors for high school students based on their skills and interests.",
    descriptionAr:
      "منصة تستخدم الذكاء الاصطناعي لاقتراح التخصصات المناسبة لطلاب الثانوية بناءً على مهاراتهم واهتماماتهم.",
    date: "2025-12-01",
    pdfLink: "#",
    universityId: "1",
    universityName: "King Saud University",
    universityNameAr: "جامعة الملك سعود",
    projectType: "Application",
    projectTypeAr: "تطبيق",
  },
  {
    id: "gp2",
    name: "Smart Traffic Management for Smart Cities",
    nameAr: "إدارة المرور الذكية للمدن الذكية",
    collegeName: "College of Engineering",
    collegeNameAr: "كلية الهندسة",
    majorName: "Civil Engineering",
    majorNameAr: "الهندسة المدنية",
    supervisorName: "Dr. Faisal Al-Harbi",
    supervisorNameAr: "د. فيصل الحربي",
    memberNames: [
      "Khalid Al-Dossari",
      "Saad Al-Shahrani",
      "Abdullah Al-Qahtani",
    ],
    memberNamesAr: ["خالد الدوسري", "سعد الشهراني", "عبدالله القحطاني"],
    description:
      "Designing a smart traffic light system that adapts to real-time traffic flow to minimize congestion in major cities.",
    descriptionAr:
      "تصميم نظام إشارات مرور ذكي يتكيف مع تدفق حركة المرور في الوقت الفعلي لتقليل الازدحام في المدن الكبرى.",
    date: "2026-04-15",
    pdfLink: "#",
    universityId: "2",
    universityName: "KFUPM",
    universityNameAr: "جامعة الملك فهد",
    projectType: "Research",
    projectTypeAr: "بحث",
  },
  {
    id: "gp3",
    name: "Automated Medical Diagnostic Tool",
    nameAr: "أداة تشخيص طبي آلية",
    collegeName: "College of Medicine",
    collegeNameAr: "كلية الطب",
    majorName: "Medicine",
    majorNameAr: "الطب",
    supervisorName: "Dr. Sara Al-Hassan",
    supervisorNameAr: "د. سارة الحسن",
    memberNames: ["Noura Al-Saud", "Reem Al-Qahtani"],
    memberNamesAr: ["نورة آل سعود", "ريم القحطاني"],
    description:
      "A hardware and software solution that assists doctors in diagnosing common illnesses quickly and accurately using sensor data.",
    descriptionAr:
      "حل للأجهزة والبرمجيات يساعد الأطباء في تشخيص الأمراض الشائعة بسرعة وبدقة باستخدام بيانات المستشعرات.",
    date: "2025-10-20",
    pdfLink: "#",
    universityId: "3",
    universityName: "Alfaisal University",
    universityNameAr: "جامعة الفيصل",
    projectType: "Hardware",
    projectTypeAr: "عتاد",
  },
];
