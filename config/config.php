<?php
// Configurações gerais do blog
define('SITE_URL', 'http://localhost/blogmyformula/');
define('BLOG_TITLE', 'Meu Blog');
define('BLOG_DESCRIPTION', 'Um blog incrível feito em PHP');
define('POSTS_PER_PAGE', 5);
define('ADMIN_EMAIL', 'admin@blog.com');

// Configurações de upload
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Função para gerar URLs amigáveis
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Função para formatar data
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

// Função para truncar texto
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Função para sanitizar entrada
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>