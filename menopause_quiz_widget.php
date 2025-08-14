<?php
$page_title = 'Teste de Sintomas da Menopausa';
$page_description = 'Descubra se você está entrando na menopausa com nosso teste completo de sintomas.';
include 'includes/header.php';
?>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/menopause-quiz.css">

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="menopause-quiz-container">
                <div class="menopause-quiz-header">
                    <h3>🌸 Teste de Sintomas da Menopausa</h3>
                    <p>Descubra se você está entrando na menopausa respondendo às perguntas abaixo</p>
                </div>
                
                <div class="menopause-quiz-form">
                    <form id="menopause-form">
                        <!-- Pergunta 1: Idade -->
                        <div class="question-group">
                            <label class="question-title">1. Qual sua faixa etária?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="idade" value="35-40" id="idade1">
                                    <label for="idade1">35-40 anos</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="idade" value="41-45" id="idade2">
                                    <label for="idade2">41-45 anos</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="idade" value="46-50" id="idade3">
                                    <label for="idade3">46-50 anos</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="idade" value="51-55" id="idade4">
                                    <label for="idade4">51-55 anos</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="idade" value="acima-55" id="idade5">
                                    <label for="idade5">Acima de 55 anos</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 2: Ciclo Menstrual -->
                        <div class="question-group">
                            <label class="question-title">2. Como está seu ciclo menstrual?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="ciclo_menstrual" value="regular" id="ciclo1">
                                    <label for="ciclo1">Regular (como sempre)</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ciclo_menstrual" value="irregular" id="ciclo2">
                                    <label for="ciclo2">Irregular (mais curto/longo que o normal)</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ciclo_menstrual" value="muito-irregular" id="ciclo3">
                                    <label for="ciclo3">Muito irregular (pula meses)</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ciclo_menstrual" value="parou-menos-12" id="ciclo4">
                                    <label for="ciclo4">Parou há menos de 12 meses</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ciclo_menstrual" value="parou-mais-12" id="ciclo5">
                                    <label for="ciclo5">Parou há mais de 12 meses</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 3: Ondas de Calor -->
                        <div class="question-group">
                            <label class="question-title">3. Com que frequência sente ondas de calor?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="ondas_calor" value="nunca" id="ondas1">
                                    <label for="ondas1">Nunca</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ondas_calor" value="raramente" id="ondas2">
                                    <label for="ondas2">Raramente (1-2 vezes por mês)</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ondas_calor" value="algumas-vezes" id="ondas3">
                                    <label for="ondas3">Algumas vezes (1-2 vezes por semana)</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="ondas_calor" value="frequentemente" id="ondas4">
                                    <label for="ondas4">Frequentemente (quase todos os dias)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 4: Suores Noturnos -->
                        <div class="question-group">
                            <label class="question-title">4. Acorda com suores noturnos?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="suores_noturnos" value="nunca" id="suores1">
                                    <label for="suores1">Nunca</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="suores_noturnos" value="raramente" id="suores2">
                                    <label for="suores2">Raramente</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="suores_noturnos" value="algumas-vezes" id="suores3">
                                    <label for="suores3">Algumas vezes por semana</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="suores_noturnos" value="quase-todas" id="suores4">
                                    <label for="suores4">Quase todas as noites</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 5: Humor -->
                        <div class="question-group">
                            <label class="question-title">5. Como está seu humor ultimamente?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="humor" value="estavel" id="humor1">
                                    <label for="humor1">Estável, como sempre</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="humor" value="ligeiramente-irritavel" id="humor2">
                                    <label for="humor2">Ligeiramente mais irritável</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="humor" value="mudancas-frequentes" id="humor3">
                                    <label for="humor3">Mudanças de humor frequentes</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="humor" value="muito-irritavel" id="humor4">
                                    <label for="humor4">Muito irritável ou deprimida</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 6: Sono -->
                        <div class="question-group">
                            <label class="question-title">6. Como está a qualidade do seu sono?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="qualidade_sono" value="durmo-bem" id="sono1">
                                    <label for="sono1">Durmo bem</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="qualidade_sono" value="acordo-algumas-vezes" id="sono2">
                                    <label for="sono2">Acordo algumas vezes</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="qualidade_sono" value="dificuldade-adormecer" id="sono3">
                                    <label for="sono3">Tenho dificuldade para adormecer</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="qualidade_sono" value="insonia-frequente" id="sono4">
                                    <label for="sono4">Insônia frequente</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 7: Libido -->
                        <div class="question-group">
                            <label class="question-title">7. Notou mudanças na libido?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="libido" value="sem-mudancas" id="libido1">
                                    <label for="libido1">Sem mudanças</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="libido" value="ligeira-diminuicao" id="libido2">
                                    <label for="libido2">Ligeira diminuição</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="libido" value="diminuicao-significativa" id="libido3">
                                    <label for="libido3">Diminuição significativa</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="libido" value="perda-quase-total" id="libido4">
                                    <label for="libido4">Perda quase total</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 8: Sintomas Físicos -->
                        <div class="question-group">
                            <label class="question-title">8. Quais sintomas físicos tem sentido?</label>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="sintomas_fisicos" value="nenhum" id="sintomas1">
                                    <label for="sintomas1">Nenhum</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="sintomas_fisicos" value="dores-cabeca" id="sintomas2">
                                    <label for="sintomas2">Dores de cabeça ocasionais</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="sintomas_fisicos" value="secura-vaginal" id="sintomas3">
                                    <label for="sintomas3">Secura vaginal</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="sintomas_fisicos" value="ganho-peso" id="sintomas4">
                                    <label for="sintomas4">Ganho de peso/inchaço</label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="sintomas_fisicos" value="multiplos-sintomas" id="sintomas5">
                                    <label for="sintomas5">Múltiplos sintomas</label>
                                </div>
                            </div>
                        </div>

                        <div class="quiz-buttons">
                            <button type="button" class="btn-calculate" onclick="calcularSintomasMenopausa()">🌸 Avaliar Sintomas</button>
                            <button type="button" class="btn-clear" onclick="limparQuestionarioMenopausa()">🔄 Limpar</button>
                        </div>
                    </form>
                    
                    <div id="menopause-resultado"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>assets/js/menopause-quiz.js"></script>

<?php include 'includes/footer.php'; ?>