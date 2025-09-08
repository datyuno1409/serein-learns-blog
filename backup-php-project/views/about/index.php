<?php
// This file is included as content in the frontend layout
?>

<!-- Professional CV-style Layout -->
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b-4 border-teal-500">
        <div class="max-w-6xl mx-auto px-8 py-12">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Profile Image -->
                <div class="w-40 h-40 rounded-lg overflow-hidden border-2 border-gray-200 shadow-md flex-shrink-0">
                    <img src="/public/profile.jpg" alt="Nguyen Thanh Dat" class="w-full h-full object-cover" onerror="this.src='/assets/images/default-avatar.svg'">
                </div>
                
                <!-- Professional Header Info -->
                <div class="flex-1">
                    <h1 class="text-4xl font-bold text-gray-800 mb-3 tracking-wide">NGUYEN THANH DAT</h1>
                    <h2 class="text-xl font-semibold text-teal-600 mb-4 uppercase tracking-wider">Technical Support Engineer</h2>
                    <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-briefcase text-teal-500"></i>
                            <span class="font-medium">1+ Years Experience</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-teal-500"></i>
                            <span>Ho Chi Minh City, Vietnam</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-teal-500"></i>
                            <span>Information Security Graduate</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-8 py-10">
        <div class="grid md:grid-cols-3 gap-10">
            <!-- Left Column -->
            <div class="md:col-span-1 space-y-8">
                <!-- Contact Info -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold mb-5 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Contact Information</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-phone text-teal-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                                <p class="font-semibold text-gray-800">+84 905922378</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-teal-500 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                                <p class="font-semibold text-gray-800">serein@example.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-teal-500 w-5 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Address</p>
                                <p class="font-semibold text-gray-800 leading-relaxed">Truong Tho, Thu Duc<br>Ho Chi Minh, Vietnam</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- About Me -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold mb-5 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Professional Summary</h2>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        <p class="mb-4">I emerged from the Information Security program at FPT University, equipped with comprehensive experience in cybersecurity testing and security project management.</p>
                        <p>What drives me every day is the desire to learn and become a Penetration Testing expert, helping businesses stand strong against all security challenges in today's digital landscape.</p>
                    </div>
                </section>

                <!-- Basic Information -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold mb-5 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Personal Details</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-gray-500 uppercase tracking-wide">Date of Birth</span>
                            <span class="font-semibold text-gray-800">14/09/2002</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-gray-500 uppercase tracking-wide">Nationality</span>
                            <span class="font-semibold text-gray-800">Vietnamese</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-gray-500 uppercase tracking-wide">Marital Status</span>
                            <span class="font-semibold text-gray-800">Single</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-gray-500 uppercase tracking-wide">Gender</span>
                            <span class="font-semibold text-gray-800">Male</span>
                        </div>
                    </div>
                </section>

                <!-- Skills -->
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold mb-5 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Core Competencies</h2>
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 gap-2">
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Cybersecurity & Penetration Testing</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Cloud Security Architecture</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Linux System Administration</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Windows Server Management</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Applied Cryptography</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">IT Infrastructure & Hardware</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Project Management</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">AI & Automation Tools</span>
                            </div>
                            <div class="flex items-center gap-3 py-1">
                                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">Web Development & WordPress</span>
                            </div>
                        </div>
                    </div>
                </section>


                </div>

            <!-- Right Column -->
            <div class="md:col-span-2 space-y-8">
                <!-- Education -->
                <section class="bg-white rounded-lg shadow-sm p-8">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Education</h2>
                    <div class="relative">
                        <!-- Timeline line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-teal-200"></div>
                        
                        <div class="relative pl-12">
                            <!-- Timeline dot -->
                            <div class="absolute left-2.5 top-2 w-3 h-3 bg-teal-500 rounded-full border-2 border-white shadow"></div>
                            
                            <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-teal-500">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">FPT University DaNang</h3>
                                        <p class="text-teal-600 font-semibold mb-2">Bachelor of Information Assurance</p>
                                    </div>
                                    <div class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        09/2020 - 01/2025
                                    </div>
                                </div>
                                
                                <div class="space-y-3 text-sm text-gray-700">
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-award text-teal-500 mt-1 flex-shrink-0"></i>
                                        <p>Served as a member of the Security Research Club from 09/2022 to 12/2023</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-trophy text-teal-500 mt-1 flex-shrink-0"></i>
                                        <p>Led the club in participating in competitions such as Hackathon, Secathon, and Bootcamp</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-star text-teal-500 mt-1 flex-shrink-0"></i>
                                        <p>Recognized as an Outstanding Student for academic excellence</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-users text-teal-500 mt-1 flex-shrink-0"></i>
                                        <p>Contributed to organizing security-related events, helping the club earn the Outstanding Club Award</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-graduation-cap text-teal-500 mt-1 flex-shrink-0"></i>
                                        <p><strong>Graduation Project:</strong> "Development of UniSAST: A Web-based Platform Integrating Open-source SAST Tools for Automated Code Security Analysis and DevSecOps Support in SMEs"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Work History -->
                <section class="bg-white rounded-lg shadow-sm p-8">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Professional Experience</h2>
                    
                    <div class="relative">
                        <!-- Timeline line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-teal-200"></div>
                        
                        <!-- Job 1 -->
                        <div class="relative pl-12">
                            <!-- Timeline dot -->
                            <div class="absolute left-2.5 top-2 w-3 h-3 bg-teal-500 rounded-full border-2 border-white shadow"></div>
                            
                            <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-teal-500">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">Technical Support Engineer</h3>
                                        <p class="text-teal-600 font-semibold mb-2">Viettel Cyber Security</p>
                                        <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-briefcase mr-1"></i>Full-time
                                        </span>
                                    </div>
                                    <div class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-medium mt-2 md:mt-0">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        01/2024 - Present
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                            <i class="fas fa-tasks text-teal-500 mr-2"></i>
                                            Key Responsibilities
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 ml-6">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-teal-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Provide technical support for cybersecurity products and services to enterprise clients</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-teal-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Troubleshoot and resolve complex technical issues related to network security and endpoint protection</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-teal-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Collaborate with cross-functional teams to implement security solutions</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-teal-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Conduct training sessions for clients on security best practices and product usage</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                            <i class="fas fa-trophy text-teal-500 mr-2"></i>
                                            Key Achievements
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 ml-6">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-star text-yellow-500 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Successfully resolved 95% of technical support tickets within SLA timeframes</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-star text-yellow-500 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Reduced average resolution time by 30% through process optimization and automation</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-star text-yellow-500 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Received recognition for outstanding customer service and technical expertise</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Job 2 -->
                        <div class="relative pl-12">
                            <!-- Timeline dot -->
                            <div class="absolute left-2.5 top-2 w-3 h-3 bg-gray-400 rounded-full border-2 border-white shadow"></div>
                            
                            <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-gray-400">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">Cybersecurity Intern</h3>
                                        <p class="text-gray-600 font-semibold mb-2">Viettel Cyber Security</p>
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-graduation-cap mr-1"></i>Internship
                                        </span>
                                    </div>
                                    <div class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium mt-2 md:mt-0">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        06/2023 - 12/2023
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                            <i class="fas fa-tasks text-gray-500 mr-2"></i>
                                            Key Responsibilities
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 ml-6">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-gray-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Assisted in vulnerability assessments and penetration testing for client systems</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-gray-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Supported the development and implementation of security policies and procedures</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-chevron-right text-gray-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Participated in incident response activities and security monitoring operations</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                            <i class="fas fa-trophy text-gray-500 mr-2"></i>
                                            Key Achievements
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 ml-6">
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-star text-yellow-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Successfully completed comprehensive cybersecurity training program</p>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-star text-yellow-400 mt-1 text-xs flex-shrink-0"></i>
                                                <p>Contributed to identifying and mitigating security vulnerabilities in client environments</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Certifications -->
                <section class="bg-white rounded-lg shadow-sm p-8">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 border-b-2 border-teal-500 pb-3 uppercase tracking-wide">Certifications & Credentials</h2>
                    <div class="grid gap-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 p-2 rounded-lg">
                                        <i class="fas fa-lock text-blue-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Applied Cryptography Specialization</h3>
                                        <p class="text-sm text-gray-600">Coursera • Issued 2023</p>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mb-2">Advanced knowledge in cryptographic algorithms, protocols, and security implementations.</p>
                            <a href="#" class="text-blue-600 text-sm hover:underline font-medium">View Certificate →</a>
                        </div>
                        
                        <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-lg p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <i class="fas fa-user-shield text-green-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">CertNexus Certified Ethical Emerging Technologist</h3>
                                        <p class="text-sm text-gray-600">Coursera • Issued 2023</p>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mb-2">Expertise in ethical considerations and responsible implementation of emerging technologies.</p>
                            <a href="#" class="text-green-600 text-sm hover:underline font-medium">View Certificate →</a>
                        </div>
                        
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-purple-100 p-2 rounded-lg">
                                        <i class="fas fa-shield-alt text-purple-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">ISC2 Systems Security Certified Practitioner (SSCP)</h3>
                                        <p class="text-sm text-gray-600">Coursera • Issued 2024</p>
                                    </div>
                                </div>
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mb-2">Comprehensive systems security knowledge covering access controls, security operations, and risk identification.</p>
                            <a href="#" class="text-purple-600 text-sm hover:underline font-medium">View Certificate →</a>
                        </div>
                        
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-6 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-orange-100 p-2 rounded-lg">
                                        <i class="fas fa-cloud text-orange-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Partner: Cloud Security</h3>
                                        <p class="text-sm text-gray-600">Cisco • Issued 2023</p>
                                    </div>
                                </div>
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mb-2">Specialized knowledge in cloud security architecture, implementation, and best practices.</p>
                            <a href="#" class="text-orange-600 text-sm hover:underline font-medium">View Certificate →</a>
                        </div>
                        
                        <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-lg p-6 border-l-4 border-cyan-500 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-cyan-100 p-2 rounded-lg">
                                        <i class="fas fa-network-wired text-cyan-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">CCNA: Switching, Routing, and Wireless Essentials</h3>
                                        <p class="text-sm text-gray-600">Cisco • Issued 2024</p>
                                    </div>
                                </div>
                                <span class="bg-cyan-100 text-cyan-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed mb-2">Fundamental networking skills in switching, routing protocols, and wireless network configuration.</p>
                            <a href="#" class="text-cyan-600 text-sm hover:underline font-medium">View Certificate →</a>
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