import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useToast } from "@/hooks/use-toast";
import { useLanguage } from "@/contexts/LanguageContext";

interface ImageUploadProps {
  onImageUpload: (file: File) => void;
  currentImage?: string;
  className?: string;
}

const ImageUpload = ({ onImageUpload, currentImage, className = "" }: ImageUploadProps) => {
  const [isDragging, setIsDragging] = useState(false);
  const { toast } = useToast();
  const { t } = useLanguage();

  const validateFile = (file: File): boolean => {
    // Check file type
    if (!file.type.startsWith('image/')) {
      toast({
        title: "Error",
        description: "Please upload an image file (JPG, PNG, GIF)",
        variant: "destructive",
      });
      return false;
    }

    // Check file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
      toast({
        title: "Error",
        description: "Image size should be less than 5MB",
        variant: "destructive",
      });
      return false;
    }

    return true;
  };

  const handleFiles = (files: FileList) => {
    if (files.length > 0) {
      const file = files[0];
      if (validateFile(file)) {
        // Create a local preview URL
        const imageUrl = URL.createObjectURL(file);
        
        // Pass both the file and URL to parent
        onImageUpload(file);
      }
    }
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = () => {
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    handleFiles(e.dataTransfer.files);
  };

  const handleFileInput = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      handleFiles(e.target.files);
    }
  };

  return (
    <div 
      className={`relative border-2 border-dashed rounded-lg p-4 text-center ${
        isDragging ? 'border-serein-500 bg-serein-50' : 'border-gray-300'
      } ${className}`}
      onDragOver={handleDragOver}
      onDragLeave={handleDragLeave}
      onDrop={handleDrop}
    >
      {currentImage && (
        <div className="mb-4">
          <img 
            src={currentImage} 
            alt="Preview"
            className="max-h-[200px] mx-auto object-contain"
          />
        </div>
      )}
      
      <Input
        type="file"
        accept="image/*"
        className="hidden"
        onChange={handleFileInput}
        id="image-upload"
      />
      
      <label htmlFor="image-upload">
        <div className="cursor-pointer">
          <p className="text-sm text-gray-600 mb-2">
            Kéo thả ảnh vào đây hoặc click để chọn ảnh
          </p>
          <p className="text-xs text-gray-500 mb-2">
            Hỗ trợ: JPG, PNG, GIF (Max: 5MB)
          </p>
          <Button type="button" variant="outline">
            Chọn ảnh
          </Button>
        </div>
      </label>
    </div>
  );
};

export default ImageUpload; 