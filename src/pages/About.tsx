import { useState } from "react";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAuth } from "@/contexts/AuthContext";
import ProfileImageUpload from "@/components/ProfileImageUpload";
import { useToast } from "@/hooks/use-toast";

const About = () => {
  const { t } = useLanguage();
  const { user } = useAuth();
  const { toast } = useToast();
  const [profileImage, setProfileImage] = useState("/profile.jpg");
  
  const skills = [
    "WordPress Design",
    "Window Server",
    "Cloud Security",
    "IT Hardware",
    "Code AI",
    "Project Management",
    "AI",
    "Network Forensic",
    "IT Helpdesk",
    "Linux"
  ];

  const achievements = [
    "Served as a member of the Security Research Club from 09/2022 to 12/2023.",
    "Led the club in participating in competitions such as Hackathon, Secathon, Bootcamp, and Secathon Asean, among others.",
    "Recognized as an Outstanding Student for one year.",
    "Contributed to organizing security-related events, helping the club earn the Outstanding Club Award.",
    "Achieved Runner-up position for the Graduation Project with the topic: 'Development of UniSAST: A Web-based Platform Integrating Open-source SAST Tools for Automated Code Security Analysis and DevSecOps Support in SMEs.'"
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
            <div className="bg-gradient-to-r from-purple-100 to-purple-50 p-8 relative">
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
                      alt={t("about.name")}
                      className="w-full h-full object-cover"
                    />
                  </div>
                )}
                
                {/* Title Info */}
                <div>
                  <h1 className="text-4xl font-bold mb-2">{t("about.name")}</h1>
                  <p className="text-purple-600 text-xl mb-2">{t("about.title")}</p>
                  <p className="text-purple-400">{t("about.experience")}</p>
                </div>
              </div>
              
              {/* Decorative Elements */}
              <div className="absolute top-4 right-4">
                <div className="text-purple-200 text-6xl">✦</div>
              </div>
              <div className="absolute bottom-4 left-4">
                <div className="text-purple-200 text-4xl">✦</div>
              </div>
            </div>

            <div className="p-8">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                {/* Left Column */}
                <div className="space-y-8">
                  {/* Contact Info */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.contactMe")}</h2>
                    <div className="space-y-2">
                      <p><span className="font-medium">{t("about.phone")}:</span> +84-905922376</p>
                      <p><span className="font-medium">{t("about.email")}:</span> ngthanhdat.fudn@gmail.com</p>
                      <p><span className="font-medium">{t("about.address")}:</span> Truong Tho, Thu Duc, Ho Chi Minh, Vietnam</p>
                    </div>
                  </section>

                  {/* Basic Information */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.basicInfo")}</h2>
                    <div className="space-y-2">
                      <p><span className="font-medium">{t("about.birthday")}:</span> 14/09/2002</p>
                      <p><span className="font-medium">{t("about.nationality")}:</span> Vietnamese</p>
                      <p><span className="font-medium">{t("about.maritalStatus")}:</span> Single</p>
                      <p><span className="font-medium">{t("about.gender")}:</span> Male</p>
                    </div>
                  </section>

                  {/* Skills */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.skills")}</h2>
                    <div className="flex flex-wrap gap-2">
                      {skills.map((skill, index) => (
                        <span 
                          key={index}
                          className="bg-purple-50 text-purple-600 px-3 py-1 rounded-full text-sm"
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
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.aboutMe")}</h2>
                    <p className="text-gray-600 leading-relaxed">
                      {t("about.aboutMeContent")}
                    </p>
                  </section>

                  {/* Education */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.education")}</h2>
                    <div className="bg-purple-50 p-4 rounded-lg">
                      <div className="flex justify-between items-start mb-2">
                        <div>
                          <h3 className="font-bold">{t("about.university")}</h3>
                          <p className="text-purple-600">{t("about.degree")}</p>
                        </div>
                        <span className="text-sm text-gray-500">{t("about.period")}</span>
                      </div>
                      <ul className="list-disc list-inside space-y-2 text-gray-600">
                        {achievements.map((achievement, index) => (
                          <li key={index} className="leading-relaxed">
                            {achievement}
                          </li>
                        ))}
                      </ul>
                    </div>
                  </section>

                  {/* Work History */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.workHistory")}</h2>
                    <div className="bg-purple-50 p-4 rounded-lg">
                      <div className="flex justify-between items-start mb-2">
                        <div>
                          <h3 className="font-bold">{t("about.company")}</h3>
                          <p className="text-purple-600">{t("about.position")}</p>
                        </div>
                        <span className="text-sm text-gray-500">{t("about.workPeriod")}</span>
                      </div>
                      <p className="text-gray-600">{t("about.responsibilities")}</p>
                    </div>
                  </section>

                  {/* Certifications */}
                  <section>
                    <h2 className="text-xl font-bold mb-4 text-purple-600">{t("about.certifications")}</h2>
                    <div className="bg-purple-50 p-4 rounded-lg">
                      <div className="flex justify-between items-start">
                        <div>
                          <h3 className="font-bold">Applied Cryptography Specialization</h3>
                          <p className="text-purple-600">{t("about.certificationProvider")}</p>
                        </div>
                        <span className="text-sm text-gray-500">{t("about.certificationYear")}</span>
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
