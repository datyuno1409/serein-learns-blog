
import { Link } from "react-router-dom";

const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-gray-900 text-white py-12">
      <div className="container">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Branding */}
          <div className="flex flex-col">
            <h3 className="text-xl font-bold font-heading mb-4">
              Learning with <span className="text-serein-400">Serein</span>
            </h3>
            <p className="text-gray-400 mb-4">
              Exploring technology and security topics to help you learn and grow in the digital world.
            </p>
            <div className="flex space-x-4">
              <a 
                href="https://twitter.com" 
                target="_blank" 
                rel="noopener noreferrer" 
                className="text-gray-400 hover:text-serein-400 transition-colors duration-200"
              >
                Twitter
              </a>
              <a 
                href="https://github.com" 
                target="_blank" 
                rel="noopener noreferrer" 
                className="text-gray-400 hover:text-serein-400 transition-colors duration-200"
              >
                GitHub
              </a>
              <a 
                href="https://linkedin.com" 
                target="_blank" 
                rel="noopener noreferrer" 
                className="text-gray-400 hover:text-serein-400 transition-colors duration-200"
              >
                LinkedIn
              </a>
            </div>
          </div>

          {/* Quick Links */}
          <div className="flex flex-col">
            <h3 className="text-xl font-bold font-heading mb-4">Quick Links</h3>
            <div className="space-y-2">
              <Link to="/" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Home
              </Link>
              <Link to="/articles" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Articles
              </Link>
              <Link to="/about" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                About
              </Link>
              <Link to="/create-article" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Create Article
              </Link>
            </div>
          </div>

          {/* Categories */}
          <div className="flex flex-col">
            <h3 className="text-xl font-bold font-heading mb-4">Categories</h3>
            <div className="space-y-2">
              <Link to="/articles?category=Cybersecurity" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Cybersecurity
              </Link>
              <Link to="/articles?category=Web+Development" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Web Development
              </Link>
              <Link to="/articles?category=Cryptography" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Cryptography
              </Link>
              <Link to="/articles?category=Software+Architecture" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                Software Architecture
              </Link>
              <Link to="/articles?category=DevOps" className="text-gray-400 hover:text-serein-400 transition-colors duration-200 block">
                DevOps
              </Link>
            </div>
          </div>
        </div>

        <div className="mt-12 pt-8 border-t border-gray-800 text-center text-gray-400">
          <p>&copy; {currentYear} Learning with Serein. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
