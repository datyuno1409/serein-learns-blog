
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { toast } from "sonner";
import { useLanguage } from "@/contexts/LanguageContext";
import { articles } from "@/data/articles";

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
      
      // Simulate API call with a timeout
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // Create a new article object
      const newArticle = {
        id: (articles.length + 1).toString(),
        title: values.title,
        image: values.image,
        description: values.description,
        content: values.body,
        tags: values.tags ? values.tags.split(',').map(tag => tag.trim()) : [],
        author: {
          name: "Current User",
          avatar: "/profile.jpg"
        },
        createdAt: new Date().toISOString(),
      };
      
      // Add the new article to the articles array
      articles.unshift(newArticle);
      
      // Show success toast and navigate to the articles page
      toast.success(t("createArticle.success"));
      navigate("/articles");
    } catch (error) {
      // Show error toast if something goes wrong
      toast.error(t("createArticle.formError"));
      console.error("Error creating article:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return {
    form,
    isSubmitting,
    handleSubmitArticle,
  };
};
