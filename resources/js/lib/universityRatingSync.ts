export interface UniversityRatingUpdate {
  id: string;
  rating: number;
  ratingCount?: number;
  userRating?: number | null;
  updatedAt: number;
}

interface UniversityWithRating {
  id: string;
  rating: number;
  ratingCount?: number;
  userRating?: number | null;
}

const STORAGE_KEY = "queen:university-rating-overrides";
const EVENT_NAME = "queen:university-rating-updated";

function canUseBrowserStorage(): boolean {
  return typeof window !== "undefined" && typeof window.localStorage !== "undefined";
}

function readOverrides(): Record<string, UniversityRatingUpdate> {
  if (!canUseBrowserStorage()) {
    return {};
  }

  try {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (!raw) {
      return {};
    }

    const parsed = JSON.parse(raw) as Record<string, UniversityRatingUpdate>;
    return parsed && typeof parsed === "object" ? parsed : {};
  } catch {
    return {};
  }
}

function writeOverrides(overrides: Record<string, UniversityRatingUpdate>): void {
  if (!canUseBrowserStorage()) {
    return;
  }

  try {
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(overrides));
  } catch {
    // Ignore storage write failures.
  }
}

export function applyUniversityRatingOverrides<T extends UniversityWithRating>(
  universities: T[],
): T[] {
  const overrides = readOverrides();

  return universities.map((university) => {
    const override = overrides[String(university.id)];
    if (!override) {
      return university;
    }

    return {
      ...university,
      rating: override.rating,
      ratingCount: override.ratingCount ?? university.ratingCount,
      userRating: override.userRating ?? university.userRating,
    };
  });
}

export function publishUniversityRatingUpdate(update: Omit<UniversityRatingUpdate, "updatedAt">): void {
  if (typeof window === "undefined") {
    return;
  }

  const payload: UniversityRatingUpdate = {
    ...update,
    id: String(update.id),
    updatedAt: Date.now(),
  };

  const overrides = readOverrides();
  overrides[payload.id] = payload;
  writeOverrides(overrides);

  window.dispatchEvent(new CustomEvent<UniversityRatingUpdate>(EVENT_NAME, { detail: payload }));
}

export function subscribeToUniversityRatingUpdates(
  callback: (update: UniversityRatingUpdate) => void,
): () => void {
  if (typeof window === "undefined") {
    return () => undefined;
  }

  const onCustomEvent = (event: Event) => {
    const customEvent = event as CustomEvent<UniversityRatingUpdate>;
    if (customEvent.detail) {
      callback(customEvent.detail);
    }
  };

  const onStorageEvent = (event: StorageEvent) => {
    if (event.key !== STORAGE_KEY || !event.newValue) {
      return;
    }

    try {
      const nextOverrides = JSON.parse(event.newValue) as Record<string, UniversityRatingUpdate>;
      Object.values(nextOverrides).forEach((value) => callback(value));
    } catch {
      // Ignore malformed storage payloads.
    }
  };

  window.addEventListener(EVENT_NAME, onCustomEvent);
  window.addEventListener("storage", onStorageEvent);

  return () => {
    window.removeEventListener(EVENT_NAME, onCustomEvent);
    window.removeEventListener("storage", onStorageEvent);
  };
}
