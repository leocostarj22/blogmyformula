<?php
require_once 'config/database.php';

echo "🗄️  Configurando base de dados...\n\n";

try {
    // Conectar sem especificar a base de dados primeiro
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "✅ Conexão estabelecida com sucesso!\n\n";
    
    // Criar base de dados se não existir
    echo "📦 Criando base de dados '" . DB_NAME . "'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    echo "✅ Base de dados criada/selecionada!\n\n";
    
    // Criar tabelas
    echo "🏗️  Criando tabelas...\n\n";
    
    // Tabela de categorias
    echo "📁 Criando tabela de categorias...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "blog_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            description TEXT,
            meta_title VARCHAR(255),
            meta_description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de posts (com campo image)
    echo "📝 Criando tabela de posts...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content LONGTEXT NOT NULL,
            excerpt TEXT,
            image VARCHAR(255) NULL,
            category_id INT,
            status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
            featured BOOLEAN DEFAULT FALSE,
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords TEXT,
            views INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES " . DB_PREFIX . "blog_categories(id) ON DELETE SET NULL,
            INDEX idx_status (status),
            INDEX idx_category (category_id),
            INDEX idx_featured (featured),
            INDEX idx_created (created_at)
        )
    ");
    
    // Verificar se a coluna image já existe, se não, adicionar
    echo "🖼️  Verificando coluna de imagem...\n";
    $result = $pdo->query("SHOW COLUMNS FROM " . DB_PREFIX . "blog_posts LIKE 'image'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE " . DB_PREFIX . "blog_posts ADD COLUMN image VARCHAR(255) NULL AFTER excerpt");
        echo "✅ Coluna 'image' adicionada à tabela de posts!\n";
    } else {
        echo "✅ Coluna 'image' já existe!\n";
    }
    
    // Tabela de tags
    echo "🏷️  Criando tabela de tags...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "blog_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabela de relacionamento posts-tags
    echo "🔗 Criando tabela de relacionamento posts-tags...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "blog_post_tags (
            post_id INT,
            tag_id INT,
            PRIMARY KEY (post_id, tag_id),
            FOREIGN KEY (post_id) REFERENCES " . DB_PREFIX . "blog_posts(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES " . DB_PREFIX . "blog_tags(id) ON DELETE CASCADE
        )
    ");
    
    // Tabela de comentários
    echo "💬 Criando tabela de comentários...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "blog_comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            author_name VARCHAR(100) NOT NULL,
            author_email VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES " . DB_PREFIX . "blog_posts(id) ON DELETE CASCADE,
            INDEX idx_post (post_id),
            INDEX idx_status (status)
        )
    ");
    
    // Tabela de utilizadores (corrigida)
    echo "👥 Criando tabela de utilizadores...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "blog_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'editor', 'author') DEFAULT 'author',
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Verificar e corrigir colunas da tabela de usuários
    echo "🔧 Verificando estrutura da tabela de usuários...\n";
    
    // Verificar se a coluna 'password' existe
    $result = $pdo->query("SHOW COLUMNS FROM " . DB_PREFIX . "blog_users LIKE 'password'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE " . DB_PREFIX . "blog_users ADD COLUMN password VARCHAR(255) NOT NULL AFTER email");
        echo "✅ Coluna 'password' adicionada!\n";
    }
    
    // Verificar se a coluna 'status' existe
    $result = $pdo->query("SHOW COLUMNS FROM " . DB_PREFIX . "blog_users LIKE 'status'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE " . DB_PREFIX . "blog_users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER role");
        echo "✅ Coluna 'status' adicionada!\n";
    }
    
    echo "\n✅ Todas as tabelas criadas com sucesso!\n\n";
    
    // Inserir dados iniciais
    echo "🌱 Inserindo dados iniciais...\n\n";
    
    // Categorias padrão
    echo "📁 Inserindo categorias padrão...\n";
    $categories = [
        ['Geral', 'geral', 'Categoria geral para posts diversos', 'Posts Gerais', 'Artigos e posts sobre temas diversos'],
        ['Tecnologia', 'tecnologia', 'Posts sobre tecnologia e inovação', 'Tecnologia', 'Últimas novidades em tecnologia e inovação'],
        ['Programação', 'programacao', 'Tutoriais e dicas de programação', 'Programação', 'Tutoriais, dicas e boas práticas de programação'],
        ['Design', 'design', 'Artigos sobre design e UX/UI', 'Design', 'Tendências e dicas de design web e gráfico'],
        ['Marketing', 'marketing', 'Estratégias e dicas de marketing digital', 'Marketing Digital', 'Estratégias e dicas para marketing digital eficaz']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO " . DB_PREFIX . "blog_categories (name, slug, description, meta_title, meta_description) VALUES (?, ?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
        echo "  ✅ Categoria: {$category[0]}\n";
    }
    
    // Tags padrão
    echo "\n🏷️  Inserindo tags padrão...\n";
    $tags = [
        ['PHP', 'php'],
        ['JavaScript', 'javascript'],
        ['CSS', 'css'],
        ['HTML', 'html'],
        ['MySQL', 'mysql'],
        ['Tutorial', 'tutorial'],
        ['Dicas', 'dicas'],
        ['Iniciante', 'iniciante'],
        ['Avançado', 'avancado'],
        ['Web Development', 'web-development']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO " . DB_PREFIX . "blog_tags (name, slug) VALUES (?, ?)");
    foreach ($tags as $tag) {
        $stmt->execute($tag);
        echo "  ✅ Tag: {$tag[0]}\n";
    }
    
    // Criar usuário administrador padrão
    echo "\n👤 Criando usuário administrador padrão...\n";
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO " . DB_PREFIX . "blog_users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@myformula.com.br', $admin_password, 'admin', 'active']);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Usuário administrador criado!\n";
        echo "   Username: admin\n";
        echo "   Password: admin123\n";
        echo "   Email: admin@myformula.com.br\n";
    } else {
        echo "ℹ️  Usuário administrador já existe!\n";
    }
    
    echo "\n🎉 Configuração da base de dados concluída com sucesso!\n";
    echo "\n📋 Próximos passos:\n";
    echo "   1. Execute 'php seeder.php' para adicionar posts de exemplo\n";
    echo "   2. Acesse o blog em: http://localhost:8000/\n";
    echo "   3. Acesse o painel admin em: http://localhost:8000/admin/\n";
    echo "   4. Faça login com: admin / admin123\n\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n🔧 Verifique:\n";
    echo "   - Se o MySQL está a correr\n";
    echo "   - Se as credenciais em config/database.php estão corretas\n";
    echo "   - Se o utilizador tem permissões para criar bases de dados\n";
}
?>