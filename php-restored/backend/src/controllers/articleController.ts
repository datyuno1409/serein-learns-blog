import { Request, Response } from 'express';
import Article from '../models/Article';

// Create sample articles for testing
export const createSampleArticles = async (req: Request, res: Response) => {
  try {
    // Delete all existing articles first
    await Article.deleteMany({});
    console.log('Deleted all existing articles');

    const sampleArticles = [
      {
        title: 'Introduction to DevOps',
        excerpt: 'Learn the basics of DevOps methodology and tools',
        content: 'DevOps is a set of practices that combines software development and IT operations. It aims to shorten the systems development life cycle and provide continuous delivery with high software quality.',
        coverImage: 'https://via.placeholder.com/800x400?text=DevOps',
        author: 'Serein',
        authorId: 'serein1',
        authorImage: 'https://via.placeholder.com/100x100?text=Serein',
        category: 'DevOps',
        tags: ['devops', 'automation', 'ci/cd'],
        publishedAt: new Date(),
        readTime: 5
      },
      {
        title: 'Getting Started with Docker',
        excerpt: 'A beginner-friendly guide to Docker containers',
        content: 'Docker is a platform for developing, shipping, and running applications in containers. Containers allow developers to package an application with all its dependencies into a standardized unit.',
        coverImage: 'https://via.placeholder.com/800x400?text=Docker',
        author: 'Serein',
        authorId: 'serein1',
        authorImage: 'https://via.placeholder.com/100x100?text=Serein',
        category: 'DevOps',
        tags: ['docker', 'containers', 'devops'],
        publishedAt: new Date(),
        readTime: 7
      },
      {
        title: 'Web Security Fundamentals',
        excerpt: 'Learn essential web security concepts to protect your applications',
        content: 'Web security is critical for protecting websites and web applications from security threats that could affect business operations, compromise user data, or damage reputation.',
        coverImage: 'https://via.placeholder.com/800x400?text=Security',
        author: 'Serein',
        authorId: 'serein1',
        authorImage: 'https://via.placeholder.com/100x100?text=Serein',
        category: 'Cybersecurity',
        tags: ['security', 'web', 'owasp'],
        publishedAt: new Date(),
        readTime: 10
      }
    ];

    const created = await Article.insertMany(sampleArticles);
    console.log(`Created ${created.length} sample articles`);

    res.status(201).json({
      message: `Created ${created.length} sample articles`,
      articles: created
    });
  } catch (error) {
    console.error('Error creating sample articles:', error);
    res.status(500).json({ message: 'Error creating sample articles' });
  }
};

// Get all articles
export const getArticles = async (req: Request, res: Response) => {
  try {
    const page = parseInt(req.query.page as string) || 1;
    const limit = parseInt(req.query.limit as string) || 12;
    const skip = (page - 1) * limit;

    console.log(`Fetching articles page=${page}, limit=${limit}, skip=${skip}`);

    const articles = await Article.find()
      .sort({ publishedAt: -1 })
      .skip(skip)
      .limit(limit);

    const total = await Article.countDocuments();

    // Log the response format for debugging
    console.log(`Returning ${articles.length} articles, total: ${total}`);
    
    // Send the response directly - the toJSON transform in the schema will handle id conversion
    res.json(articles);
  } catch (error) {
    console.error('Error in getArticles:', error);
    res.status(500).json({ message: 'Error fetching articles' });
  }
};

// Get single article
export const getArticle = async (req: Request, res: Response) => {
  try {
    const article = await Article.findById(req.params.id);
    if (!article) {
      return res.status(404).json({ message: 'Article not found' });
    }
    res.json(article);
  } catch (error) {
    console.error('Error in getArticle:', error);
    res.status(500).json({ message: 'Error fetching article' });
  }
};

