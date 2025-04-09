
import React from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
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
import { ArticleFormData } from "@/hooks/useArticleForm";
import { 
  Form, 
  FormControl, 
  FormField, 
  FormItem, 
  FormLabel, 
  FormMessage 
} from "@/components/ui/form";
import { UseFormReturn } from "react-hook-form";

export const CATEGORIES = ["Cybersecurity", "Web Development", "Cryptography", "Software Architecture", "DevOps"];

interface ArticleFormProps {
  form: UseFormReturn<ArticleFormData>;
  onSubmit: (data: ArticleFormData) => Promise<void>;
  isSubmitting: boolean;
}

export const ArticleForm: React.FC<ArticleFormProps> = ({ form, onSubmit, isSubmitting }) => {
  const navigate = useNavigate();
  const { t } = useLanguage();

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
                  placeholder={t("createArticle.titlePlaceholder")}
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
                  placeholder={t("createArticle.excerptPlaceholder")}
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
                  placeholder={t("createArticle.contentPlaceholder")}
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
                <Input
                  {...field}
                  placeholder={t("createArticle.coverImagePlaceholder")}
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
                value={field.value} 
                onValueChange={field.onChange}
              >
                <FormControl>
                  <SelectTrigger>
                    <SelectValue placeholder={t("createArticle.selectCategory")} />
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
                  placeholder={t("createArticle.tagsPlaceholder")}
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
            {isSubmitting ? t("createArticle.publishing") : t("createArticle.publish")}
          </Button>
          <Button
            type="button"
            variant="outline"
            onClick={() => navigate("/articles")}
          >
            {t("createArticle.cancel")}
          </Button>
        </div>
      </form>
    </Form>
  );
};
