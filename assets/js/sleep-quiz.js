// Questionário de Qualidade do Sono
function calcularQualidadeSono() {
    console.log('Função calcularQualidadeSono chamada');
    
    const resultadoDiv = document.getElementById('sleep-resultado');
    
    // Obter respostas de todas as perguntas
    const respostas = {
        horas: document.querySelector('input[name="horas_sono"]:checked'),
        adormecer: document.querySelector('input[name="tempo_adormecer"]:checked'),
        acordar: document.querySelector('input[name="acordar_noite"]:checked'),
        descansado: document.querySelector('input[name="sentir_descansado"]:checked'),
        habitos: document.querySelector('input[name="habitos_sono"]:checked')
    };
    
    console.log('Respostas:', respostas);
    
    // Verificar se todas as perguntas foram respondidas
    const perguntasNaoRespondidas = Object.keys(respostas).filter(key => !respostas[key]);
    
    if (perguntasNaoRespondidas.length > 0) {
        resultadoDiv.innerHTML = `
            <div class="result-card result-poor">
                <h4 class="result-title">⚠️ Questionário Incompleto</h4>
                <p class="result-description">Por favor, responda todas as perguntas para obter seu resultado.</p>
            </div>
        `;
        return;
    }
    
    // Sistema de pontuação
    let pontuacao = 0;
    
    // Pergunta 1: Horas de sono
    const horasValor = respostas.horas.value;
    if (horasValor === '7-8') pontuacao += 3;
    else if (horasValor === '5-6' || horasValor === 'mais-8') pontuacao += 2;
    else pontuacao += 1; // menos-5
    
    // Pergunta 2: Tempo para adormecer
    const adormecerValor = respostas.adormecer.value;
    if (adormecerValor === 'quase-nunca') pontuacao += 3;
    else if (adormecerValor === '1-2-vezes') pontuacao += 2;
    else pontuacao += 1; // 3-mais-vezes
    
    // Pergunta 3: Acordar durante a noite
    const acordarValor = respostas.acordar.value;
    if (acordarValor === 'raramente') pontuacao += 3;
    else if (acordarValor === 'algumas-vezes') pontuacao += 2;
    else pontuacao += 1; // frequentemente
    
    // Pergunta 4: Sentir-se descansado
    const descansadoValor = respostas.descansado.value;
    if (descansadoValor === 'sempre') pontuacao += 3;
    else if (descansadoValor === 'algumas-vezes') pontuacao += 2;
    else pontuacao += 1; // raramente
    
    // Pergunta 5: Hábitos antes de dormir
    const habitosValor = respostas.habitos.value;
    if (habitosValor === 'nao') pontuacao += 3;
    else if (habitosValor === 'algumas-vezes') pontuacao += 2;
    else pontuacao += 1; // quase-sempre
    
    console.log('Pontuação total:', pontuacao);
    
    // Determinar resultado baseado na pontuação
    let categoria, classeCSS, emoji, titulo, descricao;
    
    if (pontuacao >= 13) {
        categoria = 'Excelente Qualidade do Sono';
        classeCSS = 'result-excellent';
        emoji = '😴';
        titulo = 'Parabéns! Excelente qualidade do sono!';
        descricao = 'Seus hábitos de sono estão no caminho certo! Continue mantendo essa rotina saudável. Pequenos ajustes podem sempre otimizar ainda mais seu descanso.';
    } else if (pontuacao >= 10) {
        categoria = 'Boa Qualidade do Sono';
        classeCSS = 'result-good';
        emoji = '😊';
        titulo = 'Boa qualidade do sono com margem para melhorias';
        descricao = 'Você está no caminho certo! Algumas pequenas mudanças na sua rotina podem melhorar significativamente a qualidade do seu descanso. Considere ajustar seus hábitos antes de dormir.';
    } else {
        categoria = 'Qualidade do Sono Comprometida';
        classeCSS = 'result-poor';
        emoji = '😴';
        titulo = 'Atenção: sua qualidade do sono precisa de cuidados';
        descricao = 'Está na hora de agir! A má qualidade do sono pode afetar seriamente sua saúde e bem-estar. Considere consultar um especialista e implementar mudanças na sua rotina de sono.';
    }
    
    // Exibir resultado
    resultadoDiv.innerHTML = `
        <div class="result-card ${classeCSS}">
            <h4 class="result-title">${emoji} ${titulo}</h4>
            <div class="result-score">${pontuacao}/15 pontos</div>
            <div class="result-category">${categoria}</div>
            <div class="result-description">${descricao}</div>
        </div>
    `;
    
    console.log('Resultado exibido');
}

// Função para limpar o questionário
function limparQuestionario() {
    console.log('Limpando questionário');
    
    // Limpar todas as seleções
    const radios = document.querySelectorAll('input[type="radio"][name^="horas_sono"], input[type="radio"][name^="tempo_adormecer"], input[type="radio"][name^="acordar_noite"], input[type="radio"][name^="sentir_descansado"], input[type="radio"][name^="habitos_sono"]');
    radios.forEach(radio => {
        radio.checked = false;
        radio.closest('.option-item').classList.remove('selected');
    });
    
    // Limpar resultado
    document.getElementById('sleep-resultado').innerHTML = '';
}

// Adicionar interatividade visual aos radio buttons
function adicionarInteratividadeQuestionario() {
    const radioButtons = document.querySelectorAll('.sleep-quiz-container input[type="radio"]');
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remover seleção visual de todas as opções do mesmo grupo
            const grupoName = this.name;
            const opcoesMesmoGrupo = document.querySelectorAll(`input[name="${grupoName}"]`);
            
            opcoesMesmoGrupo.forEach(opcao => {
                opcao.closest('.option-item').classList.remove('selected');
            });
            
            // Adicionar seleção visual à opção escolhida
            this.closest('.option-item').classList.add('selected');
        });
    });
}

// Verificar se a página carregou
console.log('Script do questionário de sono carregado!');

// Inicializar interatividade quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar um pouco para garantir que o HTML foi inserido
    setTimeout(() => {
        adicionarInteratividadeQuestionario();
    }, 100);
});