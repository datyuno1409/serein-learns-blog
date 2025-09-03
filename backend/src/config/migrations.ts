import db from './database';

export async function createTables() {
  // Check if articles table exists
  const hasArticlesTable = await db.schema.hasTable('articles');
  
  if (!hasArticlesTable) {
    await db.schema.createTable('articles', (table) => {
      table.increments('id').primary();
      table.string('title').notNullable();
      table.text('excerpt').notNullable();
      table.text('content').notNullable();
      table.string('coverImage').notNullable();
      table.string('author').notNullable();
      table.string('authorId').notNullable();
      table.string('authorImage').notNullable();
      table.string('category').notNullable();
      table.json('tags').notNullable();
      table.datetime('publishedAt').defaultTo(db.fn.now());
      table.integer('readTime').notNullable();
      table.boolean('featured').defaultTo(false);
      table.timestamps(true, true);
    });
    
    console.log('Created articles table');
  }
}

export async function dropTables() {
  await db.schema.dropTableIfExists('articles');
  console.log('Dropped all tables');
}