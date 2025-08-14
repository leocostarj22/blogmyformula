<?php
require_once 'includes/Blog.php';

$blog = new Blog();

// Função para processar shortcodes
function processShortcodes($content) {
    // Shortcode da calculadora de IMC
    $calculadora_html = '
    <div class="calculator-container" style="max-width: 500px; margin: 20px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); overflow: hidden; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="padding: 30px;">
            <div style="margin-bottom: 20px;">
                <label for="peso" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Peso (kg):</label>
                <input type="number" id="peso" placeholder="Ex: 70" step="0.1" min="1" max="500" style="width: 100%; padding: 12px 16px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="altura" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px;">Altura (cm):</label>
                <input type="number" id="altura" placeholder="Ex: 175" step="1" min="50" max="250" style="width: 100%; padding: 12px 16px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; box-sizing: border-box;">
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 25px;">
                <button onclick="calcularIMC()" style="flex: 1; padding: 14px 20px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; background: linear-gradient(135deg, #28a745, #20c997); color: white;">Calcular IMC</button>
                <button onclick="limparCalculadora()" style="flex: 1; padding: 14px 20px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; background: #6c757d; color: white;">Limpar</button>
            </div>
        </div>
        
        <div id="resultado" style="padding: 0 30px 30px;"></div>
    </div>';
    
    // Shortcode do questionário de qualidade do sono
    $questionario_sono_html = '
    <div class="sleep-quiz-container">
        <div class="sleep-quiz-header">
            <h3>Questionário de Qualidade do Sono</h3>
            <p>Responda com sinceridade — não há respostas certas ou erradas, apenas informações para o ajudar</p>
        </div>
        
        <div class="sleep-quiz-form">
            <div class="question-group">
                <div class="question-title">1. Em média, quantas horas dorme por noite?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="horas1" name="horas_sono" value="menos-5">
                        <label for="horas1">Menos de 5 horas</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="horas2" name="horas_sono" value="5-6">
                        <label for="horas2">5 a 6 horas</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="horas3" name="horas_sono" value="7-8">
                        <label for="horas3">7 a 8 horas</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="horas4" name="horas_sono" value="mais-8">
                        <label for="horas4">Mais de 8 horas</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">2. Com que frequência demora mais de 30 minutos a adormecer?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="adormecer1" name="tempo_adormecer" value="quase-nunca">
                        <label for="adormecer1">Quase nunca</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="adormecer2" name="tempo_adormecer" value="1-2-vezes">
                        <label for="adormecer2">1 a 2 vezes por semana</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="adormecer3" name="tempo_adormecer" value="3-mais-vezes">
                        <label for="adormecer3">3 ou mais vezes por semana</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">3. Acorda durante a noite e tem dificuldade em voltar a adormecer?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="acordar1" name="acordar_noite" value="raramente">
                        <label for="acordar1">Raramente</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="acordar2" name="acordar_noite" value="algumas-vezes">
                        <label for="acordar2">Algumas vezes</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="acordar3" name="acordar_noite" value="frequentemente">
                        <label for="acordar3">Frequentemente</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">4. Quando acorda, sente-se descansado(a)?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="descansado1" name="sentir_descansado" value="sempre">
                        <label for="descansado1">Sempre</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="descansado2" name="sentir_descansado" value="algumas-vezes">
                        <label for="descansado2">Algumas vezes</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="descansado3" name="sentir_descansado" value="raramente">
                        <label for="descansado3">Raramente</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">5. Consome cafeína, álcool ou utiliza ecrãs (telemóvel, televisão, computador) até 1 hora antes de dormir?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="habitos1" name="habitos_sono" value="nao">
                        <label for="habitos1">Não</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="habitos2" name="habitos_sono" value="algumas-vezes">
                        <label for="habitos2">Algumas vezes</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="habitos3" name="habitos_sono" value="quase-sempre">
                        <label for="habitos3">Quase sempre</label>
                    </div>
                </div>
            </div>
            
            <div class="quiz-buttons">
                <button onclick="calcularQualidadeSono()" class="quiz-btn quiz-btn-primary">Avaliar Qualidade do Sono</button>
                <button onclick="limparQuestionario()" class="quiz-btn quiz-btn-secondary">Limpar</button>
            </div>
        </div>
        
        <div id="sleep-resultado" class="quiz-result"></div>
    </div>';
    
    // Shortcode do teste de menopausa
    $teste_menopausa_html = '
    <div class="menopause-quiz-container">
        <div class="menopause-quiz-header">
            <h3>Teste de Sintomas da Menopausa</h3>
            <p>Avalie seus sintomas e descubra em que fase da menopausa você pode estar</p>
        </div>
        
        <div class="menopause-quiz-form">
            <div class="question-group">
                <div class="question-title">1. Qual é a sua idade?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="idade1" name="idade" value="menos-40">
                        <label for="idade1">Menos de 40 anos</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="idade2" name="idade" value="40-45">
                        <label for="idade2">40 a 45 anos</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="idade3" name="idade" value="45-50">
                        <label for="idade3">45 a 50 anos</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="idade4" name="idade" value="mais-50">
                        <label for="idade4">Mais de 50 anos</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">2. Como está o seu ciclo menstrual?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="ciclo1" name="ciclo_menstrual" value="regular">
                        <label for="ciclo1">Regular (como sempre foi)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="ciclo2" name="ciclo_menstrual" value="irregular">
                        <label for="ciclo2">Irregular (mudanças na frequência ou fluxo)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="ciclo3" name="ciclo_menstrual" value="ausente">
                        <label for="ciclo3">Ausente há mais de 12 meses</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">3. Com que frequência tem ondas de calor?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="ondas1" name="ondas_calor" value="nunca">
                        <label for="ondas1">Nunca</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="ondas2" name="ondas_calor" value="raramente">
                        <label for="ondas2">Raramente (1-2 vezes por mês)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="ondas3" name="ondas_calor" value="frequentemente">
                        <label for="ondas3">Frequentemente (várias vezes por semana)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="ondas4" name="ondas_calor" value="muito-frequentemente">
                        <label for="ondas4">Muito frequentemente (diariamente)</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">4. Tem suores noturnos?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="suores1" name="suores_noturnos" value="nunca">
                        <label for="suores1">Nunca</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="suores2" name="suores_noturnos" value="raramente">
                        <label for="suores2">Raramente</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="suores3" name="suores_noturnos" value="frequentemente">
                        <label for="suores3">Frequentemente</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="suores4" name="suores_noturnos" value="muito-frequentemente">
                        <label for="suores4">Muito frequentemente</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">5. Como está o seu humor e energia?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="humor1" name="humor_energia" value="estavel">
                        <label for="humor1">Estável, como sempre</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="humor2" name="humor_energia" value="leves-alteracoes">
                        <label for="humor2">Leves alterações ocasionais</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="humor3" name="humor_energia" value="alteracoes-moderadas">
                        <label for="humor3">Alterações moderadas frequentes</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="humor4" name="humor_energia" value="alteracoes-severas">
                        <label for="humor4">Alterações severas que afetam o dia a dia</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">6. Como está a qualidade do seu sono?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="sono1" name="qualidade_sono" value="boa">
                        <label for="sono1">Boa (durmo bem e acordo descansada)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="sono2" name="qualidade_sono" value="regular">
                        <label for="sono2">Regular (algumas dificuldades ocasionais)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="sono3" name="qualidade_sono" value="ruim">
                        <label for="sono3">Ruim (frequentes interrupções ou insónia)</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="sono4" name="qualidade_sono" value="muito-ruim">
                        <label for="sono4">Muito ruim (raramente durmo bem)</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">7. Como está a sua libido?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="libido1" name="libido" value="normal">
                        <label for="libido1">Normal, sem mudanças</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="libido2" name="libido" value="leve-reducao">
                        <label for="libido2">Leve redução</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="libido3" name="libido" value="reducao-moderada">
                        <label for="libido3">Redução moderada</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="libido4" name="libido" value="reducao-severa">
                        <label for="libido4">Redução severa ou ausência</label>
                    </div>
                </div>
            </div>
            
            <div class="question-group">
                <div class="question-title">8. Tem outros sintomas físicos (secura vaginal, dores articulares, ganho de peso)?</div>
                <div class="option-group">
                    <div class="option-item">
                        <input type="radio" id="fisicos1" name="sintomas_fisicos" value="nenhum">
                        <label for="fisicos1">Nenhum sintoma</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="fisicos2" name="sintomas_fisicos" value="leves">
                        <label for="fisicos2">Sintomas leves ocasionais</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="fisicos3" name="sintomas_fisicos" value="moderados">
                        <label for="fisicos3">Sintomas moderados frequentes</label>
                    </div>
                    <div class="option-item">
                        <input type="radio" id="fisicos4" name="sintomas_fisicos" value="severos">
                        <label for="fisicos4">Sintomas severos que afetam a qualidade de vida</label>
                    </div>
                </div>
            </div>
            
            <div class="quiz-buttons">
                <button onclick="calcularSintomasMenopausa()" class="quiz-btn quiz-btn-primary">Avaliar Sintomas</button>
                <button onclick="limparQuestionarioMenopausa()" class="quiz-btn quiz-btn-secondary">Limpar</button>
            </div>
        </div>
        
        <div id="menopause-resultado" class="quiz-result"></div>
    </div>';
    
    // Substituir os shortcodes pelo HTML
    $content = str_replace('[calculadora-imc]', $calculadora_html, $content);
    $content = str_replace('[questionario-sono]', $questionario_sono_html, $content);
    $content = str_replace('[teste-menopausa]', $teste_menopausa_html, $content);
    
    return $content;
}

