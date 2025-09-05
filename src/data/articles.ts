
export interface Article {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  coverImage: string;
  author: string;
  authorId: string;
  authorImage: string;
  category: string;
  tags: string[];
  publishedAt: string;
  readTime: number;
  featured?: boolean;
  views?: number;
}

export const articles: Article[] = [
  {
    id: "1",
    title: "The Future of Cybersecurity in an AI-Driven World",
    excerpt: "How artificial intelligence is reshaping the cybersecurity landscape and what it means for your digital safety.",
    content: `
      <p>The cybersecurity landscape is rapidly evolving with the integration of artificial intelligence. As organizations embrace AI to enhance their security posture, attackers are also leveraging these technologies to create more sophisticated threats.</p>
      
      <h2>The Rise of AI in Cybersecurity</h2>
      <p>AI and machine learning algorithms can analyze vast amounts of data to identify patterns and anomalies that might indicate a security breach. This capability has proven invaluable for security teams trying to stay ahead of emerging threats.</p>
      
      <p>However, the same technology that strengthens our defenses can also be weaponized. Adversarial attacks specifically designed to fool AI systems are becoming more common, creating a technological arms race between defenders and attackers.</p>
      
      <h2>Key Trends to Watch</h2>
      <ul>
        <li>Automated threat detection and response</li>
        <li>AI-powered social engineering attacks</li>
        <li>Deepfake technology in phishing campaigns</li>
        <li>Machine learning models for vulnerability prediction</li>
      </ul>
      
      <h2>Building Resilient Systems</h2>
      <p>The answer isn't to avoid AI, but to build systems that are resilient to AI-powered attacks. This includes implementing diverse layers of security, continuous monitoring, and regular testing against adversarial examples.</p>
      
      <p>As we move forward, the collaboration between human expertise and artificial intelligence will be crucial for maintaining robust security postures in an increasingly complex threat landscape.</p>
    `,
    coverImage: "https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    author: "Serein",
    authorId: "callmeserein",
    authorImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    category: "Cybersecurity",
    tags: ["AI", "Cybersecurity", "Machine Learning", "Digital Safety"],
    publishedAt: "2025-04-05T10:30:00Z",
    readTime: 6,
    featured: true
  },
  {
    id: "2",
    title: "Understanding Zero-Trust Architecture",
    excerpt: "How the zero-trust security model is changing network defense strategies and why organizations should adopt it.",
    content: `
      <p>Zero-trust architecture represents a fundamental shift in how organizations approach security. Rather than assuming everything inside the corporate network is safe, zero-trust operates on the principle of "never trust, always verify."</p>
      
      <h2>The Traditional Security Model</h2>
      <p>For decades, organizations have relied on a perimeter-based security model, where strong defenses at the network edge were assumed to keep threats outside. Once inside the network, users and devices were often given broad access.</p>
      
      <p>This model has proven insufficient in today's world of sophisticated threats, remote work, and cloud services that extend beyond traditional network boundaries.</p>
      
      <h2>Core Principles of Zero-Trust</h2>
      <ul>
        <li>Verify explicitly - Always authenticate and authorize based on all available data points</li>
        <li>Use least privilege access - Limit user access with Just-In-Time and Just-Enough-Access policies</li>
        <li>Assume breach - Minimize blast radius and segment access, verify end-to-end encryption, and use analytics to improve defenses</li>
      </ul>
      
      <h2>Implementing Zero-Trust</h2>
      <p>Moving to a zero-trust model requires careful planning and a phased approach. Organizations should begin by identifying their sensitive data, mapping the flows of that data, and building a zero-trust architecture to protect their most critical assets.</p>
      
      <p>While the transition may be challenging, the security benefits of a well-implemented zero-trust architecture make it worth the effort in an era of persistent and sophisticated threats.</p>
    `,
    coverImage: "https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    author: "Serein",
    authorId: "callmeserein",
    authorImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    category: "Cybersecurity",
    tags: ["Zero-Trust", "Network Security", "Architecture", "Defense Strategy"],
    publishedAt: "2025-04-01T14:45:00Z",
    readTime: 8,
    featured: false
  },
  {
    id: "3",
    title: "Web Development Trends for 2025",
    excerpt: "The most important web development technologies and methodologies that will shape the industry in 2025.",
    content: `
      <p>Web development continues to evolve at a rapid pace, with new technologies and approaches emerging regularly. Staying current with these trends is essential for developers who want to build modern, efficient, and user-friendly applications.</p>
      
      <h2>Frontend Frameworks</h2>
      <p>React, Vue, and Angular continue to dominate the frontend landscape, but meta-frameworks built on top of these foundations are gaining traction. These frameworks offer improved performance, better developer experience, and more streamlined deployment processes.</p>
      
      <h2>Server Components and Islands Architecture</h2>
      <p>The line between client and server rendering continues to blur with the adoption of server components. This approach allows developers to build applications that combine the best aspects of both server-side and client-side rendering for optimal performance and user experience.</p>
      
      <h2>Edge Computing</h2>
      <p>Moving computation closer to the user with edge functions and distributed computing models is becoming increasingly popular. This trend reduces latency and improves performance for users across the globe.</p>
      
      <h2>AI-Assisted Development</h2>
      <p>Artificial intelligence is changing how developers write code, with AI assistants helping to generate code, identify bugs, and optimize performance. These tools are becoming more sophisticated and integrate seamlessly into modern development workflows.</p>
      
      <p>As we move through 2025, web developers who embrace these trends will be well-positioned to build applications that are fast, secure, and provide exceptional user experiences across all devices.</p>
    `,
    coverImage: "https://images.unsplash.com/photo-1461749280684-dccba630e2f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    author: "Serein",
    authorId: "callmeserein",
    authorImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    category: "Web Development",
    tags: ["Web Development", "Frontend", "Edge Computing", "AI"],
    publishedAt: "2025-03-25T09:15:00Z",
    readTime: 7,
    featured: true
  },
  {
    id: "4",
    title: "The Importance of Quantum-Resistant Cryptography",
    excerpt: "Why organizations need to prepare for quantum computing threats to current encryption methods.",
    content: `
      <p>Quantum computers pose a significant threat to many of the cryptographic systems that currently secure our digital infrastructure. As quantum computing technology advances, organizations need to start preparing for the post-quantum era.</p>
      
      <h2>The Quantum Threat</h2>
      <p>Many widely-used cryptographic algorithms, including RSA and ECC, rely on mathematical problems that are difficult for classical computers to solve. However, quantum computers using Shor's algorithm could potentially break these systems in hours rather than the billions of years it would take traditional computers.</p>
      
      <h2>Timeline for Concern</h2>
      <p>While large-scale quantum computers capable of breaking current encryption don't exist yet, the threat is real enough that government agencies and standards bodies are already preparing. Organizations handling data that must remain secure for many years should be particularly concerned.</p>
      
      <h2>Quantum-Resistant Algorithms</h2>
      <p>The National Institute of Standards and Technology (NIST) has been working to standardize post-quantum cryptographic algorithms. These new algorithms are designed to resist attacks from both quantum and classical computers.</p>
      
      <h2>Steps to Prepare</h2>
      <ul>
        <li>Inventory your cryptographic assets to understand where vulnerable algorithms are used</li>
        <li>Develop crypto-agility to quickly replace algorithms when needed</li>
        <li>Begin testing post-quantum algorithms in non-production environments</li>
        <li>Create a transition plan for moving to quantum-resistant cryptography</li>
      </ul>
      
      <p>The shift to quantum-resistant cryptography represents one of the largest cryptographic transitions in computing history. Starting preparation now is essential for maintaining security in the quantum computing era.</p>
    `,
    coverImage: "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    author: "Serein",
    authorId: "callmeserein",
    authorImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    category: "Cryptography",
    tags: ["Quantum Computing", "Cryptography", "Security", "Encryption"],
    publishedAt: "2025-03-18T11:20:00Z",
    readTime: 9,
    featured: false
  },
  {
    id: "5",
    title: "Building Scalable Microservices Architecture",
    excerpt: "Best practices for designing, implementing, and maintaining microservices at scale.",
    content: `
      <p>Microservices architecture has become a standard approach for building complex, scalable applications. However, implementing microservices effectively requires careful planning and adherence to best practices.</p>
      
      <h2>Domain-Driven Design</h2>
      <p>Start with a solid understanding of your business domains. Each microservice should align with a bounded context in your domain model, with clear responsibilities and well-defined interfaces.</p>
      
      <h2>Inter-Service Communication</h2>
      <p>Choose appropriate communication patterns based on your requirements. Synchronous REST or gRPC might work well for some scenarios, while asynchronous messaging using queues or event streaming platforms like Kafka might be better for others.</p>
      
      <h2>Data Management</h2>
      <p>Properly managing data in a microservices architecture is crucial. Each service should own its data and provide controlled access to other services. Consider patterns like Command Query Responsibility Segregation (CQRS) for complex data scenarios.</p>
      
      <h2>Observability</h2>
      <p>Implement comprehensive logging, monitoring, and tracing across your microservices. Distributed tracing tools like Jaeger or Zipkin can help you understand request flows and identify bottlenecks in your system.</p>
      
      <h2>Deployment and Scaling</h2>
      <p>Containerization with Docker and orchestration with Kubernetes have become standard approaches for deploying microservices. Implement CI/CD pipelines to automate testing and deployment processes.</p>
      
      <p>While microservices offer many benefits, they also introduce complexity. Start with a monolith if your application is small, and extract microservices as your understanding of the domain and requirements evolve.</p>
    `,
    coverImage: "https://images.unsplash.com/photo-1531297484001-80022131f5a1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    author: "Serein",
    authorId: "callmeserein",
    authorImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    category: "Software Architecture",
    tags: ["Microservices", "Architecture", "Scalability", "DevOps"],
    publishedAt: "2025-03-10T15:30:00Z",
    readTime: 10,
    featured: false
  },
  {
    id: "6",
    title: "Securing Your Development Pipeline",
    excerpt: "How to implement DevSecOps practices to ensure security throughout your software development lifecycle.",
    content: `
      <p>Security can no longer be an afterthought in software development. DevSecOps integrates security practices throughout the development pipeline, ensuring vulnerabilities are caught and addressed early.</p>
      
      <h2>Shift Left Security</h2>
      <p>Moving security earlier in the development process—"shifting left"—helps identify vulnerabilities when they're easier and less expensive to fix. Implement security testing in your CI/CD pipeline to catch issues automatically.</p>
      
      <h2>Key Security Practices</h2>
      <ul>
        <li>Static Application Security Testing (SAST) to analyze source code for security vulnerabilities</li>
        <li>Software Composition Analysis (SCA) to identify vulnerabilities in dependencies</li>
        <li>Dynamic Application Security Testing (DAST) to test running applications</li>
        <li>Infrastructure as Code scanning to ensure secure infrastructure configurations</li>
        <li>Container security scanning for vulnerabilities in container images</li>
      </ul>
      
      <h2>Security as Code</h2>
      <p>Treat security configurations and policies as code, storing them in version control and applying the same rigor as application code. This approach ensures consistency and allows for automated testing of security configurations.</p>
      
      <h2>Continuous Security Monitoring</h2>
      <p>Implement runtime security monitoring to detect and respond to threats in production environments. This includes monitoring for unusual behavior, potential data breaches, and new vulnerabilities in deployed components.</p>
      
      <p>By integrating security throughout your development pipeline, you can deliver software that's not only functional but also secure by design.</p>
    `,
    coverImage: "https://images.unsplash.com/photo-1487058792275-0ad4aaf24ca7?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    author: "Serein",
    authorId: "callmeserein",
    authorImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
    category: "DevOps",
    tags: ["DevSecOps", "Security", "CI/CD", "Development"],
    publishedAt: "2025-03-05T13:40:00Z",
    readTime: 8,
    featured: false
  }
];

