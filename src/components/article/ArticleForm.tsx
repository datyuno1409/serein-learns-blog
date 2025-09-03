
import React from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
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
import { ArticleFormValues } from "@/hooks/useArticleForm";

interface ArticleFormProps {
  form: UseFormReturn<ArticleFormValues>;
  isSubmitting: boolean;
  onSubmit: (data: ArticleFormValues) => void;
  onImageSelected?: (file: File) => void;
}

export const ArticleForm = ({ form, isSubmitting, onSubmit, onImageSelected }: ArticleFormProps) => {
  const navigate = useNavigate();
  const { t } = useLanguage();

  const handleImageUpload = (file: File) => {
    const imageUrl = URL.createObjectURL(file);
    form.setValue('image', imageUrl);
    onImageSelected?.(file);
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
          name="description"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.excerpt")} <span className="text-red-500">*</span>
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
          name="body"
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
          name="image"
          render={({ field }) => (
            <FormItem>
              <FormLabel className="text-base">
                {t("createArticle.coverImage")} <span className="text-red-500">*</span>
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