// Verificar se o slug foi fornecido
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: ' . SITE_URL);
    exit;
}

$slug = sanitizeInput($_GET['slug']);
$post = $blog->getPostBySlug($slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    $page_title = 'Post não encontrado';
    include 'includes/header.php';
    echo '<div class="alert alert-danger"><h4>Post não encontrado</h4><p>O post que você está procurando não existe ou foi removido.</p></div>';
    include 'includes/footer.php';
    exit;
}

// Buscar posts relacionados - CORRIGIDO: usar 'id' em vez de 'post_id'
$related_posts = $blog->getRelatedPosts($post['id'], $post['category_id'], 3);

// Meta tags para SEO - CORRIGIDO: verificar se a chave existe
$page_title = $post['meta_title'] ?: $post['title'];
$page_description = $post['meta_description'] ?: truncateText(strip_tags($post['content']), 160);
$page_keywords = isset($post['meta_keywords']) ? $post['meta_keywords'] : '';

// Definir classe para identificar páginas de posts
$body_class = 'post-page';

include 'includes/header.php';
?>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <article class="card">
            <?php if ($post['image']): ?>
            <img src="<?php echo SITE_URL . UPLOAD_DIR . $post['image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>" style="max-height: 400px; object-fit: cover;">
            <?php endif; ?>
            
            <div class="card-body">
                <!-- Post Header -->
                <header class="mb-4">
                    <h1 class="card-title mb-3 fw-bold">
                        <a href="#" class="text-decoration-none text-dark">
                            <?php echo $post['title']; ?>
                        </a>
                    </h1>
                    
                    <p class="card-text text-muted mb-3 fs-6">
                        <i class="fas fa-calendar me-2"></i><?php echo formatDate($post['created_at']); ?>
                        <span class="ms-4">
                            <i class="fas fa-folder me-2"></i>
                            <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $post['category_slug']; ?>" class="text-decoration-none text-muted">
                                <?php echo $post['category_name']; ?>
                            </a>
                        </span>
                        <span class="ms-4"><i class="fas fa-eye me-2"></i><?php echo $post['views']; ?> visualizações</span>
                        <?php if (isset($post['author_name']) && $post['author_name']): ?>
                        <span class="ms-4"><i class="fas fa-user me-2"></i><?php echo $post['author_name']; ?></span>
                        <?php endif; ?>
                    </p>
                    
                    <?php if (!empty($post['tags'])): ?>
                    <div class="mb-3">
                        <?php foreach ($post['tags'] as $tag): ?>
                        <a href="<?php echo SITE_URL; ?>search.php?tag=<?php echo $tag['slug']; ?>" class="badge bg-primary text-decoration-none me-1">
                            <i class="fas fa-tag me-1"></i><?php echo $tag['name']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </header>
                
                <!-- Post Content -->
                <div class="post-content">
                    <?php echo processShortcodes($post['content']); ?>
                </div>
                
                <!-- Social Share -->
                <div class="mt-4 pt-3 border-top">
                    <h6>Compartilhar:</h6>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . 'post.php?slug=' . $post['slug']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . 'post.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . SITE_URL . 'post.php?slug=' . $post['slug']); ?>" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </article>
        
        <!-- Related Posts -->
        <?php if (!empty($related_posts)): ?>
        <section class="mt-5">
            <h3 class="mb-4">Posts Relacionados</h3>
            <div class="row">
                <?php foreach ($related_posts as $related): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <?php if ($related['image']): ?>
                        <img src="<?php echo SITE_URL . UPLOAD_DIR . $related['image']; ?>" class="card-img-top" alt="<?php echo $related['title']; ?>" style="height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">
                                <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $related['slug']; ?>" class="text-decoration-none">
                                    <?php echo truncateText($related['title'], 50); ?>
                                </a>
                            </h6>
                            <p class="card-text small text-muted">
                                <i class="fas fa-calendar me-1"></i><?php echo formatDate($related['created_at'], 'd/m/Y'); ?>
                            </p>
                            <p class="card-text flex-grow-1"><?php echo truncateText(strip_tags($related['content']), 80); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted">
                                    <?php if (isset($post['author']) && !empty($post['author'])): ?>
                                        Por <?= htmlspecialchars($post['author']) ?> • 
                                    <?php endif; ?>
                                    <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                </small>
                                <a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-primary btn-sm">Ler mais</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Comments Section -->
        <section class="mt-5">
            <?php
            // Processar envio de comentário
            $comment_message = '';
            $comment_error = '';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
                $author_name = sanitizeInput($_POST['author_name'] ?? '');
                $author_email = sanitizeInput($_POST['author_email'] ?? '');
                $comment_content = sanitizeInput($_POST['comment_content'] ?? '');
                
                if ($blog->addComment($post['id'], $author_name, $author_email, $comment_content)) {
                    $comment_message = 'Comentário enviado com sucesso! Aguarde a aprovação.';
                } else {
                    $comment_error = 'Erro ao enviar comentário. Verifique os dados e tente novamente.';
                }
            }
            
            // Buscar comentários aprovados
            $comments = $blog->getPostComments($post['id']);
            $comment_count = $blog->countPostComments($post['id']);
            ?>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Comentários (<?php echo $comment_count; ?>)
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Mensagens de feedback -->
                    <?php if ($comment_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $comment_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($comment_error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $comment_error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Lista de comentários -->
                    <?php if (!empty($comments)): ?>
                    <div class="comments-list mb-4">
                        <?php foreach ($comments as $comment): ?>
                        <div class="comment mb-3 p-3 border rounded">
                            <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="comment-author"><?php echo htmlspecialchars($comment['author_name']); ?></strong>
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo formatDate($comment['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                            <div class="comment-content">
                                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-4">Seja o primeiro a comentar!</p>
                    <?php endif; ?>
                    
                    <!-- Formulário de comentário -->
                    <div class="comment-form">
                        <h5 class="mb-3">Deixe seu comentário</h5>
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="author_name" class="form-label">Nome *</label>
                                    <input type="text" class="form-control" id="author_name" name="author_name" required maxlength="100" value="<?php echo isset($_POST['author_name']) ? htmlspecialchars($_POST['author_name']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="author_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="author_email" name="author_email" required maxlength="100" value="<?php echo isset($_POST['author_email']) ? htmlspecialchars($_POST['author_email']) : ''; ?>">
                                    <div class="form-text">Seu email não será publicado.</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment_content" class="form-label">Comentário *</label>
                                <textarea class="form-control" id="comment_content" name="comment_content" rows="4" required maxlength="1000" placeholder="Escreva seu comentário..."><?php echo isset($_POST['comment_content']) ? htmlspecialchars($_POST['comment_content']) : ''; ?></textarea>
                                <div class="form-text">Máximo 1000 caracteres.</div>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>
                                Enviar Comentário
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>