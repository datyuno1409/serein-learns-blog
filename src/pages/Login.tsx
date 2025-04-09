import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";
import { useLanguage } from "@/contexts/LanguageContext";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useToast } from "@/hooks/use-toast";

const Login = () => {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();
  const { toast } = useToast();
  const { t } = useLanguage();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    // Simple validation
    if (!username || !password) {
      toast({
        title: t("login.error"),
        description: t("login.fillFields"),
        variant: "destructive",
      });
      setIsLoading(false);
      return;
    }

    const success = login(username, password);
    
    if (success) {
      toast({
        title: t("login.success"),
        description: t("login.welcomeBack"),
      });
      navigate("/");
    } else {
      toast({
        title: t("login.error"),
        description: t("login.invalidCredentials"),
        variant: "destructive",
      });
    }
    
    setIsLoading(false);
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        <section className="bg-gray-50 py-8">
          <div className="container">
            <h1 className="text-3xl font-bold mb-2">{t("login.title")}</h1>
            <p className="text-gray-600 mb-0">
              {t("login.subtitle")}
            </p>
          </div>
        </section>

        <section className="py-12">
          <div className="container">
            <div className="max-w-md mx-auto">
              <div className="bg-white p-8 rounded-lg shadow-sm border">
                <form onSubmit={handleSubmit} className="space-y-6">
                  <div className="space-y-2">
                    <Label htmlFor="username">{t("login.username")}</Label>
                    <Input
                      id="username"
                      value={username}
                      onChange={(e) => setUsername(e.target.value)}
                      placeholder={t("login.usernamePlaceholder")}
                    />
                  </div>
                  
                  <div className="space-y-2">
                    <Label htmlFor="password">{t("login.password")}</Label>
                    <Input
                      id="password"
                      type="password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder={t("login.passwordPlaceholder")}
                    />
                  </div>

                  <Button 
                    type="submit" 
                    className="w-full bg-serein-500 hover:bg-serein-600"
                    disabled={isLoading}
                  >
                    {isLoading ? t("login.loggingIn") : t("login.loginButton")}
                  </Button>
                </form>
              </div>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Login;
