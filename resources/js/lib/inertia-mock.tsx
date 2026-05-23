import React, { useEffect } from "react";
import { Link as WouterLink, useLocation, useRoute } from "wouter";
import { toast } from "sonner";

// Mock implementation of Inertia's Link
export const Link = ({ href, children, className, ...props }: any) => {
  return (
    <WouterLink href={href} className={className} {...props}>
      {children}
    </WouterLink>
  );
};

// Mock implementation of Inertia's Head
export const Head = ({ title }: { title: string }) => {
  useEffect(() => {
    document.title = title;
  }, [title]);
  return null;
};

// Mock implementation of usePage
export const usePage = () => {
  return {
    props: {
      auth: { user: null },
      errors: {},
      flash: { success: null, error: null },
    },
    url: window.location.pathname,
    component: "MockComponent",
    version: null,
    scrollRegions: [],
    rememberedState: {},
  };
};

// Mock implementation of useForm
export const useForm = (initialValues: any) => {
  const [data, setData] = React.useState(initialValues);
  const [processing, setProcessing] = React.useState(false);
  const [errors, setErrors] = React.useState({});
  const [, setLocation] = useLocation();

  const handleChange = (field: string, value: any) => {
    setData((prev: any) => ({ ...prev, [field]: value }));
  };

  const submit = (method: string, url: string, options: any = {}) => {
    setProcessing(true);
    // Simulate network delay
    setTimeout(() => {
      setProcessing(false);
      console.log(`Mock Inertia ${method.toUpperCase()} to ${url}`, data);

      if (options.onSuccess) {
        options.onSuccess();
      }

      // Flash message simulation
      toast.success("Request successful (Mock Mode)");
    }, 800);
  };

  return {
    data,
    setData: (keyOrData: any, value?: any) => {
      if (typeof keyOrData === "string") {
        handleChange(keyOrData, value);
      } else {
        setData(keyOrData);
      }
    },
    post: (url: string, options?: any) => submit("post", url, options),
    put: (url: string, options?: any) => submit("put", url, options),
    patch: (url: string, options?: any) => submit("patch", url, options),
    delete: (url: string, options?: any) => submit("delete", url, options),
    processing,
    errors,
    reset: () => setData(initialValues),
    clearErrors: () => setErrors({}),
    setError: (field: string, error: string) =>
      setErrors((prev: any) => ({ ...prev, [field]: error })),
  };
};

// Mock router
export const router = {
  visit: (url: string) => {
    window.location.href = url;
  },
  post: (url: string, data: any, options: any) => {
    console.log("Mock Inertia POST", url, data);
    if (options?.onSuccess) options.onSuccess();
    toast.success("Action completed (Mock Mode)");
  },
  get: (url: string) => {
    console.log("Mock Inertia GET", url);
  },
  put: (url: string, data: any) => {
    console.log("Mock Inertia PUT", url, data);
  },
  delete: (url: string) => {
    console.log("Mock Inertia DELETE", url);
  },
};
