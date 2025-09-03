<?php
// This file is included as content in the frontend layout
?>
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= __('projects.title') ?></h1>
                    <p class="text-gray-600"><?= __('projects.subtitle') ?></p>
                </div>
                <button class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i><?= __('projects.create_project') ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- UniSAST Platform Project -->
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">UniSAST Platform</h3>
                                <span class="inline-block bg-teal-100 text-teal-800 text-xs px-2 py-1 rounded-full font-medium">Featured</span>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        A web-based platform integrating open-source SAST tools for automated code security analysis and DevSecOps support in SMEs. Built with React, Node.js, and Docker.
                    </p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">React</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Node.js</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Docker</span>
                        <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Security</span>
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">DevSecOps</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <a href="#" class="flex items-center hover:text-gray-700">
                                <i class="fab fa-github mr-1"></i> GitHub
                            </a>
                            <a href="#" class="flex items-center hover:text-gray-700">
                                <i class="fas fa-external-link-alt mr-1"></i> Live Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Network Security Monitor Project -->
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-network-wired text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Network Security Monitor</h3>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        A network security monitoring tool that analyzes traffic patterns and detects potential security threats using machine learning algorithms.
                    </p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Python</span>
                        <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">Machine Learning</span>
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Network Security</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <a href="#" class="flex items-center hover:text-gray-700">
                                <i class="fab fa-github mr-1"></i> GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Blog Project -->
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-blog text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Learning Blog</h3>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        A personal blog platform for sharing technology and security knowledge. Built with PHP and modern frontend technologies.
                    </p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">PHP</span>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">JavaScript</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">MySQL</span>
                        <span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full">Tailwind CSS</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <a href="#" class="flex items-center hover:text-gray-700">
                                <i class="fab fa-github mr-1"></i> GitHub
                            </a>
                            <a href="#" class="flex items-center hover:text-gray-700">
                                <i class="fas fa-external-link-alt mr-1"></i> Live Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>