import mongoose, { Schema, Document } from 'mongoose';

export interface IArticle extends Document {
  title: string;
  excerpt: string;
  content: string;
  coverImage: string;
  author: string;
  authorId: string;
  authorImage: string;
  category: string;
  tags: string[];
  publishedAt: Date;
  readTime: number;
  featured: boolean;
  createdAt: Date;
  updatedAt: Date;
}

const ArticleSchema = new Schema<IArticle>({
  title: { type: String, required: true },
  excerpt: { type: String, required: true },
  content: { type: String, required: true },
  coverImage: { type: String, required: true },
  author: { type: String, required: true },
  authorId: { type: String, required: true },
  authorImage: { type: String, required: true },
  category: { type: String, required: true },
  tags: [{ type: String }],
  publishedAt: { type: Date, default: Date.now },
  readTime: { type: Number, required: true },
  featured: { type: Boolean, default: false }
}, {
  timestamps: true
});

export default mongoose.model<IArticle>('Article', ArticleSchema);