<?php
require_once 'config/database.php';
require_once 'config/config.php';

// Conectar à base de dados
$database = new Database();
$db = $database->getConnection();

echo "🌱 Iniciando seeder...\n\n";

try {
    $db->beginTransaction();
    
    // Verificar se já existem posts
    $stmt = $db->query("SELECT COUNT(*) FROM " . DB_PREFIX . "blog_posts");
    $existing_posts = $stmt->fetchColumn();
    
    if ($existing_posts > 0) {
        echo "⚠️  Já existem {$existing_posts} posts na base de dados.\n";
        echo "Deseja continuar e adicionar mais posts? (s/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) !== 's') {
            echo "❌ Seeder cancelado.\n";
            exit;
        }
    }
    
    // Buscar categoria padrão
    $stmt = $db->query("SELECT id FROM " . DB_PREFIX . "blog_categories WHERE slug = 'geral' LIMIT 1");
    $category = $stmt->fetch();
    
    if (!$category) {
        echo "❌ Categoria 'Geral' não encontrada. Execute primeiro o SQL de criação das tabelas.\n";
        exit;
    }
    
    $category_id = $category['id'];
    
    // Posts de exemplo
    $sample_posts = [
        [
            'title' => 'Bem-vindos ao Nosso Blog!',
            'slug' => 'bem-vindos-ao-nosso-blog',
            'content' => '<p>Olá e bem-vindos ao nosso novo blog! Estamos muito entusiasmados por partilhar conteúdo interessante e útil convosco.</p>\n\n<p>Neste espaço, iremos abordar diversos temas relacionados com:</p>\n\n<ul>\n<li><strong>Tecnologia</strong> - As últimas novidades do mundo tech</li>\n<li><strong>Programação</strong> - Dicas e tutoriais para desenvolvedores</li>\n<li><strong>Design</strong> - Tendências e boas práticas</li>\n<li><strong>Negócios</strong> - Estratégias e insights empresariais</li>\n</ul>\n\n<p>Esperamos que gostem do conteúdo e não se esqueçam de deixar os vossos comentários!</p>\n\n<blockquote>\n<p>"O conhecimento partilhado é conhecimento multiplicado."</p>\n</blockquote>',
            'excerpt' => 'Bem-vindos ao nosso novo blog! Descubram o que temos preparado para vocês.',
            'featured' => 1,
            'status' => 'published',
            'meta_title' => 'Bem-vindos ao Nosso Blog - Conteúdo de Qualidade',
            'meta_description' => 'Descubra o nosso novo blog com conteúdo sobre tecnologia, programação, design e negócios. Bem-vindos à nossa comunidade!'
        ],
        [
            'title' => 'Como Criar um Blog Profissional em 2024',
            'slug' => 'como-criar-blog-profissional-2024',
            'content' => '<p>Criar um blog profissional em 2024 requer mais do que apenas escrever bom conteúdo. É necessário pensar em estratégia, design e otimização.</p>\n\n<h2>1. Escolha da Plataforma</h2>\n<p>A escolha da plataforma é crucial. Algumas opções populares incluem:</p>\n<ul>\n<li>WordPress</li>\n<li>Ghost</li>\n<li>Medium</li>\n<li>Sistemas personalizados (como este!)</li>\n</ul>\n\n<h2>2. Design Responsivo</h2>\n<p>O design deve ser:</p>\n<ul>\n<li>Responsivo para todos os dispositivos</li>\n<li>Rápido no carregamento</li>\n<li>Fácil de navegar</li>\n<li>Visualmente atrativo</li>\n</ul>\n\n<h2>3. SEO e Otimização</h2>\n<p>Não se esqueçam de:</p>\n<ul>\n<li>Otimizar títulos e meta descrições</li>\n<li>Usar palavras-chave relevantes</li>\n<li>Criar URLs amigáveis</li>\n<li>Implementar schema markup</li>\n</ul>\n\n<p>Com estas dicas, estarão no bom caminho para criar um blog de sucesso!</p>',
            'excerpt' => 'Guia completo para criar um blog profissional em 2024. Dicas sobre plataforma, design e SEO.',
            'featured' => 0,
            'status' => 'published',
            'meta_title' => 'Como Criar um Blog Profissional em 2024 - Guia Completo',
            'meta_description' => 'Aprenda a criar um blog profissional em 2024 com as melhores práticas de design, SEO e estratégia de conteúdo.'
        ],
        [
            'title' => 'As Melhores Práticas de PHP em 2024',
            'slug' => 'melhores-praticas-php-2024',
            'content' => '<p>O PHP continua a ser uma das linguagens mais populares para desenvolvimento web. Vamos explorar as melhores práticas para 2024.</p>\n\n<h2>1. Use PHP 8.x</h2>\n<p>O PHP 8 trouxe muitas melhorias:</p>\n<ul>\n<li>JIT Compiler</li>\n<li>Union Types</li>\n<li>Named Arguments</li>\n<li>Match Expression</li>\n</ul>\n\n<h2>2. Composer e Autoloading</h2>\n<p>Sempre use o Composer para gestão de dependências:</p>\n<pre><code>composer require vendor/package</code></pre>\n\n<h2>3. PSR Standards</h2>\n<p>Siga os padrões PSR:</p>\n<ul>\n<li>PSR-1: Basic Coding Standard</li>\n<li>PSR-2: Coding Style Guide</li>\n<li>PSR-4: Autoloader</li>\n<li>PSR-12: Extended Coding Style</li>\n</ul>\n\n<h2>4. Segurança</h2>\n<p>Pontos importantes de segurança:</p>\n<ul>\n<li>Validação e sanitização de inputs</li>\n<li>Prepared statements para SQL</li>\n<li>Escape de outputs</li>\n<li>Uso de HTTPS</li>\n</ul>\n\n<p>Seguindo estas práticas, o vosso código PHP será mais seguro, maintível e eficiente!</p>',
            'excerpt' => 'Descubra as melhores práticas de PHP para 2024. Dicas sobre versões, padrões e segurança.',
            'featured' => 1,
            'status' => 'published',
            'meta_title' => 'Melhores Práticas de PHP 2024 - Guia para Desenvolvedores',
            'meta_description' => 'Aprenda as melhores práticas de PHP para 2024. Dicas sobre PHP 8, Composer, PSR standards e segurança.'
        ],
        [
            'title' => 'Tendências de Design Web para 2024',
            'slug' => 'tendencias-design-web-2024',
            'content' => '<p>O design web está em constante evolução. Vamos explorar as principais tendências para 2024.</p>\n\n<h2>1. Dark Mode</h2>\n<p>O modo escuro continua popular:</p>\n<ul>\n<li>Reduz fadiga ocular</li>\n<li>Economiza bateria em dispositivos OLED</li>\n<li>Aparência moderna e elegante</li>\n</ul>\n\n<h2>2. Micro-interações</h2>\n<p>Pequenas animações que melhoram a experiência:</p>\n<ul>\n<li>Feedback visual para ações</li>\n<li>Transições suaves</li>\n<li>Loading states interessantes</li>\n</ul>\n\n<h2>3. Tipografia Bold</h2>\n<p>Fontes grandes e impactantes:</p>\n<ul>\n<li>Hierarquia visual clara</li>\n<li>Legibilidade melhorada</li>\n<li>Personalidade única</li>\n</ul>\n\n<h2>4. Cores Vibrantes</h2>\n<p>Paletas de cores ousadas:</p>\n<ul>\n<li>Gradientes modernos</li>\n<li>Contrastes elevados</li>\n<li>Cores neon e saturadas</li>\n</ul>\n\n<h2>5. Layouts Assimétricos</h2>\n<p>Quebrar a simetria tradicional:</p>\n<ul>\n<li>Layouts únicos e memoráveis</li>\n<li>Direcionamento visual inteligente</li>\n<li>Criatividade sem comprometer usabilidade</li>\n</ul>\n\n<p>Estas tendências podem ajudar a criar websites mais modernos e envolventes!</p>',
            'excerpt' => 'Explore as principais tendências de design web para 2024. Dark mode, micro-interações e muito mais.',
            'featured' => 0,
            'status' => 'published',
            'meta_title' => 'Tendências de Design Web 2024 - O Que Está em Alta',
            'meta_description' => 'Descubra as principais tendências de design web para 2024: dark mode, micro-interações, tipografia bold e cores vibrantes.'
        ],
        [
            'title' => 'Introdução ao Marketing Digital',
            'slug' => 'introducao-marketing-digital',
            'content' => '<p>O marketing digital é essencial para qualquer negócio moderno. Vamos explorar os conceitos básicos.</p>\n\n<h2>O Que é Marketing Digital?</h2>\n<p>Marketing digital engloba todas as estratégias de marketing que utilizam dispositivos eletrónicos ou internet.</p>\n\n<h2>Principais Canais</h2>\n<h3>1. SEO (Search Engine Optimization)</h3>\n<ul>\n<li>Otimização para motores de busca</li>\n<li>Tráfego orgânico</li>\n<li>Resultados a longo prazo</li>\n</ul>\n\n<h3>2. SEM (Search Engine Marketing)</h3>\n<ul>\n<li>Publicidade paga nos motores de busca</li>\n<li>Google Ads, Bing Ads</li>\n<li>Resultados imediatos</li>\n</ul>\n\n<h3>3. Redes Sociais</h3>\n<ul>\n<li>Facebook, Instagram, LinkedIn</li>\n<li>Engagement com audiência</li>\n<li>Construção de marca</li>\n</ul>\n\n<h3>4. Email Marketing</h3>\n<ul>\n<li>Comunicação direta</li>\n<li>Alto ROI</li>\n<li>Personalização</li>\n</ul>\n\n<h2>Métricas Importantes</h2>\n<ul>\n<li><strong>CTR</strong> - Click Through Rate</li>\n<li><strong>CPC</strong> - Cost Per Click</li>\n<li><strong>ROI</strong> - Return on Investment</li>\n<li><strong>CAC</strong> - Customer Acquisition Cost</li>\n</ul>\n\n<p>O marketing digital oferece possibilidades infinitas para fazer crescer o vosso negócio!</p>',
            'excerpt' => 'Guia introdutório ao marketing digital. Conheça os principais canais e métricas importantes.',
            'featured' => 0,
            'status' => 'published',
            'meta_title' => 'Introdução ao Marketing Digital - Guia para Iniciantes',
            'meta_description' => 'Aprenda os conceitos básicos do marketing digital: SEO, SEM, redes sociais, email marketing e métricas importantes.'
        ]
    ];
    
    echo "📝 Criando posts de exemplo...\n\n";
    
    $stmt = $db->prepare("
        INSERT INTO " . DB_PREFIX . "blog_posts 
        (title, slug, content, excerpt, category_id, status, featured, meta_title, meta_description, views, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    foreach ($sample_posts as $index => $post) {
        // Verificar se o post já existe
        $check_stmt = $db->prepare("SELECT id FROM oc_blog_posts WHERE slug = ?");
        $check_stmt->execute([$post['slug']]);
        
        if ($check_stmt->fetch()) {
            echo "⚠️  Post '{$post['title']}' já existe. A saltar...\n";
            continue;
        }
        
        // Views aleatórias para tornar mais realista
        $views = rand(10, 500);
        
        $stmt->execute([
            $post['title'],
            $post['slug'],
            $post['content'],
            $post['excerpt'],
            $category_id,
            $post['status'],
            $post['featured'],
            $post['meta_title'],
            $post['meta_description'],
            $views
        ]);
        
        echo "✅ Post criado: {$post['title']} ({$views} visualizações)\n";
    }
    
    // Criar algumas tags de exemplo
    echo "\n🏷️  Criando tags de exemplo...\n\n";
    
    $sample_tags = [
        ['name' => 'PHP', 'slug' => 'php'],
        ['name' => 'JavaScript', 'slug' => 'javascript'],
        ['name' => 'CSS', 'slug' => 'css'],
        ['name' => 'HTML', 'slug' => 'html'],
        ['name' => 'Design', 'slug' => 'design'],
        ['name' => 'SEO', 'slug' => 'seo'],
        ['name' => 'Marketing', 'slug' => 'marketing'],
        ['name' => 'Tutorial', 'slug' => 'tutorial'],
        ['name' => 'Dicas', 'slug' => 'dicas'],
        ['name' => 'Tendências', 'slug' => 'tendencias']
    ];
    
    $tag_stmt = $db->prepare("INSERT IGNORE INTO oc_blog_tags (name, slug) VALUES (?, ?)");
    
    foreach ($sample_tags as $tag) {
        $tag_stmt->execute([$tag['name'], $tag['slug']]);
        echo "🏷️  Tag criada: {$tag['name']}\n";
    }
    
    // Associar tags aos posts aleatoriamente
    echo "\n🔗 Associando tags aos posts...\n\n";
    
    $posts_stmt = $db->query("SELECT id FROM oc_blog_posts ORDER BY id DESC LIMIT 5");
    $posts = $posts_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $tags_stmt = $db->query("SELECT id FROM oc_blog_tags");
    $tags = $tags_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $post_tag_stmt = $db->prepare("INSERT IGNORE INTO oc_blog_post_tags (post_id, tag_id) VALUES (?, ?)");
    
    foreach ($posts as $post_id) {
        // Associar 2-4 tags aleatórias a cada post
        $num_tags = rand(2, 4);
        $selected_tags = array_rand(array_flip($tags), $num_tags);
        
        if (!is_array($selected_tags)) {
            $selected_tags = [$selected_tags];
        }
        
        foreach ($selected_tags as $tag_id) {
            $post_tag_stmt->execute([$post_id, $tag_id]);
        }
        
        echo "🔗 Post ID {$post_id} associado a " . count($selected_tags) . " tags\n";
    }
    
    $db->commit();
    
    echo "\n🎉 Seeder executado com sucesso!\n";
    echo "\n📊 Resumo:\n";
    echo "   - Posts criados: " . count($sample_posts) . "\n";
    echo "   - Tags criadas: " . count($sample_tags) . "\n";
    echo "   - Associações post-tag criadas\n";
    echo "\n🌐 Pode agora visitar o blog em: http://localhost/imc/\n";
    echo "🔧 Painel admin em: http://localhost/imc/admin/\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "❌ Erro ao executar seeder: " . $e->getMessage() . "\n";
}
?>