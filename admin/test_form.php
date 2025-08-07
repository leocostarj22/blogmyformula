<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/Blog.php';

session_start();

// Simular login de admin para teste
$_SESSION['admin_logged_in'] = true;

echo "<h1>Teste do Formulário de Edição</h1>";

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>📨 Dados POST Recebidos:</h2>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Verificar se é edição
    if (isset($_POST['edit_category'])) {
        echo "<h2>✅ Formulário de EDIÇÃO detectado!</h2>";
        
        $id = (int)$_POST['id'];
        $name = sanitizeInput($_POST['name'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        
        echo "ID: {$id}<br>";
        echo "Nome: {$name}<br>";
        echo "Descrição: {$description}<br>";
        
        // Testar atualização no banco
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE oc_blog_categories SET name = ?, description = ? WHERE id = ?");
            $result = $stmt->execute([$name, $description, $id]);
            
            if ($result) {
                echo "<h3 style='color: green;'>✅ ATUALIZAÇÃO REALIZADA COM SUCESSO!</h3>";
            } else {
                echo "<h3 style='color: red;'>❌ ERRO na atualização</h3>";
                echo "Erro: " . implode(' | ', $stmt->errorInfo());
            }
        } catch (Exception $e) {
            echo "<h3 style='color: red;'>❌ EXCEÇÃO: " . $e->getMessage() . "</h3>";
        }
    } else {
        echo "<h2>❌ NÃO é formulário de edição</h2>";
    }
} else {
    echo "<h2>📝 Formulário de Teste</h2>";
    
    // Buscar uma categoria para teste
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM oc_blog_categories LIMIT 1");
    $stmt->execute();
    $category = $stmt->fetch();
    
    if ($category) {
        echo "<form method='POST'>";
        echo "<input type='hidden' name='id' value='{$category['id']}'>";
        echo "<p>Nome: <input type='text' name='name' value='" . htmlspecialchars($category['name']) . "'></p>";
        echo "<p>Descrição: <textarea name='description'>" . htmlspecialchars($category['description']) . "</textarea></p>";
        echo "<button type='submit' name='edit_category'>Testar Edição</button>";
        echo "</form>";
    } else {
        echo "<p>❌ Nenhuma categoria encontrada para teste</p>";
    }
}
?>