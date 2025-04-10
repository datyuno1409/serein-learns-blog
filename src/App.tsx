import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { LanguageProvider } from "@/contexts/LanguageContext";
import { AuthProvider } from "@/contexts/AuthContext";
import Index from "./pages/Index";
import Articles from "@/pages/Articles";
import ArticleDetail from "@/pages/ArticleDetail";
import EditArticle from "@/pages/EditArticle";
import CreateArticle from "@/pages/CreateArticle";
import About from "@/pages/About";
import Login from "@/pages/Login";
import NotFound from "@/pages/NotFound";
import MyProjects from "@/pages/MyProjects";

const queryClient = new QueryClient();

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <LanguageProvider>
        <AuthProvider>
          <TooltipProvider>
            <Toaster />
            <Sonner />
            <Router>
              <Routes>
                <Route path="/" element={<Index />} />
                <Route path="/articles" element={<Articles />} />
                <Route path="/article/:id" element={<ArticleDetail />} />
                <Route path="/articles/edit/:id" element={<EditArticle />} />
                <Route path="/create-article" element={<CreateArticle />} />
                <Route path="/my-projects" element={<MyProjects />} />
                <Route path="/about" element={<About />} />
                <Route path="/login" element={<Login />} />
                <Route path="*" element={<NotFound />} />
              </Routes>
            </Router>
          </TooltipProvider>
        </AuthProvider>
      </LanguageProvider>
    </QueryClientProvider>
  );
}

export default App;
