import express, { Request, Response, NextFunction } from 'express';
import multer from 'multer';
import path from 'path';
import fs from 'fs';
import mongoose from 'mongoose';
import {
  getArticles,
  getArticle,
  createArticle,
  updateArticle,
  deleteArticle,
  createSampleArticles
} from '../controllers/articleController';
import { protect } from '../middleware/authMiddleware';

const router = express.Router();

// Ensure uploads directory exists
const uploadsDir = path.join(__dirname, '../../uploads');
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true });
  console.log('Created uploads directory:', uploadsDir);
}

// Configure multer for file upload
const storage = multer.diskStorage({
  destination: function (req: Request, file: Express.Multer.File, cb) {
    cb(null, uploadsDir);
  },
  filename: function (req: Request, file: Express.Multer.File, cb) {
    cb(null, `${Date.now()}-${file.originalname}`);
  }
});

const upload = multer({ storage });

// Wrap controllers to ensure void return type
const asyncHandler = (fn: (req: Request, res: Response, next: NextFunction) => Promise<any>) => {
  return (req: Request, res: Response, next: NextFunction) => {
    Promise.resolve(fn(req, res, next)).catch(next);
  };
};

// Public routes
router.get('/', asyncHandler(async (req: Request, res: Response) => {
  await getArticles(req, res);
}));

router.get('/:id', asyncHandler(async (req: Request, res: Response) => {
  await getArticle(req, res);
}));

// Create sample articles for testing
router.post('/samples', asyncHandler(async (req: Request, res: Response) => {
  await createSampleArticles(req, res);
}));

router.get('/create-samples', asyncHandler(async (req: Request, res: Response) => {
  await createSampleArticles(req, res);
}));

// Protected routes (for now we'll skip the protection)
router.post('/', upload.single('coverImage'), asyncHandler(async (req: Request, res: Response) => {
  await createArticle(req, res);
}));

router.put('/:id', upload.single('coverImage'), asyncHandler(async (req: Request, res: Response) => {
  await updateArticle(req, res);
}));

router.delete('/:id', asyncHandler(async (req: Request, res: Response) => {
  await deleteArticle(req, res);
}));

// Clear all articles
router.delete('/', asyncHandler(async (req: Request, res: Response) => {
  try {
    const result = await mongoose.model('Article').deleteMany({});
    res.json({ message: `Deleted ${result.deletedCount} articles` });
  } catch (error) {
    console.error('Error clearing articles:', error);
    res.status(500).json({ message: 'Error clearing articles' });
  }
}));

export default router; 