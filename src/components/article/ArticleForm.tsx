import React from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue 
} from "@/components/ui/select";
import { useNavigate } from "react-router-dom";
import { useLanguage } from "@/contexts/LanguageContext";
import { 
  Form, 
  FormControl, 
  FormField, 
  FormItem, 
  FormLabel, 
  FormMessage 
} from "@/components/ui/form";
import { UseFormReturn } from "react-hook-form";
import ImageUpload from "@/components/ImageUpload";

export const CATEGORIES = ["Cybersecurity", "Web Development", "Cryptography", "Software Architecture", "DevOps"];

interface ArticleFormProps {
  form: UseFormReturn<{
    title: string;
    excerpt?: string;
    content: string;
    category: string;
    tags?: string;
    coverImageUrl?: string;
  }>;
  isSubmitting: boolean;
  onSubmit: (data: any) => void;
}

export const ArticleForm = ({ form, isSubmitting, onSubmit }: ArticleFormProps) => {
  const navigate = useNavigate();
  const { t } = useLanguage();

  const handleImageUpload = (file: File) => {
    // Create a local URL for the uploaded image
    const imageUrl = URL.createObjectURL(file);
    form.setValue('coverImageUrl', imageUrl);
  };

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-8">
        <FormField
          control={form.control}
          name="title"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.formTitle")} <span className="text-red-500">*</span>
              </FormLabel>
              <FormControl>
                <Input
                  {...field}
                  placeholder={t("createArticle.titlePlaceholder") as string}
                  className="text-lg"
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="excerpt"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.excerpt")} <span className="text-gray-500 text-sm">({t("createArticle.briefSummary")})</span>
              </FormLabel>
              <FormControl>
                <Textarea
                  {...field}
                  placeholder={t("createArticle.excerptPlaceholder") as string}
                  className="resize-none"
                  rows={3}
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="content"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.content")} <span className="text-red-500">*</span>
              </FormLabel>
              <FormControl>
                <Textarea
                  {...field}
                  placeholder={t("createArticle.contentPlaceholder") as string}
                  className="min-h-[300px]"
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="coverImageUrl"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.coverImage")}
              </FormLabel>
              <FormControl>
                <ImageUpload
                  onImageUpload={handleImageUpload}
                  currentImage={field.value}
                  className="mt-2"
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="category"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.category")} <span className="text-red-500">*</span>
              </FormLabel>
              <Select 
                onValueChange={field.onChange}
                value={field.value}
              >
                <FormControl>
                  <SelectTrigger>
                    <SelectValue placeholder={t("createArticle.selectCategory") as string} />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  {CATEGORIES.map(category => (
                    <SelectItem key={category} value={category}>
                      {category}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormMessage />
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="tags"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.tags")} <span className="text-gray-500 text-sm">({t("createArticle.commaSeparated")})</span>
              </FormLabel>
              <FormControl>
                <Input
                  {...field}
                  placeholder={t("createArticle.tagsPlaceholder") as string}
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          )}
        />

        <div className="pt-4 flex gap-4">
          <Button
            type="submit"
            disabled={isSubmitting}
            className="bg-serein-500 hover:bg-serein-600"
          >
            {isSubmitting ? t("createArticle.publishing") as string : t("createArticle.publish") as string}
          </Button>
          <Button
            type="button"
            variant="outline"
            onClick={() => navigate("/articles")}
          >
            {t("createArticle.cancel") as string}
          </Button>
        </div>
      </form>
    </Form>
  );
};