// Create article
export const createArticle = async (req: Request, res: Response) => {
  try {
    console.log('Creating article with data:', req.body);
    
    // Map uploaded file to coverImage field if present
    if ((req as any).file) {
      const uploadedFile = (req as any).file as Express.Multer.File;
      // Serve path that matches static route in server.ts
      req.body.coverImage = `/uploads/${uploadedFile.filename}`;
    }

    // Provide a fallback cover image if none uploaded/provided
    if (!req.body.coverImage) {
      req.body.coverImage = '/profile.jpg';
    }

    // Normalize tags to string array
    if (typeof req.body.tags === 'string') {
      try {
        // Accept JSON array string or comma-separated string
        const parsed = JSON.parse(req.body.tags);
        if (Array.isArray(parsed)) {
          req.body.tags = parsed;
        }
      } catch {
        req.body.tags = req.body.tags
          .split(',')
          .map((tag: string) => tag.trim())
          .filter((tag: string) => tag.length > 0);
      }
    }

    // If authorId is not provided in the request
    if (!req.body.authorId && req.user?.id) {
      req.body.authorId = req.user.id;
    }
    if (!req.body.authorId) {
      req.body.authorId = 'callmeserein';
    }
    
    // If author is not provided
    if (!req.body.author && req.user?.name) {
      req.body.author = req.user.name;
    }
    if (!req.body.author) {
      req.body.author = 'Serein';
    }
    
    // If authorImage is not provided
    if (!req.body.authorImage && req.user?.image) {
      req.body.authorImage = req.user.image;
    }
    if (!req.body.authorImage) {
      req.body.authorImage = '/profile.jpg';
    }
    
    // Calculate read time if not provided
    if (!req.body.readTime && req.body.content) {
      req.body.readTime = Math.ceil(req.body.content.split(' ').length / 200);
    }
    
    const article = new Article(req.body);
    
    const savedArticle = await article.save();
    console.log('Article created:', savedArticle.id);
    res.status(201).json(savedArticle);
  } catch (error) {
    console.error('Error in createArticle:', error);
    res.status(500).json({ message: 'Error creating article' });
  }
};

// Update article
export const updateArticle = async (req: Request, res: Response) => {
  try {
    const article = await Article.findById(req.params.id);
    
    if (!article) {
      return res.status(404).json({ message: 'Article not found' });
    }

    // Skip authorization check during development
    // if (article.authorId !== req.user?.id) {
    //   return res.status(403).json({ message: 'Not authorized' });
    // }

    // Map uploaded file to coverImage field if present
    if ((req as any).file) {
      const uploadedFile = (req as any).file as Express.Multer.File;
      req.body.coverImage = `/uploads/${uploadedFile.filename}`;
    }

    // Normalize tags to string array if provided
    if (typeof req.body.tags === 'string') {
      try {
        const parsed = JSON.parse(req.body.tags);
        if (Array.isArray(parsed)) {
          req.body.tags = parsed;
        }
      } catch {
        req.body.tags = req.body.tags
          .split(',')
          .map((tag: string) => tag.trim())
          .filter((tag: string) => tag.length > 0);
      }
    }

    // Calculate read time if content is updated
    if (req.body.content) {
      req.body.readTime = Math.ceil(req.body.content.split(' ').length / 200);
    }

    const updatedArticle = await Article.findByIdAndUpdate(
      req.params.id,
      req.body,
      { new: true }
    );

    console.log('Article updated:', updatedArticle?.id);
    res.json(updatedArticle);
  } catch (error) {
    console.error('Error in updateArticle:', error);
    res.status(500).json({ message: 'Error updating article' });
  }
};

// Delete article
export const deleteArticle = async (req: Request, res: Response) => {
  try {
    const article = await Article.findById(req.params.id);
    
    if (!article) {
      return res.status(404).json({ message: 'Article not found' });
    }

    // Skip authorization check during development
    // if (article.authorId !== req.user?.id) {
    //   return res.status(403).json({ message: 'Not authorized' });
    // }

    await article.deleteOne();
    console.log('Article deleted:', req.params.id);
    res.json({ message: 'Article deleted' });
  } catch (error) {
    console.error('Error in deleteArticle:', error);
    res.status(500).json({ message: 'Error deleting article' });
  }
}; 