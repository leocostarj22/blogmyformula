<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/Blog.php';

echo "<h1>Teste de Funções</h1>";

// Testar se as funções existem
if (function_exists('sanitizeInput')) {
    echo "✅ sanitizeInput() existe<br>";
} else {
    echo "❌ sanitizeInput() NÃO existe<br>";
}

if (function_exists('createSlug')) {
    echo "✅ createSlug() existe<br>";
} else {
    echo "❌ createSlug() NÃO existe<br>";
}

// Testar conexão com banco
try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Conexão com banco OK<br>";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Testar se consegue buscar categorias
try {
    $stmt = $db->prepare("SELECT COUNT(*) FROM oc_blog_categories");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "✅ Encontradas {$count} categorias<br>";
} catch (Exception $e) {
    echo "❌ Erro ao buscar categorias: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Se todas as funções existem e o banco funciona, o problema está na lógica do formulário.</strong>";
?>