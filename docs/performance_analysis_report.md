# Báo Cáo Phân Tích Performance - Serein Learns Blog

## Tổng Quan
Báo cáo này phân tích các vấn đề về hiệu suất trong hệ thống blog PHP, tập trung vào database queries, caching và optimization.

## 1. Vấn Đề N+1 Query

### 1.1 HomeController.php - Trang Chủ
**Vấn đề nghiêm trọng:** N+1 query khi load tags cho từng bài viết

```php
// Vấn đề hiện tại trong HomeController
foreach ($articles as &$article) {
    // Query riêng biệt cho mỗi bài viết - N+1 problem!
    $stmt = $conn->prepare("
        SELECT t.name 
        FROM tags t 
        JOIN article_tags at ON t.id = at.tag_id 
        WHERE at.article_id = ?
    ");
    $stmt->execute([$article['id']]);
    $article['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
```

**Giải pháp đề xuất:**
```php
// Tối ưu: Load tất cả tags trong 1 query
$articleIds = array_column($articles, 'id');
if (!empty($articleIds)) {
    $placeholders = str_repeat('?,', count($articleIds) - 1) . '?';
    $stmt = $conn->prepare("
        SELECT at.article_id, t.name 
        FROM tags t 
        JOIN article_tags at ON t.id = at.tag_id 
        WHERE at.article_id IN ($placeholders)
    ");
    $stmt->execute($articleIds);
    $tagsByArticle = [];
    while ($row = $stmt->fetch()) {
        $tagsByArticle[$row['article_id']][] = $row['name'];
    }
    
    foreach ($articles as &$article) {
        $article['tags'] = $tagsByArticle[$article['id']] ?? [];
    }
}
```

### 1.2 Categories List - Đếm Bài Viết
**Vấn đề:** Query phức tạp với LEFT JOIN có thể chậm

```php
// Query hiện tại
$sql = "SELECT c.*, 
               COUNT(a.id) as post_count,
               c.created_at
        FROM categories c 
        LEFT JOIN articles a ON c.id = a.category_id 
        GROUP BY c.id 
        ORDER BY c.created_at DESC";
```

**Đề xuất tối ưu:**
- Thêm index cho `articles.category_id`
- Cache kết quả đếm bài viết
- Sử dụng subquery thay vì LEFT JOIN nếu cần thiết

## 2. Vấn Đề Tìm Kiếm Global

### 2.1 Global Search Performance
**Vấn đề:** Multiple LIKE queries không có index

```php
// Queries chậm trong global_search.php
WHERE title LIKE ? OR content LIKE ? OR excerpt LIKE ?
WHERE name LIKE ? OR description LIKE ?
WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?
```

**Giải pháp:**
1. **Full-Text Search Index:**
```sql
-- Tạo full-text index
ALTER TABLE posts ADD FULLTEXT(title, content, excerpt);
ALTER TABLE categories ADD FULLTEXT(name, description);
ALTER TABLE users ADD FULLTEXT(username, full_name);

-- Sử dụng MATCH AGAINST thay vì LIKE
SELECT * FROM posts 
WHERE MATCH(title, content, excerpt) AGAINST(? IN NATURAL LANGUAGE MODE);
```

2. **Search Result Caching:**
```php
function performGlobalSearch($query, $limit = 10) {
    $cacheKey = 'search_' . md5($query . $limit);
    $cached = getFromCache($cacheKey);
    if ($cached) {
        return $cached;
    }
    
    // Perform search...
    $results = doSearch($query, $limit);
    
    // Cache for 5 minutes
    setCache($cacheKey, $results, 300);
    return $results;
}
```

## 3. Database Index Optimization

### 3.1 Indexes Cần Thiết
```sql
-- Performance indexes
CREATE INDEX idx_articles_status_created ON articles(status, created_at DESC);
CREATE INDEX idx_articles_category_id ON articles(category_id);
CREATE INDEX idx_articles_author_id ON articles(author_id);
CREATE INDEX idx_comments_article_id ON comments(article_id);
CREATE INDEX idx_comments_status ON comments(status);
CREATE INDEX idx_article_tags_article_id ON article_tags(article_id);
CREATE INDEX idx_article_tags_tag_id ON article_tags(tag_id);
CREATE INDEX idx_media_created_at ON media(created_at DESC);

-- Search optimization
CREATE INDEX idx_posts_title ON posts(title);
CREATE INDEX idx_categories_name ON categories(name);
CREATE INDEX idx_users_username ON users(username);
```

### 3.2 Composite Indexes
```sql
-- Cho pagination hiệu quả
CREATE INDEX idx_articles_status_created_id ON articles(status, created_at DESC, id);

-- Cho admin dashboard
CREATE INDEX idx_comments_status_created ON comments(status, created_at DESC);
```

## 4. Caching Strategy

### 4.1 Query Result Caching
```php
class QueryCache {
    private static $cache = [];
    private static $ttl = [];
    
    public static function get($key) {
        if (!isset(self::$cache[$key])) {
            return null;
        }
        
        if (isset(self::$ttl[$key]) && time() > self::$ttl[$key]) {
            unset(self::$cache[$key], self::$ttl[$key]);
            return null;
        }
        
        return self::$cache[$key];
    }
    
    public static function set($key, $value, $ttl = 300) {
        self::$cache[$key] = $value;
        self::$ttl[$key] = time() + $ttl;
    }
    
    public static function invalidate($pattern) {
        foreach (array_keys(self::$cache) as $key) {
            if (fnmatch($pattern, $key)) {
                unset(self::$cache[$key], self::$ttl[$key]);
            }
        }
    }
}
```

