<?php

class Project {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function getAllProjects() {
        $stmt = $this->db->prepare("SELECT * FROM projects ORDER BY is_featured DESC, created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProjectById($id) {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getProjectBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getFeaturedProjects() {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE is_featured = 1 ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createProject($data) {
        $stmt = $this->db->prepare("
            INSERT INTO projects (title, slug, description, content, image_url, github_url, live_url, technologies, status, is_featured) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['description'],
            $data['content'],
            $data['image_url'],
            $data['github_url'],
            $data['live_url'],
            json_encode($data['technologies']),
            $data['status'],
            $data['is_featured']
        ]);
    }
    
    public function updateProject($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE projects 
            SET title = ?, slug = ?, description = ?, content = ?, 
                image_url = ?, github_url = ?, live_url = ?, technologies = ?, status = ?, is_featured = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['description'],
            $data['content'],
            $data['image_url'],
            $data['github_url'],
            $data['live_url'],
            json_encode($data['technologies']),
            $data['status'],
            $data['is_featured'],
            $id
        ]);
    }
    
    public function deleteProject($id) {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id = ?");
        return $stmt->execute([$id]);
    }
}