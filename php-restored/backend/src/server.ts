import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import mongoose from 'mongoose';
import path from 'path';
import articleRoutes from './routes/articleRoutes';

dotenv.config();

const app = express();

const corsOptions = {
  origin: [
    'http://localhost:8080',
    'http://localhost:8081',
    'http://localhost:8082',
    'http://localhost:3000'
  ],
  credentials: false,
  optionsSuccessStatus: 200
};

app.use(cors(corsOptions));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

app.use('/uploads', express.static(path.join(__dirname, '../uploads')));

app.use((req, res, next) => {
  console.log(`${req.method} ${req.url}`);
  next();
});

app.use('/api/articles', articleRoutes);

app.get('/api/status', (req, res) => {
  res.json({ status: 'ok', message: 'API is running' });
});

app.get('/', (req, res) => {
  res.json({ message: 'Server is running' });
});

async function connectMongo() {
  const MONGODB_URI = process.env.MONGODB_URI || 'mongodb://localhost:27017/serein-blog';
  try {
    await mongoose.connect(MONGODB_URI, {
      retryWrites: true,
      w: 'majority',
      maxPoolSize: 10,
      serverSelectionTimeoutMS: 5000,
      socketTimeoutMS: 45000,
    } as any);
    console.log('Connected to MongoDB');
  } catch (error) {
    console.error('MongoDB connection error, falling back to in-memory Mongo:', error);
    try {
      const { MongoMemoryServer } = await import('mongodb-memory-server');
      const mongod = await MongoMemoryServer.create();
      const uri = mongod.getUri();
      await mongoose.connect(uri);
      console.log('Connected to in-memory MongoDB');
    } catch (memError) {
      console.error('Failed to start in-memory MongoDB:', memError);
    }
  }
}

async function start() {
  await connectMongo();
  const PORT = process.env.PORT || 5000;
  app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
    console.log(`API available at http://localhost:${PORT}/api`);
  });
}

start();