// Add local storage key
const STORAGE_KEY = 'blog_articles';

// Load articles from storage or use default
export const loadArticles = (): Article[] => {
  const savedArticles = localStorage.getItem(STORAGE_KEY);
  if (savedArticles) {
    return JSON.parse(savedArticles);
  }
  return articles;
};

// Save articles to storage
export const saveArticles = (articles: Article[]) => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(articles));
};

// Add new article
export const addArticle = (article: Omit<Article, "id">): Article => {
  const newArticle = {
    ...article,
    id: Date.now().toString(), // Generate unique ID
  };
  
  const currentArticles = loadArticles();
  const updatedArticles = [newArticle, ...currentArticles];
  saveArticles(updatedArticles);
  
  return newArticle;
};

// Update existing article
export const updateArticle = (id: string, article: Partial<Article>): Article | undefined => {
  const currentArticles = loadArticles();
  const index = currentArticles.findIndex(a => a.id === id);
  
  if (index === -1) return undefined;
  
  const updatedArticle = {
    ...currentArticles[index],
    ...article,
  };
  
  currentArticles[index] = updatedArticle;
  saveArticles(currentArticles);
  
  return updatedArticle;
};

// Delete article
export const deleteArticle = (id: string): boolean => {
  const currentArticles = loadArticles();
  const filteredArticles = currentArticles.filter(a => a.id !== id);
  
  if (filteredArticles.length === currentArticles.length) {
    return false;
  }
  
  saveArticles(filteredArticles);
  return true;
};

