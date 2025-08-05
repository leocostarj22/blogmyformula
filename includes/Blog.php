<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class Blog {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Buscar posts com paginação
    public function getPosts($page = 1, $category_id = null, $search = null) {
        $offset = ($page - 1) * POSTS_PER_PAGE;
        $limit = POSTS_PER_PAGE;
        
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM " . DB_PREFIX . "blog_posts p 
                LEFT JOIN " . DB_PREFIX . "blog_categories c ON p.category_id = c.id 
                WHERE p.status = 'published'";
        
        $params = [];
        
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
        
        if ($search) {
            $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Buscar post por slug
    public function getPostBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM " . DB_PREFIX . "blog_posts p 
                LEFT JOIN " . DB_PREFIX . "blog_categories c ON p.category_id = c.id 
                WHERE p.slug = ? AND p.status = 'published'";
        
        $post = $this->db->fetch($sql, [$slug]);
        
        if ($post) {
            // Incrementar visualizações
            $this->incrementViews($post['id']);
            
            // Buscar tags do post
            $post['tags'] = $this->getPostTags($post['id']);
            
            // Garantir que meta_keywords existe (mesmo que vazio)
            if (!isset($post['meta_keywords'])) {
                $post['meta_keywords'] = '';
            }
        }
        
        return $post;
    }
    
    // Contar posts (método em falta)
    public function countPosts($category_id = null, $search = null) {
        $sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "blog_posts WHERE status = 'published'";
        $params = [];
        
        if ($category_id) {
            $sql .= " AND category_id = ?";
            $params[] = $category_id;
        }
        
        if ($search) {
            $sql .= " AND (title LIKE ? OR content LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }
    
    // Buscar categorias
    public function getCategories() {
        $sql = "SELECT * FROM " . DB_PREFIX . "blog_categories ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    // Buscar categoria por slug
    public function getCategoryBySlug($slug) {
        $sql = "SELECT * FROM " . DB_PREFIX . "blog_categories WHERE slug = ?";
        $category = $this->db->fetch($sql, [$slug]);
        
        // Garantir que as chaves meta sempre existam (mesmo que vazias)
        if ($category) {
            if (!isset($category['meta_title'])) {
                $category['meta_title'] = '';
            }
            if (!isset($category['meta_description'])) {
                $category['meta_description'] = '';
            }
        }
        
        return $category;
    }
    
    // Buscar tags de um post
    public function getPostTags($post_id) {
        $sql = "SELECT t.* FROM " . DB_PREFIX . "blog_tags t 
                JOIN " . DB_PREFIX . "blog_post_tags pt ON t.id = pt.tag_id 
                WHERE pt.post_id = ?";
        return $this->db->fetchAll($sql, [$post_id]);
    }
    
    // Incrementar visualizações
    private function incrementViews($post_id) {
        $sql = "UPDATE " . DB_PREFIX . "blog_posts SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$post_id]);
    }
    
    // Contar total de posts
    public function getTotalPosts($category_id = null, $search = null) {
        $sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "blog_posts WHERE status = 'published'";
        $params = [];
        
        if ($category_id) {
            $sql .= " AND category_id = ?";
            $params[] = $category_id;
        }
        
        if ($search) {
            $sql .= " AND (title LIKE ? OR content LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }
    
    // Buscar posts relacionados
    public function getRelatedPosts($post_id, $category_id, $limit = 3) {
        $sql = "SELECT * FROM " . DB_PREFIX . "blog_posts 
                WHERE category_id = ? AND id != ? AND status = 'published' 
                ORDER BY created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$category_id, $post_id, $limit]);
    }
    
    // Buscar posts em destaque
    public function getFeaturedPosts($limit = 3) {
        $sql = "SELECT p.*, c.name as category_name FROM " . DB_PREFIX . "blog_posts p 
                LEFT JOIN " . DB_PREFIX . "blog_categories c ON p.category_id = c.id 
                WHERE p.featured = 1 AND p.status = 'published' 
                ORDER BY p.created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    // Método auxiliar para buscar posts populares
    public function getPopularPosts($limit = 5) {
        $sql = "SELECT * FROM " . DB_PREFIX . "blog_posts WHERE status = 'published' ORDER BY views DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    // Método auxiliar para buscar posts recentes
    public function getRecentPosts($limit = 5) {
        $sql = "SELECT * FROM " . DB_PREFIX . "blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    // Método auxiliar para buscar tags com contagem
    public function getTagsWithCount() {
        $sql = "SELECT t.*, COUNT(pt.post_id) as post_count 
                FROM " . DB_PREFIX . "blog_tags t 
                LEFT JOIN " . DB_PREFIX . "blog_post_tags pt ON t.id = pt.tag_id 
                GROUP BY t.id 
                ORDER BY post_count DESC";
        return $this->db->fetchAll($sql);
    }

    // Método específico para o painel administrativo - buscar TODOS os posts
    public function getAllPosts($page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM " . DB_PREFIX . "blog_posts p 
                LEFT JOIN " . DB_PREFIX . "blog_categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$per_page, $offset]);
    }
    
    // Contar TODOS os posts para o painel administrativo
    public function countAllPosts() {
        $sql = "SELECT COUNT(*) as total FROM " . DB_PREFIX . "blog_posts";
        $result = $this->db->fetch($sql);
        return $result['total'];
    }
}
?>