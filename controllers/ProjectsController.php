<?php

require_once 'models/Project.php';

class ProjectsController
{
    private $db;
    private $projectModel;
    
    public function __construct($database)
    {
        $this->db = $database;
        $this->projectModel = new Project($database);
    }
    
    public function index()
    {
        $projects = $this->projectModel->getAllProjects();
        require_once 'includes/Language.php';
        $page_title = 'My Projects - Learning with Serein';
        $content = 'views/myprojects/index.php';
        require 'views/layouts/frontend.php';
    }

    public function view($id)
    {
        $project = $this->getProjectById($id);
        
        if (!$project) {
            http_response_code(404);
            require 'views/404.php';
            return;
        }
        
        require_once 'includes/Language.php';
        $page_title = $project['title'] . ' - Learning with Serein';
        $page_css = 'assets/css/project-detail.css';
        $page_js = 'assets/js/project-detail.js';
        $content = 'views/myprojects/view.php';
        require 'views/layouts/frontend.php';
    }

    public function getProjectById($id) {
        return $this->projectModel->getProjectById($id);
    }
    
    public function show($id)
    {
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            http_response_code(404);
            require 'views/404.php';
            return;
        }
        
        require_once 'includes/Language.php';
        $page_title = $project['title'] . ' - Learning with Serein';
        
        // Include the view directly with the project data
        include 'views/myprojects/show.php';
    }
    
    public function detail($id) {
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            header('HTTP/1.0 404 Not Found');
            include 'views/errors/404.php';
            return;
        }
        
        include 'views/myprojects/detail.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $content = $_POST['content'] ?? '';
            $technologies = $_POST['technologies'] ?? [];
            $github_url = $_POST['github_url'] ?? '';
            $live_url = $_POST['live_url'] ?? '';
            $image_url = $_POST['image_url'] ?? '';
            $status = $_POST['status'] ?? 'planning';
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            if (empty($title) || empty($description)) {
                $error = 'Title and description are required';
            } else {
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $title)));
                
                $result = $this->projectModel->createProject([
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'content' => $content,
                    'technologies' => $technologies,
                    'github_url' => $github_url,
                    'live_url' => $live_url,
                    'image_url' => $image_url,
                    'status' => $status,
                    'is_featured' => $is_featured
                ]);
                
                if ($result) {
                    header('Location: /myprojects');
                    exit;
                } else {
                    $error = 'Failed to create project';
                }
            }
        }
        
        require_once 'includes/Language.php';
        $page_title = 'Create Project - Learning with Serein';
        $content = 'views/myprojects/create.php';
        require 'views/layouts/frontend.php';
    }
    
    public function edit($id) {
        $project = $this->projectModel->getProjectById($id);
        
        if (!$project) {
            http_response_code(404);
            require 'views/404.php';
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $content = $_POST['content'] ?? '';
            $technologies = $_POST['technologies'] ?? [];
            $github_url = $_POST['github_url'] ?? '';
            $live_url = $_POST['live_url'] ?? '';
            $image_url = $_POST['image_url'] ?? '';
            $status = $_POST['status'] ?? 'planning';
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            if (empty($title) || empty($description)) {
                $error = 'Title and description are required';
            } else {
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $title)));
                
                $result = $this->projectModel->updateProject($id, [
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'content' => $content,
                    'technologies' => $technologies,
                    'github_url' => $github_url,
                    'live_url' => $live_url,
                    'image_url' => $image_url,
                    'status' => $status,
                    'is_featured' => $is_featured
                ]);
                
                if ($result) {
                    header('Location: /myprojects');
                    exit;
                } else {
                    $error = 'Failed to update project';
                }
            }
        }
        
        require_once 'includes/Language.php';
        $page_title = 'Edit Project - Learning with Serein';
        $content = 'views/myprojects/edit.php';
        require 'views/layouts/frontend.php';
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->projectModel->deleteProject($id);
            
            if ($result) {
                header('Location: /myprojects');
                exit;
            } else {
                $error = 'Failed to delete project';
                header('Location: /myprojects?error=' . urlencode($error));
                exit;
            }
        }
        
        header('Location: /myprojects');
        exit;
    }
}