<?php
require_once '../config/config.php';
require_once '../includes/Blog.php';

echo "<h1>Teste do Sistema de Posts</h1>";

// Testar conexão direta
try {
    $database = new Database();
    $db = $database->getConnection();
    echo "✅ Conexão direta OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão direta: " . $e->getMessage() . "<br>";
}

// Testar através da classe Blog
try {
    $blog = new Blog();
    $db2 = $blog->getConnection();
    echo "✅ Conexão via Blog OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão via Blog: " . $e->getMessage() . "<br>";
}

// Testar busca de posts
try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM oc_blog_posts");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "✅ Encontrados {$count} posts<br>";
} catch (Exception $e) {
    echo "❌ Erro ao buscar posts: " . $e->getMessage() . "<br>";
}

echo "<br><a href='posts.php'>Testar Posts.php</a>";
?>