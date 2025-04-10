import { useState } from "react";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAuth } from "@/contexts/AuthContext";
import { Github, Globe, MoreVertical, Edit, Trash, Plus } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useToast } from "@/hooks/use-toast";

interface Project {
  id: string;
  title: string;
  description: string;
  image: string;
  tags: string[];
  github?: string;
  demo?: string;
  featured: boolean;
}

const MyProjects = () => {
  const { t } = useLanguage();
  const { user } = useAuth();
  const { toast } = useToast();
  const [projects, setProjects] = useState<Project[]>([
    {
      id: "1",
      title: "UniSAST Platform",
      description: "A web-based platform integrating open-source SAST tools for automated code security analysis and DevSecOps support in SMEs. Built with React, Node.js, and Docker.",
      image: "/projects/unisast.png",
      tags: ["React", "Node.js", "Docker", "Security", "DevSecOps"],
      github: "https://github.com/callmeserein/unisast",
      demo: "https://unisast.dev",
      featured: true
    },
    {
      id: "2",
      title: "Serein Blog",
      description: "Personal blog and portfolio website built with React, TypeScript, and Tailwind CSS. Features multilingual support, dark mode, and a custom CMS.",
      image: "/projects/blog.png",
      tags: ["React", "TypeScript", "Tailwind CSS", "i18n"],
      github: "https://github.com/callmeserein/blog",
      demo: "https://serein.dev",
      featured: true
    },
    {
      id: "3",
      title: "Network Security Monitor",
      description: "A network security monitoring tool that analyzes traffic patterns and detects potential security threats using machine learning algorithms.",
      image: "/projects/network-monitor.png",
      tags: ["Python", "Machine Learning", "Network Security"],
      github: "https://github.com/callmeserein/network-monitor",
      featured: false
    }
  ]);

  const handleDelete = async (projectId: string) => {
    try {
      // In a real app, you would call your API here
      // await deleteProject(projectId);
      
      setProjects(projects.filter(project => project.id !== projectId));
      
      toast({
        title: t("myProjects.deleteSuccess") as string,
        description: "",
      });
    } catch (error) {
      toast({
        title: t("myProjects.deleteError") as string,
        description: "",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="bg-gray-50 py-12">
          <div className="container">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-3xl md:text-4xl font-bold">{t("myProjects.title")}</h1>
              {user?.role === 'admin' && (
                <Button className="bg-serein-500 hover:bg-serein-600">
                  <Plus className="w-4 h-4 mr-2" />
                  {t("myProjects.create")}
                </Button>
              )}
            </div>
            <p className="text-lg text-gray-600 mb-8 max-w-3xl">
              {t("myProjects.subtitle")}
            </p>
          </div>
        </section>

        {/* Projects Grid */}
        <section className="py-12">
          <div className="container">
            <div className="space-y-12">
              {projects.map((project) => (
                <div 
                  key={project.id}
                  className="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all"
                >
                  <div className="flex flex-col md:flex-row">
                    {/* Project Image */}
                    <div className="md:w-1/3 relative">
                      <div className="aspect-video md:aspect-square w-full">
                        <img 
                          src={project.image} 
                          alt={project.title}
                          className="w-full h-full object-cover"
                        />
                      </div>
                      {project.featured && (
                        <div className="absolute top-2 left-2">
                          <span className="bg-serein-500 text-white text-xs px-2 py-1 rounded-full">
                            Featured
                          </span>
                        </div>
                      )}
                      {user?.role === 'admin' && (
                        <div className="absolute top-2 right-2">
                          <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                              <Button variant="ghost" size="icon" className="h-8 w-8 bg-white/80 hover:bg-white">
                                <MoreVertical className="h-4 w-4" />
                              </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                              <DropdownMenuItem>
                                <Edit className="mr-2 h-4 w-4" />
                                {t('myProjects.edit')}
                              </DropdownMenuItem>
                              <AlertDialog>
                                <AlertDialogTrigger asChild>
                                  <DropdownMenuItem onSelect={(e) => e.preventDefault()} className="text-red-600">
                                    <Trash className="mr-2 h-4 w-4" />
                                    {t('myProjects.delete')}
                                  </DropdownMenuItem>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                  <AlertDialogHeader>
                                    <AlertDialogTitle>{t('myProjects.deleteConfirmTitle')}</AlertDialogTitle>
                                    <AlertDialogDescription>
                                      {t('myProjects.deleteConfirmMessage')}
                                    </AlertDialogDescription>
                                  </AlertDialogHeader>
                                  <AlertDialogFooter>
                                    <AlertDialogCancel>{t('common.cancel')}</AlertDialogCancel>
                                    <AlertDialogAction 
                                      onClick={() => handleDelete(project.id)}
                                      className="bg-red-600 hover:bg-red-700"
                                    >
                                      {t('common.delete')}
                                    </AlertDialogAction>
                                  </AlertDialogFooter>
                                </AlertDialogContent>
                              </AlertDialog>
                            </DropdownMenuContent>
                          </DropdownMenu>
                        </div>
                      )}
                    </div>
                    
                    {/* Project Info */}
                    <div className="p-6 md:w-2/3">
                      <h3 className="text-xl font-bold text-gray-900 mb-2">
                        {project.title}
                      </h3>
                      <p className="text-gray-600 mb-4">
                        {project.description}
                      </p>
                      
                      {/* Tags */}
                      <div className="flex flex-wrap gap-2 mb-4">
                        {project.tags.map((tag, tagIndex) => (
                          <span 
                            key={tagIndex}
                            className="bg-serein-50 text-serein-600 px-2 py-1 rounded-full text-sm"
                          >
                            {tag}
                          </span>
                        ))}
                      </div>
                      
                      {/* Links */}
                      <div className="flex gap-4">
                        {project.github && (
                          <a
                            href={project.github}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center text-serein-600 hover:text-serein-700 transition-colors"
                          >
                            <Github className="w-4 h-4 mr-1" />
                            <span>GitHub</span>
                          </a>
                        )}
                        {project.demo && (
                          <a
                            href={project.demo}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center text-serein-600 hover:text-serein-700 transition-colors"
                          >
                            <Globe className="w-4 h-4 mr-1" />
                            <span>Live Demo</span>
                          </a>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default MyProjects; 