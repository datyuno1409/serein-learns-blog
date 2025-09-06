CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    content TEXT,
    image_url VARCHAR(500),
    technologies TEXT, -- JSON string of technologies array
    github_url VARCHAR(500),
    live_url VARCHAR(500),
    is_featured BOOLEAN DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active', -- active, inactive, draft
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO projects (title, slug, description, content, technologies, github_url, live_url, is_featured, status) VALUES
('UniSAST Platform', 'unisast-platform', 'A web-based platform integrating open-source SAST tools for automated code security analysis and DevSecOps support in SMEs. Built with React, Node.js, and Docker for comprehensive security scanning.', 
'<h2>Project Overview</h2><p>UniSAST Platform is a comprehensive web-based solution designed to integrate multiple open-source Static Application Security Testing (SAST) tools into a unified platform. The project aims to democratize code security analysis for small and medium enterprises (SMEs) by providing an accessible, automated, and comprehensive security scanning solution.</p><h2>Key Features</h2><ul><li>Multi-tool integration supporting popular SAST tools</li><li>Automated CI/CD pipeline integration</li><li>Comprehensive vulnerability reporting</li><li>User-friendly dashboard for security metrics</li><li>Docker-based containerization for easy deployment</li></ul>', 
'["React", "Node.js", "Docker", "Security", "DevSecOps"]', 
'https://github.com/serein7/unisast-platform', 
'https://unisast-demo.serein.dev', 
1, 'active'),

('Network Security Monitor', 'network-security-monitor', 'A network security monitoring tool that analyzes traffic patterns and detects potential security threats using machine learning algorithms.',
'<h2>Network Security Monitor</h2><p>This project implements a comprehensive network security monitoring solution that uses advanced machine learning algorithms to analyze network traffic patterns and identify potential security threats in real-time.</p><h2>Features</h2><ul><li>Real-time traffic analysis</li><li>Machine learning-based threat detection</li><li>Automated alert system</li><li>Comprehensive logging and reporting</li></ul>',
'["Python", "Machine Learning", "Network Security"]',
'https://github.com/serein7/network-security-monitor',
'',
0, 'active'),

('Learning Blog', 'learning-blog', 'A personal blog platform for sharing technology and security knowledge. Built with PHP and modern frontend technologies.',
'<h2>Learning Blog Platform</h2><p>A modern, responsive blog platform designed for sharing technical knowledge, tutorials, and insights about technology and cybersecurity. The platform features a clean, user-friendly interface and robust content management capabilities.</p><h2>Technical Stack</h2><ul><li>Backend: PHP with MVC architecture</li><li>Frontend: Modern JavaScript and CSS</li><li>Database: SQLite for lightweight deployment</li><li>Responsive design for all devices</li></ul>',
'["PHP", "JavaScript", "MySQL", "Tailwind CSS"]',
'https://github.com/serein7/learning-blog',
'https://blog.serein.dev',
1, 'active');