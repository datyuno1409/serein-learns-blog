
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";

const About = () => {
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
                  About <span className="text-serein-500">Serein</span>
                </h1>
                <p className="text-xl text-gray-700 leading-relaxed mb-6">
                  Technology enthusiast, security advocate, and lifelong learner sharing knowledge and insights with the world.
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
              <h2 className="text-3xl font-bold mb-8">My Story</h2>
              <div className="prose prose-lg max-w-none">
                <p>
                  Hello! I'm Serein, a technology professional with over a decade of experience in software development, cybersecurity, and technical education. My journey began with a curiosity about how computers work and evolved into a passion for building secure, efficient systems and sharing that knowledge with others.
                </p>
                <p>
                  After completing my degree in Computer Science, I worked as a software developer at several technology companies, where I honed my skills in web development, system architecture, and security practices. As I progressed in my career, I became increasingly interested in cybersecurity and how technology intersects with privacy, ethics, and society at large.
                </p>
                <p>
                  In 2020, I decided to start this blog, "Learning with Serein," as a platform to share my knowledge, experiences, and insights with a broader audience. My goal is to make complex technical topics accessible and engaging for readers of all backgrounds, from beginners to seasoned professionals.
                </p>
                <p>
                  When I'm not writing or coding, you can find me experimenting with new technologies, contributing to open-source projects, attending tech conferences, or hiking in the great outdoors to disconnect and recharge.
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* Expertise Section */}
        <section className="py-16 bg-gray-50">
          <div className="container">
            <h2 className="text-3xl font-bold mb-12 text-center">Areas of Expertise</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Web Development</h3>
                <p className="text-gray-600 mb-6">
                  Full-stack development with modern frameworks and technologies, focusing on performance, accessibility, and user experience.
                </p>
                <Link to="/articles?category=Web+Development">
                  <Button variant="link" className="text-serein-500 p-0">
                    Read related articles →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Cybersecurity</h3>
                <p className="text-gray-600 mb-6">
                  Security best practices, threat modeling, application security, and building systems with security-by-design principles.
                </p>
                <Link to="/articles?category=Cybersecurity">
                  <Button variant="link" className="text-serein-500 p-0">
                    Read related articles →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Cryptography</h3>
                <p className="text-gray-600 mb-6">
                  Understanding modern cryptographic systems, encryption techniques, and their practical applications in software.
                </p>
                <Link to="/articles?category=Cryptography">
                  <Button variant="link" className="text-serein-500 p-0">
                    Read related articles →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Software Architecture</h3>
                <p className="text-gray-600 mb-6">
                  Designing scalable, maintainable software systems with appropriate patterns and practices for different contexts.
                </p>
                <Link to="/articles?category=Software+Architecture">
                  <Button variant="link" className="text-serein-500 p-0">
                    Read related articles →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">DevOps</h3>
                <p className="text-gray-600 mb-6">
                  Implementing CI/CD pipelines, infrastructure as code, and modern deployment practices for efficient software delivery.
                </p>
                <Link to="/articles?category=DevOps">
                  <Button variant="link" className="text-serein-500 p-0">
                    Read related articles →
                  </Button>
                </Link>
              </div>
              
              <div className="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                <h3 className="text-xl font-bold mb-4">Technical Writing</h3>
                <p className="text-gray-600 mb-6">
                  Creating clear, concise, and engaging content that explains complex technical concepts to diverse audiences.
                </p>
                <Link to="/articles">
                  <Button variant="link" className="text-serein-500 p-0">
                    Browse all articles →
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
              <h2 className="text-3xl font-bold mb-6">Get in Touch</h2>
              <p className="text-lg text-gray-600 mb-8">
                Have a question, suggestion, or just want to say hello? I'd love to hear from you!
              </p>
              <Link to="/contact">
                <Button className="bg-serein-500 hover:bg-serein-600">
                  Contact Me
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
