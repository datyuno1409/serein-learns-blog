
export const en = {
  // Navigation
  nav: {
    home: "Home",
    articles: "Articles",
    about: "About",
    search: "Search",
    createArticle: "Create Article",
    login: "Login",
    logout: "Logout",
    myProjects: "My Projects"
  },
  articles: {
    edit: 'Edit',
    delete: 'Delete',
    deleteConfirmTitle: 'Are you sure?',
    deleteConfirmMessage: 'This action cannot be undone. This will permanently delete the article.',
    deleteSuccess: 'Success',
    deleteSuccessMessage: 'Article deleted successfully',
    deleteError: 'Error',
    deleteErrorMessage: 'Failed to delete article',
    minuteRead: 'min read',
    notFound: 'No article found',
    adjustSearch: 'Try browsing all articles',
    minRead: 'min read',
    share: 'Share this article',
    copyLink: 'Copy Link',
    aboutAuthor: 'About the Author',
    techWriter: 'Technology Writer',
    related: 'Related Articles',
    view: 'View'
  },
  common: {
    cancel: 'Cancel',
    delete: 'Delete',
    verify: 'Verify',
    viewAll: 'View All Articles',
  },
  auth: {
    unauthorized: 'Unauthorized',
    loginRequired: 'You need to login to access this page.',
  },
  createArticle: {
    title: 'Edit Article',
    subtitle: 'Update your article content and settings',
    formTitle: 'Title',
    titlePlaceholder: 'Enter article title',
    excerpt: 'Excerpt',
    briefSummary: 'Brief summary',
    excerptPlaceholder: 'Enter a brief summary of your article',
    content: 'Content',
    contentPlaceholder: 'Write your article content here',
    coverImage: 'Cover Image',
    coverImagePlaceholder: 'Enter URL for cover image',
    category: 'Category',
    selectCategory: 'Select a category',
    tags: 'Tags',
    commaSeparated: 'comma separated',
    tagsPlaceholder: 'e.g. JavaScript, Security, React',
    publish: 'Update Article',
    publishing: 'Updating...',
    cancel: 'Cancel',
    missingFields: 'Missing required fields',
    fillRequiredFields: 'Please fill in all required fields.',
    success: 'Article updated!',
    successMsg: 'Your article has been successfully updated.',
  },
  education: {
    title: 'EDUCATION',
    university: 'FPT University Da Nang',
    degree: 'Bachelors - Information Assurance',
    period: '10/2020 - 12/2024 (4 years 2 months)',
    achievements: [
      'Served as a member of the Security Research Club from 09/2022 to 12/2023.',
      'Led the club in participating in competitions such as Hackathon, Secathon, Bootcamp, and Secathon Asean, among others.',
      'Recognized as an Outstanding Student for one year.',
      'Contributed to organizing security-related events, helping the club earn the Outstanding Club Award.',
      'Achieved Runner-up position for the Graduation Project with the topic: "Development of UniSAST: A Web-based Platform Integrating Open-source SAST Tools for Automated Code Security Analysis and DevSecOps Support in SMEs."'
    ]
  },
  about: {
    name: "NGUYEN THANH DAT",
    title: "TECHNICAL SUPPORT ENGINEER",
    experience: "1 YEAR OF EXPERIENCE",
    description: "Learn more about my background, skills and experience",
    skills: "Skills",
    certifications: "Certifications",
    education: "Education",
    achievements: "Key Achievements",
    contact: "CONTACT",
    downloadCV: "Download CV",
    phone: "Phone",
    email: "Email",
    address: "Address",
    addressValue: "Truong Tho Ward, Thu Duc City, Ho Chi Minh City, Vietnam"
  },
  myProjects: {
    title: "My Projects",
    subtitle: "A collection of my personal projects and contributions to open-source software",
    create: "Create Project",
    edit: "Edit Project",
    delete: "Delete Project",
    deleteConfirmTitle: "Delete Project?",
    deleteConfirmMessage: "This action cannot be undone. This will permanently delete the project.",
    deleteSuccess: "Project deleted successfully",
    deleteError: "Failed to delete project",
    form: {
      title: "Project Title",
      description: "Project Description",
      image: "Project Image URL",
      github: "GitHub Repository URL",
      demo: "Live Demo URL",
      tags: "Technologies Used",
      featured: "Featured Project",
      save: "Save Project",
      saving: "Saving...",
      cancel: "Cancel"
    }
  }
} as const;

export default en; 