// Clear all articles
export const clearAllArticles = (): boolean => {
  try {
    saveArticles([]);
    return true;
  } catch (error) {
    console.error("Failed to clear articles:", error);
    return false;
  }
};

export function getArticlesByCategory(category: string): Article[] {
  const currentArticles = loadArticles();
  return currentArticles.filter(article => article.category === category);
}

export function getArticleById(id: string): Article | undefined {
  const currentArticles = loadArticles();
  return currentArticles.find(article => article.id === id);
}

export function getFeaturedArticles(): Article[] {
  const currentArticles = loadArticles();
  return currentArticles.filter(article => article.featured);
}

export function getLatestArticles(count: number = 3): Article[] {
  const currentArticles = loadArticles();
  return [...currentArticles]
    .sort((a, b) => new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime())
    .slice(0, count);
}

export function getArticlesByTag(tag: string): Article[] {
  const currentArticles = loadArticles();
  return currentArticles.filter(article => article.tags.includes(tag));
}

export function searchArticles(query: string): Article[] {
  const currentArticles = loadArticles();
  const lowerCaseQuery = query.toLowerCase();
  return currentArticles.filter(article =>
    article.title.toLowerCase().includes(lowerCaseQuery) ||
    article.excerpt.toLowerCase().includes(lowerCaseQuery) ||
    article.content.toLowerCase().includes(lowerCaseQuery) ||
    article.tags.some(tag => tag.toLowerCase().includes(lowerCaseQuery))
  );
}
