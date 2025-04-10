import { useState } from "react";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAuth } from "@/contexts/AuthContext";
import ProfileImageUpload from "@/components/ProfileImageUpload";
import { useToast } from "@/hooks/use-toast";
import { Github, Globe } from "lucide-react";

const About = () => {
  const { t } = useLanguage();
  const { user } = useAuth();
  const { toast } = useToast();
  const [profileImage, setProfileImage] = useState("/profile.jpg");
  
  const skills = [
    "AI",
    "Linux",
    "Code AI",
    "IT Helpdesk",
    "IT Hardware",
    "Cryptography",
    "Cybersecurity",
    "Cloud Security",
    "Window Server",
    "WordPress Design",
    "Project Management"
  ];

  const handleImageUpload = async (file: File) => {
    try {
      // Create a FormData object to send the file
      const formData = new FormData();
      formData.append('image', file);

      // In a real application, you would send this to your server
      // const response = await fetch('/api/upload-profile-image', {
      //   method: 'POST',
      //   body: formData
      // });
      
      // For demo, we'll just create a local URL
      const imageUrl = URL.createObjectURL(file);
      setProfileImage(imageUrl);
      
      toast({
        title: "Success",
        description: "Profile image updated successfully",
      });
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to upload image",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow bg-gray-50">
        <div className="container py-12">
          <div className="bg-white rounded-xl shadow-lg overflow-hidden">
            {/* Header Section */}
            <div className="bg-gradient-to-r from-serein-100 to-serein-50 p-8 relative">
              <div className="flex flex-col md:flex-row gap-8 items-center">
                {/* Profile Image */}
                {user?.role === 'admin' ? (
                  <ProfileImageUpload 
                    currentImage={profileImage}
                    onImageUpload={handleImageUpload}
                  />
                ) : (
                  <div className="w-48 h-48 rounded-full overflow-hidden border-4 border-white shadow-lg">
                    <img 
                      src={profileImage}
                      alt={t("about.name") as string}
                      className="w-full h-full object-cover"
                    />
                  </div>
                )}
                
                {/* Title Info */}
                <div>
                  <h1 className="text-4xl font-bold mb-2">{t("about.name") as string}</h1>
                  <p className="text-serein-600 text-xl mb-2">{t("about.title") as string}</p>
                  <p className="text-serein-400">{t("about.experience") as string}</p>
                </div>
              </div>
              
              {/* Decorative Elements */}
              <div className="absolute top-4 right-4">
                <div className="text-serein-200 text-6xl">✦</div>
                </div>
              <div className="absolute bottom-4 left-4">
                <div className="text-serein-200 text-4xl">✦</div>
              </div>
            </div>

            <div className="p-8">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                {/* Left Column */}
                <div className="space-y-8">
                  {/* Contact Info */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("about.contact") as string}</h2>
                    <div className="space-y-2">
                      <p><span className="font-medium">{t("about.phone") as string}:</span> +84-905922376</p>
                      <p><span className="font-medium">{t("about.email") as string}:</span> ngthanhdat.fudn@gmail.com</p>
                      <p><span className="font-medium">{t("about.address") as string}:</span> {t("about.addressValue") as string}</p>
                    </div>
                  </section>

                  {/* Basic Information */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("basicInfo.title")}</h2>
                    <div className="space-y-2">
                      <p><span className="font-medium">{t("basicInfo.birthday")}:</span> {t("basicInfo.birthdayValue")}</p>
                      <p><span className="font-medium">{t("basicInfo.nationality")}:</span> {t("basicInfo.nationalityValue")}</p>
                      <p><span className="font-medium">{t("basicInfo.maritalStatus")}:</span> {t("basicInfo.maritalStatusValue")}</p>
                      <p><span className="font-medium">{t("basicInfo.gender")}:</span> {t("basicInfo.genderValue")}</p>
          </div>
        </section>

                  {/* Skills */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("about.skills")}</h2>
                    <div className="flex flex-wrap gap-2">
                      {skills.map((skill, index) => (
                        <span 
                          key={index}
                          className="bg-serein-50 text-serein-600 px-3 py-1 rounded-full text-sm hover:bg-serein-100 transition-colors"
                        >
                          {skill}
                        </span>
                      ))}
                    </div>
                  </section>
                </div>

                {/* Right Column */}
                <div className="md:col-span-2 space-y-8">
                  {/* About Me */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("about.aboutMe")}</h2>
                    <p className="text-gray-600 leading-relaxed">
                      {t("about.aboutMeContent")}
                    </p>
                  </section>

                  {/* Education */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("about.education")}</h2>
                    <div className="bg-serein-50 p-6 rounded-lg border border-serein-100">
                      <div className="flex justify-between items-start mb-4">
                        <div>
                          <h3 className="font-bold text-serein-900">{t("education.university")}</h3>
                          <p className="text-serein-600">{t("education.degree")}</p>
              </div>
                        <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full">
                          {t("education.period")}
                        </span>
            </div>
                      <ul className="list-disc list-inside space-y-3 text-gray-600">
                        {(t("education.achievements") as string[]).map((achievement, index) => (
                          <li key={index} className="leading-relaxed hover:text-serein-700 transition-colors">
                            {achievement}
                          </li>
                        ))}
                      </ul>
          </div>
        </section>

                  {/* Work History */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("about.workHistory")}</h2>
                    <div className="bg-serein-50 p-6 rounded-lg border border-serein-100">
                      <div className="flex justify-between items-start mb-4">
                        <div>
                          <h3 className="font-bold text-serein-900">{t("about.company")}</h3>
                          <p className="text-serein-600">{t("about.position")}</p>
                        </div>
                        <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full">
                          {t("about.workPeriod")}
                        </span>
                      </div>
                      <p className="text-gray-600">{t("about.responsibilities")}</p>
              </div>
                  </section>

                  {/* Certifications */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-serein-600">{t("about.certifications")}</h2>
                    <div className="space-y-4">
                      {/* Applied Cryptography */}
                      <div className="bg-serein-50 p-6 rounded-lg border border-serein-100 hover:shadow-md transition-all">
                        <div className="flex justify-between items-start">
                          <div className="flex-1 pr-4">
                            <h3 className="font-bold text-serein-900">Applied Cryptography Specialization</h3>
                            <p className="text-serein-600">Coursera</p>
                          </div>
                          <div className="flex flex-col items-end gap-2 min-w-[100px]">
                            <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full whitespace-nowrap">
                              2023
                            </span>
                            <a 
                              href="https://byvn.net/2oBG" 
                              target="_blank" 
                              rel="noopener noreferrer" 
                              className="inline-flex items-center text-sm text-serein-600 hover:text-serein-700 transition-colors whitespace-nowrap"
                            >
                              {t("about.certificationLink")} →
                            </a>
                          </div>
                        </div>
              </div>
              
                      {/* CertNexus CEET */}
                      <div className="bg-serein-50 p-6 rounded-lg border border-serein-100 hover:shadow-md transition-all">
                        <div className="flex justify-between items-start">
                          <div className="flex-1 pr-4">
                            <h3 className="font-bold text-serein-900">CertNexus Certified Ethical Emerging Technologist Specialization</h3>
                            <p className="text-serein-600">Coursera</p>
                          </div>
                          <div className="flex flex-col items-end gap-2 min-w-[100px]">
                            <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full whitespace-nowrap">
                              2023
                            </span>
                            <a 
                              href="https://byvn.net/VwKe" 
                              target="_blank" 
                              rel="noopener noreferrer" 
                              className="inline-flex items-center text-sm text-serein-600 hover:text-serein-700 transition-colors whitespace-nowrap"
                            >
                              {t("about.certificationLink")} →
                            </a>
                          </div>
                        </div>
              </div>
              
                      {/* ISC2 SSCP */}
                      <div className="bg-serein-50 p-6 rounded-lg border border-serein-100 hover:shadow-md transition-all">
                        <div className="flex justify-between items-start">
                          <div className="flex-1 pr-4">
                            <h3 className="font-bold text-serein-900">ISC2 Systems Security Certified Practitioner (SSCP) Specialization</h3>
                            <p className="text-serein-600">Coursera</p>
                          </div>
                          <div className="flex flex-col items-end gap-2 min-w-[100px]">
                            <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full whitespace-nowrap">
                              2024
                            </span>
                            <a 
                              href="https://byvn.net/kHxR" 
                              target="_blank" 
                              rel="noopener noreferrer" 
                              className="inline-flex items-center text-sm text-serein-600 hover:text-serein-700 transition-colors whitespace-nowrap"
                            >
                              {t("about.certificationLink")} →
                            </a>
                          </div>
                        </div>
              </div>
              
                      {/* Cisco Cloud Security */}
                      <div className="bg-serein-50 p-6 rounded-lg border border-serein-100 hover:shadow-md transition-all">
                        <div className="flex justify-between items-start">
                          <div className="flex-1 pr-4">
                            <h3 className="font-bold text-serein-900">Partner: Cloud Security</h3>
                            <p className="text-serein-600">Cisco</p>
                          </div>
                          <div className="flex flex-col items-end gap-2 min-w-[100px]">
                            <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full whitespace-nowrap">
                              2023
                            </span>
                            <a 
                              href="https://byvn.net/5gSr" 
                              target="_blank" 
                              rel="noopener noreferrer" 
                              className="inline-flex items-center text-sm text-serein-600 hover:text-serein-700 transition-colors whitespace-nowrap"
                            >
                              {t("about.certificationLink")} →
                            </a>
                          </div>
                        </div>
              </div>
              
                      {/* CCNA */}
                      <div className="bg-serein-50 p-6 rounded-lg border border-serein-100 hover:shadow-md transition-all">
                        <div className="flex justify-between items-start">
                          <div className="flex-1 pr-4">
                            <h3 className="font-bold text-serein-900">CCNA: Switching, Routing, and Wireless Essentials</h3>
                            <p className="text-serein-600">Cisco</p>
                          </div>
                          <div className="flex flex-col items-end gap-2 min-w-[100px]">
                            <span className="text-sm text-serein-500 bg-serein-100 px-3 py-1 rounded-full whitespace-nowrap">
                              2024
                            </span>
                            <a 
                              href="https://byvn.net/wqkA" 
                              target="_blank" 
                              rel="noopener noreferrer" 
                              className="inline-flex items-center text-sm text-serein-600 hover:text-serein-700 transition-colors whitespace-nowrap"
                            >
                              {t("about.certificationLink")} →
                            </a>
                          </div>
              </div>
            </div>
          </div>
        </section>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
};

export default About;
