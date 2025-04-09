import { useState } from "react";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";
import { useToast } from "@/hooks/use-toast";

interface ProfileImageUploadProps {
  currentImage: string;
  onImageUpload: (file: File) => void;
}

const ProfileImageUpload = ({ currentImage, onImageUpload }: ProfileImageUploadProps) => {
  const [isDragging, setIsDragging] = useState(false);
  const { t } = useLanguage();
  const { toast } = useToast();

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
    
    const files = e.dataTransfer.files;
    handleFiles(files);
  };

  const handleFileInput = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (files) {
      handleFiles(files);
    }
  };

  const handleFiles = (files: FileList) => {
    if (files.length > 0) {
      const file = files[0];
      
      // Validate file type
      if (!file.type.startsWith('image/')) {
        toast({
          title: "Error",
          description: "Please upload an image file",
          variant: "destructive",
        });
        return;
      }

      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        toast({
          title: "Error",
          description: "Image size should be less than 5MB",
          variant: "destructive",
        });
        return;
      }

      onImageUpload(file);
    }
  };

  return (
    <div className="relative">
      <div 
        className={`w-48 h-48 rounded-full overflow-hidden border-4 border-white shadow-lg relative group
          ${isDragging ? 'border-purple-500' : ''}`}
        onDragOver={handleDragOver}
        onDragLeave={handleDragLeave}
        onDrop={handleDrop}
      >
        <img 
          src={currentImage} 
          alt="Profile"
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
          <Button 
            variant="secondary"
            size="sm"
            onClick={() => document.getElementById('profile-image-input')?.click()}
          >
            Change Photo
          </Button>
        </div>
      </div>
      <input
        id="profile-image-input"
        type="file"
        accept="image/*"
        className="hidden"
        onChange={handleFileInput}
      />
      <div className="mt-2 text-sm text-gray-500 text-center">
        Drag & drop an image or click to upload
      </div>
    </div>
  );
};

export default ProfileImageUpload; 