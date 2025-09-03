
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { toast } from "sonner";
import { useLanguage } from "@/contexts/LanguageContext";
import { articleAPI } from "@/services/api";

// Define the form schema using zod
const formSchema = z.object({
  title: z.string().min(1, { message: "Title is required" }).max(100),
  image: z.string().min(1, { message: "Image is required" }),
  description: z.string().min(10, { message: "Description must be at least 10 characters" }),
  body: z.string().min(50, { message: "Content must be at least 50 characters" }),
  tags: z.string().optional(),
});

export type ArticleFormValues = z.infer<typeof formSchema>;

export const useArticleForm = () => {
  const navigate = useNavigate();
  const { t } = useLanguage();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [uploadedFile, setUploadedFile] = useState<File | null>(null);

  // Initialize the form with react-hook-form and zod validation
  const form = useForm<ArticleFormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      title: "",
      image: "",
      description: "",
      body: "",
      tags: "",
    },
  });

  const handleSubmitArticle = async (values: ArticleFormValues) => {
    try {
      setIsSubmitting(true);
      if (!uploadedFile) {
        toast.error("Please select a cover image");
        return;
      }

      // Build multipart form data for backend
      const formData = new FormData();
      formData.append("title", values.title);
      formData.append("excerpt", values.description);
      formData.append("content", values.body);
      formData.append("category", "Web Development");
      formData.append("tags", values.tags || "");
      formData.append("coverImage", uploadedFile);

      await articleAPI.create(formData);

      toast.success(String(t("createArticle.success")));
      navigate("/articles");
    } catch (error) {
      // Show error toast if something goes wrong
      toast.error("Failed to create article");
      console.error("Error creating article:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return {
    form,
    isSubmitting,
    handleSubmitArticle,
    setUploadedFile,
  };
};
