import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";

const About = () => {
  const { t } = useLanguage();
  
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="bg-gradient-to-r from-gray-100 to-gray-200 py-20">
          <div className="container">
            <div className="flex flex-col md:flex-row items-center gap-12">
              <div className="md:w-1/2">
                <h1 className="text-4xl md:text-5xl font-bold mb-6">
                  {t('nav.about')} <span className="text-serein-500">Serein</span>
                </h1>
                <p className="text-xl text-gray-700 leading-relaxed mb-6">
                  {t('hero.subtitle')}
                </p>
                <div className="flex space-x-4">
                  <a 
                    href="https://twitter.com" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className="text-gray-700 hover:text-serein-500 transition-colors duration-200"
                  >
                    Twitter
                  </a>
                  <a 
                    href="https://github.com" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className="text-gray-700 hover:text-serein-500 transition-colors duration-200"
                  >
                    GitHub
                  </a>
                  <a 
                    href="https://linkedin.com" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className="text-gray-700 hover:text-serein-500 transition-colors duration-200"
                  >
                    LinkedIn
                  </a>
                </div>
              </div>
              <div className="md:w-1/2">
                <div className="rounded-2xl overflow-hidden shadow-xl">
                  <img 
                    src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                    alt="Serein" 
                    className="w-full h-auto"
                  />
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Bio Section */}
        <section className="py-16">
          <div className="container">
            <div className="max-w-3xl mx-auto">
              <h2 className="text-3xl font-bold mb-8">{t('hero.aboutSerein')}</h2>
              <div className="prose prose-lg max-w-none">
                <p>{t('createArticle.subtitle')}</p>
              </div>
            </div>
          </div>
        </section>

        {/* Expertise Section */}
        <section className="py-16 bg-gray-50">
          <div className="container">
            <h2 className="text-3xl font-bold mb-12 text-center">{t('popularTopics')}</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Web Development</h3>
                <p className="text-gray-600 mb-6">
                  {t('hero.subtitle')}
                </p>
                <Link to="/articles?category=Web+Development">
                  <Button variant="link" className="text-serein-500 p-0">
                    {t('hero.browseArticles')} →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Cybersecurity</h3>
                <p className="text-gray-600 mb-6">
                  {t('hero.subtitle')}
                </p>
                <Link to="/articles?category=Cybersecurity">
                  <Button variant="link" className="text-serein-500 p-0">
                    {t('hero.browseArticles')} →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Cryptography</h3>
                <p className="text-gray-600 mb-6">
                  {t('hero.subtitle')}
                </p>
                <Link to="/articles?category=Cryptography">
                  <Button variant="link" className="text-serein-500 p-0">
                    {t('hero.browseArticles')} →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Software Architecture</h3>
                <p className="text-gray-600 mb-6">
                  {t('hero.subtitle')}
                </p>
                <Link to="/articles?category=Software+Architecture">
                  <Button variant="link" className="text-serein-500 p-0">
                    {t('hero.browseArticles')} →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">DevOps</h3>
                <p className="text-gray-600 mb-6">
                  {t('hero.subtitle')}
                </p>
                <Link to="/articles?category=DevOps">
                  <Button variant="link" className="text-serein-500 p-0">
                    {t('hero.browseArticles')} →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Technical Writing</h3>
                <p className="text-gray-600 mb-6">
                  {t('hero.subtitle')}
                </p>
                <Link to="/articles">
                  <Button variant="link" className="text-serein-500 p-0">
                    {t('viewAll')} →
                  </Button>
                </Link>
              </div>
            </div>
          </div>
        </section>

        {/* Contact Section */}
        <section className="py-16">
          <div className="container">
            <div className="max-w-2xl mx-auto text-center">
              <h2 className="text-3xl font-bold mb-6">{t('newsletterTitle')}</h2>
              <p className="text-lg text-gray-600 mb-8">
                {t('newsletterSubtitle')}
              </p>
              <Link to="/contact">
                <Button className="bg-serein-500 hover:bg-serein-600">
                  {t('subscribe')}
                </Button>
              </Link>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default About;
