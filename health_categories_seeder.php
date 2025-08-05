<?php
require_once 'config/database.php';
require_once 'config/config.php';

// Conectar à base de dados
$database = new Database();
$db = $database->getConnection();

echo "🏥 Iniciando seeder de categorias de saúde...\n\n";

try {
    $db->beginTransaction();
    
    // Categorias de saúde
    $health_categories = [
        [
            'name' => 'Perda de Peso',
            'slug' => 'perda-de-peso',
            'description' => 'Dicas, estratégias e informações sobre emagrecimento saudável e sustentável.',
            'meta_title' => 'Perda de Peso - Dicas e Estratégias para Emagrecer',
            'meta_description' => 'Descubra as melhores estratégias para perda de peso saudável. Dicas de alimentação, exercícios e hábitos para emagrecer de forma sustentável.'
        ],
        [
            'name' => 'Menopausa',
            'slug' => 'menopausa',
            'description' => 'Informações sobre menopausa, sintomas, tratamentos e como viver bem nesta fase da vida.',
            'meta_title' => 'Menopausa - Sintomas, Tratamentos e Bem-estar',
            'meta_description' => 'Tudo sobre menopausa: sintomas, tratamentos naturais, terapia hormonal e dicas para viver bem durante esta transição.'
        ],
        [
            'name' => 'Anti-aging',
            'slug' => 'anti-aging',
            'description' => 'Estratégias e produtos para retardar o envelhecimento e manter a juventude.',
            'meta_title' => 'Anti-aging - Estratégias para Retardar o Envelhecimento',
            'meta_description' => 'Descubra as melhores estratégias anti-aging: cuidados com a pele, suplementos, exercícios e hábitos para manter a juventude.'
        ],
        [
            'name' => 'Saúde Sexual',
            'slug' => 'saude-sexual',
            'description' => 'Informações sobre saúde sexual, libido, disfunções e bem-estar íntimo.',
            'meta_title' => 'Saúde Sexual - Bem-estar e Qualidade de Vida Íntima',
            'meta_description' => 'Informações sobre saúde sexual: libido, disfunções, tratamentos e dicas para melhorar a qualidade de vida íntima.'
        ],
        [
            'name' => 'Sono',
            'slug' => 'sono',
            'description' => 'Dicas para melhorar a qualidade do sono, tratar insónia e ter noites reparadoras.',
            'meta_title' => 'Sono - Dicas para Dormir Melhor e Tratar Insónia',
            'meta_description' => 'Aprenda a melhorar a qualidade do sono: dicas para dormir melhor, tratar insónia e ter noites verdadeiramente reparadoras.'
        ]
    ];
    
    echo "📁 Criando categorias de saúde...\n\n";
    
    $stmt = $db->prepare("
        INSERT INTO " . DB_PREFIX . "blog_categories 
        (name, slug, description, meta_title, meta_description, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
        description = VALUES(description),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        updated_at = NOW()
    ");
    
    $created_count = 0;
    $updated_count = 0;
    
    foreach ($health_categories as $category) {
        // Verificar se a categoria já existe
        $check_stmt = $db->prepare("SELECT id FROM " . DB_PREFIX . "blog_categories WHERE slug = ?");
        $check_stmt->execute([$category['slug']]);
        $existing = $check_stmt->fetch();
        
        $stmt->execute([
            $category['name'],
            $category['slug'],
            $category['description'],
            $category['meta_title'],
            $category['meta_description']
        ]);
        
        if ($existing) {
            echo "🔄 Categoria atualizada: {$category['name']}\n";
            $updated_count++;
        } else {
            echo "✅ Categoria criada: {$category['name']}\n";
            $created_count++;
        }
    }
    
    // Criar tags relacionadas com saúde
    echo "\n🏷️  Criando tags de saúde...\n\n";
    
    $health_tags = [
        ['name' => 'Emagrecimento', 'slug' => 'emagrecimento'],
        ['name' => 'Dieta', 'slug' => 'dieta'],
        ['name' => 'Exercício', 'slug' => 'exercicio'],
        ['name' => 'Nutrição', 'slug' => 'nutricao'],
        ['name' => 'Hormônios', 'slug' => 'hormonios'],
        ['name' => 'Terapia Hormonal', 'slug' => 'terapia-hormonal'],
        ['name' => 'Sintomas', 'slug' => 'sintomas'],
        ['name' => 'Tratamento Natural', 'slug' => 'tratamento-natural'],
        ['name' => 'Cuidados com a Pele', 'slug' => 'cuidados-pele'],
        ['name' => 'Suplementos', 'slug' => 'suplementos'],
        ['name' => 'Antioxidantes', 'slug' => 'antioxidantes'],
        ['name' => 'Colágeno', 'slug' => 'colageno'],
        ['name' => 'Libido', 'slug' => 'libido'],
        ['name' => 'Disfunção Sexual', 'slug' => 'disfuncao-sexual'],
        ['name' => 'Bem-estar Íntimo', 'slug' => 'bem-estar-intimo'],
        ['name' => 'Relacionamento', 'slug' => 'relacionamento'],
        ['name' => 'Insónia', 'slug' => 'insonia'],
        ['name' => 'Qualidade do Sono', 'slug' => 'qualidade-sono'],
        ['name' => 'Melatonina', 'slug' => 'melatonina'],
        ['name' => 'Higiene do Sono', 'slug' => 'higiene-sono'],
        ['name' => 'Stress', 'slug' => 'stress'],
        ['name' => 'Relaxamento', 'slug' => 'relaxamento'],
        ['name' => 'Saúde da Mulher', 'slug' => 'saude-mulher'],
        ['name' => 'Longevidade', 'slug' => 'longevidade'],
        ['name' => 'Prevenção', 'slug' => 'prevencao']
    ];
    
    $tag_stmt = $db->prepare("
        INSERT INTO " . DB_PREFIX . "blog_tags (name, slug, created_at) 
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE name = VALUES(name)
    ");
    
    $tags_created = 0;
    foreach ($health_tags as $tag) {
        $tag_stmt->execute([$tag['name'], $tag['slug']]);
        echo "🏷️  Tag: {$tag['name']}\n";
        $tags_created++;
    }
    
    // Criar alguns posts de exemplo para cada categoria
    echo "\n📝 Criando posts de exemplo para as categorias de saúde...\n\n";
    
    $sample_health_posts = [
        // Perda de Peso
        [
            'title' => '10 Dicas Eficazes para Perda de Peso Saudável',
            'slug' => '10-dicas-perda-peso-saudavel',
            'content' => '<p>Perder peso de forma saudável é um objetivo comum, mas nem sempre fácil de alcançar. Aqui estão 10 dicas comprovadas para ajudar na sua jornada de emagrecimento.</p>

<h2>1. Estabeleça Metas Realistas</h2>
<p>Defina objetivos alcançáveis e mensuráveis. Perder 0,5 a 1 kg por semana é considerado saudável e sustentável.</p>

<h2>2. Mantenha um Diário Alimentar</h2>
<p>Registar o que come ajuda a identificar padrões e áreas de melhoria na sua alimentação.</p>

<h2>3. Beba Mais Água</h2>
<p>A água ajuda na saciedade e pode acelerar o metabolismo. Beba pelo menos 2 litros por dia.</p>

<h2>4. Inclua Proteína em Todas as Refeições</h2>
<p>A proteína aumenta a saciedade e ajuda a preservar a massa muscular durante a perda de peso.</p>

<h2>5. Pratique Exercício Regularmente</h2>
<p>Combine exercícios cardiovasculares com treino de força para melhores resultados.</p>

<h2>6. Durma o Suficiente</h2>
<p>A falta de sono pode afetar hormônios que regulam a fome e a saciedade.</p>

<h2>7. Gerir o Stress</h2>
<p>O stress crónico pode levar ao aumento de peso. Pratique técnicas de relaxamento.</p>

<h2>8. Coma Devagar</h2>
<p>Mastigar bem e comer devagar ajuda o cérebro a reconhecer a saciedade.</p>

<h2>9. Planeie as Refeições</h2>
<p>Ter um plano alimentar evita escolhas impulsivas e pouco saudáveis.</p>

<h2>10. Seja Paciente e Consistente</h2>
<p>A perda de peso sustentável leva tempo. Mantenha-se focado nos seus objetivos.</p>

<p>Lembre-se: consulte sempre um profissional de saúde antes de iniciar qualquer programa de perda de peso.</p>',
            'excerpt' => 'Descubra 10 dicas comprovadas para perder peso de forma saudável e sustentável.',
            'category_slug' => 'perda-de-peso',
            'featured' => 1,
            'tags' => ['emagrecimento', 'dieta', 'exercicio', 'nutricao']
        ],
        
        // Menopausa
        [
            'title' => 'Menopausa: Sintomas e Como Lidar com Esta Fase',
            'slug' => 'menopausa-sintomas-como-lidar',
            'content' => '<p>A menopausa é uma fase natural da vida da mulher que marca o fim dos ciclos menstruais. Compreender os sintomas e saber como lidar com eles é fundamental para manter a qualidade de vida.</p>

<h2>O Que é a Menopausa?</h2>
<p>A menopausa ocorre quando os ovários param de produzir estrogénio e progesterona, geralmente entre os 45 e 55 anos.</p>

<h2>Sintomas Comuns</h2>
<h3>Sintomas Físicos:</h3>
<ul>
<li>Afrontamentos (ondas de calor)</li>
<li>Suores noturnos</li>
<li>Irregularidades menstruais</li>
<li>Secura vaginal</li>
<li>Alterações no sono</li>
<li>Ganho de peso</li>
</ul>

<h3>Sintomas Emocionais:</h3>
<ul>
<li>Alterações de humor</li>
<li>Irritabilidade</li>
<li>Ansiedade</li>
<li>Depressão</li>
<li>Dificuldades de concentração</li>
</ul>

<h2>Estratégias para Lidar com a Menopausa</h2>

<h3>1. Alimentação Equilibrada</h3>
<p>Inclua alimentos ricos em cálcio, vitamina D e fitoestrogénios como a soja.</p>

<h3>2. Exercício Regular</h3>
<p>Ajuda a controlar o peso, fortalecer os ossos e melhorar o humor.</p>

<h3>3. Gestão do Stress</h3>
<p>Pratique yoga, meditação ou outras técnicas de relaxamento.</p>

<h3>4. Terapia Hormonal</h3>
<p>Consulte o seu médico sobre as opções de terapia hormonal disponíveis.</p>

<h3>5. Suplementos Naturais</h3>
<p>Alguns suplementos como o trevo vermelho podem ajudar com os sintomas.</p>

<p>Lembre-se: cada mulher vive a menopausa de forma diferente. É importante ter acompanhamento médico regular.</p>',
            'excerpt' => 'Compreenda os sintomas da menopausa e descubra estratégias eficazes para lidar com esta fase da vida.',
            'category_slug' => 'menopausa',
            'featured' => 1,
            'tags' => ['hormonios', 'sintomas', 'terapia-hormonal', 'saude-mulher']
        ],
        
        // Anti-aging
        [
            'title' => 'Estratégias Anti-aging: Como Retardar o Envelhecimento',
            'slug' => 'estrategias-anti-aging-retardar-envelhecimento',
            'content' => '<p>O envelhecimento é um processo natural, mas existem estratégias comprovadas que podem ajudar a retardá-lo e manter uma aparência jovem e saudável.</p>

<h2>Cuidados com a Pele</h2>
<h3>Proteção Solar</h3>
<p>Use protetor solar diariamente, mesmo em dias nublados. Os raios UV são a principal causa do envelhecimento precoce.</p>

<h3>Hidratação</h3>
<p>Mantenha a pele hidratada com cremes adequados ao seu tipo de pele.</p>

<h3>Ingredientes Anti-aging</h3>
<ul>
<li><strong>Retinol:</strong> Estimula a renovação celular</li>
<li><strong>Vitamina C:</strong> Antioxidante poderoso</li>
<li><strong>Ácido Hialurónico:</strong> Hidratação profunda</li>
<li><strong>Peptídeos:</strong> Estimulam a produção de colágeno</li>
</ul>

<h2>Alimentação Anti-aging</h2>
<h3>Antioxidantes</h3>
<p>Inclua alimentos ricos em antioxidantes:</p>
<ul>
<li>Frutos vermelhos</li>
<li>Vegetais de folha verde</li>
<li>Chá verde</li>
<li>Chocolate negro</li>
<li>Nozes e sementes</li>
</ul>

<h3>Ómega-3</h3>
<p>Peixes gordos, sementes de linhaça e nozes ajudam a manter a pele saudável.</p>

<h2>Suplementos</h2>
<ul>
<li><strong>Colágeno:</strong> Melhora a elasticidade da pele</li>
<li><strong>Coenzima Q10:</strong> Antioxidante celular</li>
<li><strong>Resveratrol:</strong> Propriedades anti-aging</li>
<li><strong>Vitamina E:</strong> Proteção contra radicais livres</li>
</ul>

<h2>Estilo de Vida</h2>
<h3>Exercício Regular</h3>
<p>Melhora a circulação, fortalece músculos e mantém a pele saudável.</p>

<h3>Sono de Qualidade</h3>
<p>Durante o sono, o corpo repara e regenera as células.</p>

<h3>Gestão do Stress</h3>
<p>O stress crónico acelera o envelhecimento. Pratique técnicas de relaxamento.</p>

<p>Lembre-se: a consistência é fundamental. Resultados anti-aging requerem tempo e dedicação.</p>',
            'excerpt' => 'Descubra estratégias eficazes anti-aging para retardar o envelhecimento e manter uma aparência jovem.',
            'category_slug' => 'anti-aging',
            'featured' => 0,
            'tags' => ['cuidados-pele', 'antioxidantes', 'colageno', 'longevidade']
        ],
        
        // Saúde Sexual
        [
            'title' => 'Saúde Sexual: Dicas para Melhorar a Libido Naturalmente',
            'slug' => 'saude-sexual-melhorar-libido-naturalmente',
            'content' => '<p>A saúde sexual é uma parte importante do bem-estar geral. Existem várias formas naturais de melhorar a libido e a qualidade de vida íntima.</p>

<h2>Fatores que Afetam a Libido</h2>
<h3>Fatores Físicos:</h3>
<ul>
<li>Desequilíbrios hormonais</li>
<li>Medicamentos</li>
<li>Doenças crónicas</li>
<li>Fadiga</li>
<li>Stress</li>
</ul>

<h3>Fatores Psicológicos:</h3>
<ul>
<li>Ansiedade</li>
<li>Depressão</li>
<li>Problemas de relacionamento</li>
<li>Baixa autoestima</li>
<li>Stress</li>
</ul>

<h2>Estratégias Naturais para Melhorar a Libido</h2>

<h3>1. Alimentação</h3>
<p>Alguns alimentos podem ajudar a aumentar a libido:</p>
<ul>
<li><strong>Chocolate negro:</strong> Liberta endorfinas</li>
<li><strong>Abacate:</strong> Rico em vitamina E</li>
<li><strong>Figos:</strong> Ricos em aminoácidos</li>
<li><strong>Melancia:</strong> Contém citrulina</li>
<li><strong>Pistácios:</strong> Melhoram a circulação</li>
</ul>

<h3>2. Exercício</h3>
<p>O exercício regular:</p>
<ul>
<li>Melhora a circulação sanguínea</li>
<li>Aumenta a energia</li>
<li>Reduz o stress</li>
<li>Melhora a autoestima</li>
<li>Equilibra as hormonas</li>
</ul>

<h3>3. Gestão do Stress</h3>
<p>Técnicas para reduzir o stress:</p>
<ul>
<li>Meditação</li>
<li>Yoga</li>
<li>Respiração profunda</li>
<li>Massagem</li>
<li>Tempo de qualidade com o parceiro</li>
</ul>

<h3>4. Sono de Qualidade</h3>
<p>O sono adequado é essencial para a produção de hormonas sexuais.</p>

<h3>5. Comunicação</h3>
<p>Falar abertamente com o parceiro sobre desejos e necessidades é fundamental.</p>

<h2>Suplementos Naturais</h2>
<ul>
<li><strong>Maca peruana:</strong> Tradicionalmente usada para aumentar a libido</li>
<li><strong>Ginseng:</strong> Pode melhorar a função sexual</li>
<li><strong>Tribulus terrestris:</strong> Pode aumentar os níveis de testosterona</li>
<li><strong>L-arginina:</strong> Melhora a circulação</li>
</ul>

<p><strong>Importante:</strong> Consulte sempre um profissional de saúde antes de tomar suplementos ou se os problemas persistirem.</p>',
            'excerpt' => 'Descubra estratégias naturais para melhorar a libido e a saúde sexual de forma holística.',
            'category_slug' => 'saude-sexual',
            'featured' => 0,
            'tags' => ['libido', 'bem-estar-intimo', 'relacionamento', 'stress']
        ],
        
        // Sono
        [
            'title' => 'Como Melhorar a Qualidade do Sono: Guia Completo',
            'slug' => 'como-melhorar-qualidade-sono-guia-completo',
            'content' => '<p>Um sono de qualidade é fundamental para a saúde física e mental. Descubra estratégias eficazes para melhorar o seu sono e combater a insónia.</p>

<h2>A Importância do Sono</h2>
<p>Durante o sono, o corpo:</p>
<ul>
<li>Repara tecidos e células</li>
<li>Consolida memórias</li>
<li>Produz hormonas importantes</li>
<li>Fortalece o sistema imunitário</li>
<li>Regula o metabolismo</li>
</ul>

<h2>Higiene do Sono</h2>
<h3>1. Horário Regular</h3>
<p>Vá para a cama e acorde sempre à mesma hora, mesmo aos fins de semana.</p>

<h3>2. Ambiente Ideal</h3>
<ul>
<li><strong>Temperatura:</strong> Entre 16-19°C</li>
<li><strong>Escuridão:</strong> Use cortinas blackout ou máscara</li>
<li><strong>Silêncio:</strong> Considere tampões ou ruído branco</li>
<li><strong>Conforto:</strong> Colchão e almofadas adequados</li>
</ul>

<h3>3. Rotina Relaxante</h3>
<p>Crie uma rotina 1-2 horas antes de dormir:</p>
<ul>
<li>Banho morno</li>
<li>Leitura</li>
<li>Meditação</li>
<li>Música suave</li>
<li>Alongamentos leves</li>
</ul>

<h2>O Que Evitar</h2>
<h3>Antes de Dormir:</h3>
<ul>
<li>Cafeína (6 horas antes)</li>
<li>Álcool (3 horas antes)</li>
<li>Refeições pesadas (3 horas antes)</li>
<li>Exercício intenso (4 horas antes)</li>
<li>Ecrãs (1 hora antes)</li>
</ul>

<h2>Alimentação e Sono</h2>
<h3>Alimentos que Promovem o Sono:</h3>
<ul>
<li><strong>Cerejas:</strong> Fonte natural de melatonina</li>
<li><strong>Banana:</strong> Rica em magnésio e potássio</li>
<li><strong>Amêndoas:</strong> Contêm magnésio</li>
<li><strong>Chá de camomila:</strong> Propriedades relaxantes</li>
<li><strong>Aveia:</strong> Estimula a produção de melatonina</li>
</ul>

<h2>Suplementos Naturais</h2>
<ul>
<li><strong>Melatonina:</strong> Regula o ciclo do sono</li>
<li><strong>Magnésio:</strong> Relaxa músculos e mente</li>
<li><strong>Valeriana:</strong> Propriedades sedativas naturais</li>
<li><strong>L-teanina:</strong> Promove relaxamento</li>
</ul>

<h2>Técnicas de Relaxamento</h2>
<h3>Respiração 4-7-8:</h3>
<ol>
<li>Inspire por 4 segundos</li>
<li>Segure por 7 segundos</li>
<li>Expire por 8 segundos</li>
<li>Repita 4 vezes</li>
</ol>

<h3>Relaxamento Muscular Progressivo:</h3>
<p>Tensione e relaxe cada grupo muscular, começando pelos pés até à cabeça.</p>

<h2>Quando Procurar Ajuda</h2>
<p>Consulte um médico se:</p>
<ul>
<li>A insónia persiste por mais de 3 semanas</li>
<li>Ressona muito alto</li>
<li>Tem pausas respiratórias durante o sono</li>
<li>Sente sonolência excessiva durante o dia</li>
</ul>

<p>Lembre-se: melhorar o sono é um processo gradual. Seja paciente e consistente com as mudanças.</p>',
            'excerpt' => 'Guia completo para melhorar a qualidade do sono com dicas práticas e estratégias eficazes.',
            'category_slug' => 'sono',
            'featured' => 1,
            'tags' => ['qualidade-sono', 'insonia', 'melatonina', 'relaxamento']
        ]
    ];
    
    // Buscar IDs das categorias criadas
    $category_ids = [];
    foreach ($health_categories as $category) {
        $stmt = $db->prepare("SELECT id FROM " . DB_PREFIX . "blog_categories WHERE slug = ?");
        $stmt->execute([$category['slug']]);
        $result = $stmt->fetch();
        if ($result) {
            $category_ids[$category['slug']] = $result['id'];
        }
    }
    
    $post_stmt = $db->prepare("
        INSERT INTO " . DB_PREFIX . "blog_posts 
        (title, slug, content, excerpt, category_id, status, featured, meta_title, meta_description, views, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, 'published', ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $posts_created = 0;
    foreach ($sample_health_posts as $post) {
        // Verificar se o post já existe
        $check_stmt = $db->prepare("SELECT id FROM " . DB_PREFIX . "blog_posts WHERE slug = ?");
        $check_stmt->execute([$post['slug']]);
        
        if ($check_stmt->fetch()) {
            echo "⚠️  Post '{$post['title']}' já existe. A saltar...\n";
            continue;
        }
        
        $category_id = $category_ids[$post['category_slug']] ?? 1;
        $views = rand(50, 300);
        
        $post_stmt->execute([
            $post['title'],
            $post['slug'],
            $post['content'],
            $post['excerpt'],
            $category_id,
            $post['featured'],
            $post['title'] . ' - MyFormula Blog',
            $post['excerpt'],
            $views
        ]);
        
        $post_id = $db->lastInsertId();
        
        // Associar tags ao post
        if (!empty($post['tags'])) {
            $tag_assoc_stmt = $db->prepare("
                INSERT IGNORE INTO " . DB_PREFIX . "blog_post_tags (post_id, tag_id)
                SELECT ?, id FROM " . DB_PREFIX . "blog_tags WHERE slug = ?
            ");
            
            foreach ($post['tags'] as $tag_slug) {
                $tag_assoc_stmt->execute([$post_id, $tag_slug]);
            }
        }
        
        echo "✅ Post criado: {$post['title']} ({$views} visualizações)\n";
        $posts_created++;
    }
    
    $db->commit();
    
    echo "\n🎉 Seeder de categorias de saúde executado com sucesso!\n\n";
    echo "📊 Resumo:\n";
    echo "   - Categorias criadas: {$created_count}\n";
    echo "   - Categorias atualizadas: {$updated_count}\n";
    echo "   - Tags de saúde criadas: {$tags_created}\n";
    echo "   - Posts de exemplo criados: {$posts_created}\n\n";
    
    echo "🏥 Categorias de Saúde Disponíveis:\n";
    foreach ($health_categories as $category) {
        echo "   ✅ {$category['name']} ({$category['slug']})\n";
    }
    
    echo "\n🌐 Visite o blog em: " . SITE_URL . "\n";
    echo "🔧 Painel admin em: " . SITE_URL . "admin/\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "❌ Erro ao executar seeder: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>