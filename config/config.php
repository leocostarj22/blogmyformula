<?php
// Configurações gerais do blog
define('SITE_URL', 'http://localhost:8000/');
define('BLOG_TITLE', 'MyFormula Blog');
define('BLOG_DESCRIPTION', 'Blog oficial da MyFormula - Suplementos e Nutrição Esportiva');
define('POSTS_PER_PAGE', 5);
define('ADMIN_EMAIL', 'contato@myformula.com.br');

// Configurações de upload
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configurações da marca
define('BRAND_NAME', 'MyFormula');
define('BRAND_LOGO', 'uploads/logo-myformula-colors.png');
define('BRAND_COLOR_PRIMARY', '#2B80B9');
define('BRAND_COLOR_SECONDARY', '#1a5a7a');

// Timezone
date_default_timezone_set('Europe/London');

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

// Função para obter a URL da logo
function getBrandLogo() {
    return SITE_URL . BRAND_LOGO;
}

// Função para detectar automaticamente a URL base (útil para desenvolvimento)
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = dirname($script);
    
    // Remove trailing slash se não for a raiz
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }
    
    return $protocol . '://' . $host . $path . '/';
}
?>