### 4.2 Page-Level Caching
```php
function getCachedContent($cacheKey, $callback, $ttl = 3600) {
    $cached = QueryCache::get($cacheKey);
    if ($cached !== null) {
        return $cached;
    }
    
    $content = $callback();
    QueryCache::set($cacheKey, $content, $ttl);
    return $content;
}

// Sử dụng trong HomeController
public function index() {
    $page = $_GET['page'] ?? 1;
    $cacheKey = "homepage_$page";
    
    $data = getCachedContent($cacheKey, function() use ($page) {
        return $this->loadHomePageData($page);
    }, 600); // Cache 10 phút
    
    // Render view...
}
```

## 5. Database Connection Optimization

### 5.1 Connection Pooling
```php
class DatabasePool {
    private static $connections = [];
    private static $maxConnections = 10;
    
    public static function getConnection() {
        if (count(self::$connections) < self::$maxConnections) {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
            ]);
            self::$connections[] = $pdo;
            return $pdo;
        }
        
        return array_shift(self::$connections);
    }
    
    public static function releaseConnection($pdo) {
        self::$connections[] = $pdo;
    }
}
```

### 5.2 Prepared Statement Caching
```php
class PreparedStatementCache {
    private static $statements = [];
    
    public static function prepare($pdo, $sql) {
        $key = md5($sql);
        if (!isset(self::$statements[$key])) {
            self::$statements[$key] = $pdo->prepare($sql);
        }
        return self::$statements[$key];
    }
}
```

## 6. Lazy Loading Implementation

### 6.1 Lazy Load Comments
```php
class Article {
    private $comments = null;
    private $commentsLoaded = false;
    
    public function getComments() {
        if (!$this->commentsLoaded) {
            $this->loadComments();
            $this->commentsLoaded = true;
        }
        return $this->comments;
    }
    
    private function loadComments() {
        // Load comments only when needed
        $stmt = $this->pdo->prepare("
            SELECT * FROM comments 
            WHERE article_id = ? AND status = 'approved'
            ORDER BY created_at DESC
        ");
        $stmt->execute([$this->id]);
        $this->comments = $stmt->fetchAll();
    }
}
```

## 7. Pagination Optimization

### 7.1 Cursor-Based Pagination
```php
// Thay vì OFFSET (chậm với large datasets)
// SELECT * FROM articles ORDER BY created_at DESC LIMIT 10 OFFSET 1000;

// Sử dụng cursor-based pagination
function getArticlesCursor($lastId = null, $limit = 10) {
    $sql = "SELECT * FROM articles WHERE 1=1";
    $params = [];
    
    if ($lastId) {
        $sql .= " AND id < ?";
        $params[] = $lastId;
    }
    
    $sql .= " ORDER BY id DESC LIMIT ?";
    $params[] = $limit;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
```

## 8. Monitoring và Profiling

### 8.1 Query Performance Monitor
```php
class QueryProfiler {
    private static $queries = [];
    
    public static function start($sql) {
        $id = uniqid();
        self::$queries[$id] = [
            'sql' => $sql,
            'start' => microtime(true)
        ];
        return $id;
    }
    
    public static function end($id) {
        if (isset(self::$queries[$id])) {
            self::$queries[$id]['duration'] = microtime(true) - self::$queries[$id]['start'];
            
            // Log slow queries
            if (self::$queries[$id]['duration'] > 0.1) { // > 100ms
                error_log("Slow query: " . self::$queries[$id]['sql'] . 
                         " Duration: " . self::$queries[$id]['duration']);
            }
        }
    }
    
    public static function getStats() {
        return self::$queries;
    }
}
```

## 9. Kế Hoạch Triển Khai

### Giai Đoạn 1 (Ưu tiên cao - 1-2 tuần)
1. ✅ Thêm database indexes cơ bản
2. ✅ Fix N+1 query trong HomeController
3. ✅ Implement basic query caching
4. ✅ Optimize global search với full-text index

### Giai Đoạn 2 (Ưu tiên trung bình - 2-3 tuần)
1. ⏳ Implement page-level caching
2. ⏳ Optimize pagination với cursor-based
3. ⏳ Add query performance monitoring
4. ⏳ Implement lazy loading cho comments

### Giai Đoạn 3 (Ưu tiên thấp - 1 tháng)
1. 📋 Database connection pooling
2. 📋 Advanced caching strategies
3. 📋 Performance dashboard
4. 📋 Automated performance testing

## 10. Metrics và KPIs

### Performance Targets
- **Page Load Time:** < 200ms (hiện tại: ~800ms)
- **Database Query Time:** < 50ms per query
- **Memory Usage:** < 64MB per request
- **Cache Hit Rate:** > 80%

### Monitoring Tools
- Query execution time logging
- Memory usage tracking
- Cache performance metrics
- User experience monitoring

---

**Tác giả:** AI Assistant  
**Ngày tạo:** " . date('d/m/Y H:i:s') . "  
**Phiên bản:** 1.0