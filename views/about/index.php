<?php
// This file is included as content in the frontend layout
?>

<div class="min-h-screen bg-white">
    <!-- Header Section with Teal Background -->
    <div class="bg-gradient-to-r from-teal-400 to-emerald-500 text-white py-16">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Profile Image -->
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg flex-shrink-0">
                    <img src="/public/profile.jpg" alt="Nguyen Thanh Dat" class="w-full h-full object-cover" onerror="this.src='/assets/images/default-avatar.svg'">
                </div>
                
                <!-- Title Info -->
                <div class="text-center md:text-left">
                    <h1 class="text-3xl font-bold mb-2">NGUYEN THANH DAT</h1>
                    <p class="text-teal-100 text-lg mb-2">TECHNICAL SUPPORT ENGINEER</p>
                    <p class="text-teal-200">1 YEARS EXPERIENCE</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="md:col-span-1 space-y-6">
                <!-- Contact Info -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">CONTACT</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="font-medium">Phone: +84 905922378</p>
                        </div>
                        <div>
                            <p class="font-medium">Email: serein@example.com</p>
                        </div>
                        <div>
                            <p class="font-medium">Address: Truong Tho, Thu Duc, Ho Chi Minh, Vietnam</p>
                        </div>
                    </div>
                </section>

                <!-- About Me -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">ABOUT ME</h2>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        <p>I emerged from the Information Security program at FPT University, equipped with some experience in cybersecurity testing and security project management. What drives me every day is the desire to learn and become a Pentest expert, helping businesses stand strong against all security challenges.</p>
                    </div>
                </section>

                <!-- Basic Information -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">Basic Information</h2>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">Birthday:</span> 14/09/2002</p>
                        <p><span class="font-medium">Nationality:</span> Vietnamese</p>
                        <p><span class="font-medium">Marital Status:</span> Single</p>
                        <p><span class="font-medium">Gender:</span> Male</p>
                    </div>
                </section>

                <!-- Skills -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">SKILLS</h2>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p>AI</p>
                        <p>Linux</p>
                        <p>Code AI</p>
                        <p>IT Helpdesk</p>
                        <p>IT Hardware</p>
                        <p>Cryptography</p>
                        <p>Cybersecurity</p>
                        <p>Cloud Security</p>
                        <p>Window Server</p>
                        <p>WordPress Design</p>
                        <p>Project Management</p>
                    </div>
                </section>

                <!-- Certifications -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">CERTIFICATIONS</h2>
                    <div class="space-y-4">
                        <div class="bg-teal-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-semibold text-gray-800">Applied Cryptography Specialization</h3>
                                <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">2023</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Coursera</p>
                            <a href="#" class="text-teal-600 text-sm hover:underline">Verify →</a>
                        </div>
                        
                        <div class="bg-teal-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-semibold text-gray-800">CertNexus Certified Ethical Emerging Technologist Specialization</h3>
                                <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">2023</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Coursera</p>
                            <a href="#" class="text-teal-600 text-sm hover:underline">Verify →</a>
                        </div>
                        
                        <div class="bg-teal-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-semibold text-gray-800">ISC2 Systems Security Certified Practitioner (SSCP) Specialization</h3>
                                <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">2024</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Coursera</p>
                            <a href="#" class="text-teal-600 text-sm hover:underline">Verify →</a>
                        </div>
                        
                        <div class="bg-teal-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-semibold text-gray-800">Partner: Cloud Security</h3>
                                <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">2023</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Cisco</p>
                            <a href="#" class="text-teal-600 text-sm hover:underline">Verify →</a>
                        </div>
                        
                        <div class="bg-teal-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-base font-semibold text-gray-800">CCNA: Switching, Routing, and Wireless Essentials</h3>
                                <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">2024</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">Cisco</p>
                            <a href="#" class="text-teal-600 text-sm hover:underline">Verify →</a>
                        </div>
                    </div>
                </section>
                </div>

            <!-- Right Column -->
            <div class="md:col-span-2 space-y-6">
                <!-- Education -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">EDUCATION</h2>
                    <div class="bg-teal-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-base font-semibold text-gray-800">FPT University DaNang</h3>
                            <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">09/2020 - 04 years 2 months</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Bachelors - Information Assurance</p>
                        
                        <div class="text-sm text-gray-700 space-y-2">
                            <p>• Served as a member of the Security Research Club from 09/2022 to 12/2023.</p>
                            <p>• Led the club in participating in competitions such as Hackathon, Secathon, Bootcamp, and Secathon among others.</p>
                            <p>• Recognized as an Outstanding Student for one year.</p>
                            <p>• Contributed to organizing security-related events, helping the club earn the Outstanding Club Award.</p>
                            <p>• Successfully completed the Graduation Project with the topic: "Development of UniSAST: A Web-based Platform Integrating Open-source SAST Tools for Automated Code Security Analysis and DevSecOps Support in SMEs."</p>
                        </div>
                    </div>
                </section>

                <!-- Work History -->
                <section>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 border-b border-gray-300 pb-2">WORK HISTORY</h2>
                    
                    <!-- Job 1 -->
                    <div class="bg-teal-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-base font-semibold text-gray-800">Cybersecurity Specialist</h3>
                            <span class="text-sm text-teal-600 bg-white px-2 py-1 rounded">08/2023 - 10/2024 (1 year 2 months)</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">ABC Technology Co., Ltd</p>
                        
                        <div class="text-sm text-gray-700 mb-3">
                            <p class="font-medium mb-2">Main Responsibilities:</p>
                            <div class="space-y-1 ml-4">
                                <p>• Monitor and analyze cybersecurity threats</p>
                                <p>• Deploy and manage security solutions for systems</p>
                                <p>• Conduct security assessments and penetration testing</p>
                                <p>• Train employees on information security awareness</p>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-700">
                            <p class="font-medium mb-2">Achievements:</p>
                            <div class="space-y-1 ml-4">
                                <p>• Reduced successful attacks on company systems by 85%</p>
                                <p>• Built effective cybersecurity incident response procedures</p>
                            </div>
                        </div>
                    </div>
                </section>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">