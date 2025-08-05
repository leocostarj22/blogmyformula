<?php
// Arquivo para processar comentários via AJAX (opcional)
require_once 'Blog.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $blog = new Blog();
    
    $post_id = intval($_POST['post_id'] ?? 0);
    $author_name = sanitizeInput($_POST['author_name'] ?? '');
    $author_email = sanitizeInput($_POST['author_email'] ?? '');
    $comment_content = sanitizeInput($_POST['comment_content'] ?? '');
    
    if ($blog->addComment($post_id, $author_name, $author_email, $comment_content)) {
        echo json_encode([
            'success' => true,
            'message' => 'Comentário enviado com sucesso! Aguarde a aprovação.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao enviar comentário. Verifique os dados e tente novamente.'
        ]);
    }
} else {
    header('HTTP/1.0 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>