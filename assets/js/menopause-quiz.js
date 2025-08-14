// Teste de Sintomas da Menopausa
function calcularSintomasMenopausa() {
    console.log('Função calcularSintomasMenopausa chamada');
    
    const resultadoDiv = document.getElementById('menopause-resultado');
    
    if (!resultadoDiv) {
        console.error('Elemento menopause-resultado não encontrado');
        return;
    }
    
    // Obter todas as respostas - CORRIGIDO: nomes das categorias agora correspondem ao objeto pontuacao
    const respostas = {
        idade: document.querySelector('input[name="idade"]:checked'),
        ciclo_menstrual: document.querySelector('input[name="ciclo_menstrual"]:checked'),
        ondas_calor: document.querySelector('input[name="ondas_calor"]:checked'),
        suores_noturnos: document.querySelector('input[name="suores_noturnos"]:checked'),
        humor_energia: document.querySelector('input[name="humor_energia"]:checked'),
        qualidade_sono: document.querySelector('input[name="qualidade_sono"]:checked'),
        libido: document.querySelector('input[name="libido"]:checked'),
        sintomas_fisicos: document.querySelector('input[name="sintomas_fisicos"]:checked')
    };
    
    // Verificar se todas as perguntas foram respondidas
    const perguntasNaoRespondidas = [];
    for (const [pergunta, resposta] of Object.entries(respostas)) {
        if (!resposta) {
            perguntasNaoRespondidas.push(pergunta);
        }
    }
    
    if (perguntasNaoRespondidas.length > 0) {
        resultadoDiv.innerHTML = `
            <div class="result-card" style="border-left-color: #dc3545; background: #f8d7da;">
                <div class="result-title">⚠️ Questionário Incompleto</div>
                <div class="result-description">
                    Por favor, responda a todas as perguntas para obter uma avaliação completa dos seus sintomas.
                </div>
            </div>
        `;
        resultadoDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return;
    }
    
    // Sistema de pontuação
    const pontuacao = {
        idade: {
            'menos-40': 0,
            '40-45': 1,
            '45-50': 2,
            'mais-50': 3
        },
        ciclo_menstrual: {
            'regular': 0,
            'irregular': 2,
            'ausente': 3
        },
        ondas_calor: {
            'nunca': 0,
            'raramente': 1,
            'frequentemente': 2,
            'muito-frequentemente': 3
        },
        suores_noturnos: {
            'nunca': 0,
            'raramente': 1,
            'frequentemente': 2,
            'muito-frequentemente': 3
        },
        humor_energia: {
            'estavel': 0,
            'leves-alteracoes': 1,
            'alteracoes-moderadas': 2,
            'alteracoes-severas': 3
        },
        qualidade_sono: {
            'boa': 0,
            'regular': 1,
            'ruim': 2,
            'muito-ruim': 3
        },
        libido: {
            'normal': 0,
            'leve-reducao': 1,
            'reducao-moderada': 2,
            'reducao-severa': 3
        },
        sintomas_fisicos: {
            'nenhum': 0,
            'leves': 1,
            'moderados': 2,
            'severos': 3
        }
    };
    
    // Calcular pontuação total - CORRIGIDO: agora com verificação adicional
    let pontuacaoTotal = 0;
    for (const [categoria, resposta] of Object.entries(respostas)) {
        if (resposta && resposta.value) {
            const valor = resposta.value;
            if (pontuacao[categoria] && pontuacao[categoria][valor] !== undefined) {
                pontuacaoTotal += pontuacao[categoria][valor];
            } else {
                console.warn(`Categoria '${categoria}' ou valor '${valor}' não encontrado na pontuação`);
            }
        }
    }
    
    console.log('Pontuação total:', pontuacaoTotal);
    
    // Determinar resultado baseado na pontuação
    let resultado, recomendacoes;
    
    if (pontuacaoTotal <= 8) {
        resultado = {
            titulo: 'Sintomas Mínimos ou Ausentes',
            descricao: 'Com base nas suas respostas, você apresenta poucos ou nenhum sintoma relacionado à menopausa. Isso pode indicar que você ainda não entrou na perimenopausa ou que está numa fase muito inicial.',
            cor: '#28a745',
            fundo: '#d4edda'
        };
        recomendacoes = [
            'Continue mantendo hábitos de vida saudáveis',
            'Pratique exercícios físicos regulares',
            'Mantenha uma alimentação equilibrada rica em cálcio',
            'Faça check-ups ginecológicos regulares',
            'Monitore mudanças no seu ciclo menstrual'
        ];
    } else if (pontuacaoTotal <= 16) {
        resultado = {
            titulo: 'Sintomas Leves a Moderados',
            descricao: 'Você está apresentando alguns sintomas que podem estar relacionados à perimenopausa. É uma fase de transição natural que pode durar alguns anos.',
            cor: '#ffc107',
            fundo: '#fff3cd'
        };
        recomendacoes = [
            'Considere consultar um ginecologista para avaliação',
            'Mantenha um diário dos sintomas para acompanhar padrões',
            'Pratique técnicas de relaxamento e gestão do stress',
            'Considere suplementação de cálcio e vitamina D',
            'Evite gatilhos como cafeína e álcool em excesso',
            'Mantenha um ambiente fresco para dormir'
        ];
    } else {
        resultado = {
            titulo: 'Sintomas Moderados a Severos',
            descricao: 'Você está apresentando vários sintomas significativos que podem estar impactando sua qualidade de vida. É importante buscar acompanhamento médico especializado.',
            cor: '#dc3545',
            fundo: '#f8d7da'
        };
        recomendacoes = [
            'Procure um ginecologista especializado em menopausa',
            'Discuta opções de tratamento, incluindo terapia hormonal',
            'Considere acompanhamento psicológico se necessário',
            'Implemente mudanças no estilo de vida imediatamente',
            'Monitore a saúde óssea e cardiovascular',
            'Junte-se a grupos de apoio para mulheres na menopausa'
        ];
    }
    
    // Exibir resultado
    resultadoDiv.innerHTML = `
        <div class="result-card" style="border-left-color: ${resultado.cor}; background: ${resultado.fundo};">
            <div class="result-title">🌸 ${resultado.titulo}</div>
            <div class="result-score">Pontuação: ${pontuacaoTotal}/24 pontos</div>
            <div class="result-description">${resultado.descricao}</div>
            
            <div class="result-recommendations">
                <h5>💡 Recomendações:</h5>
                <ul>
                    ${recomendacoes.map(rec => `<li>${rec}</li>`).join('')}
                </ul>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: rgba(167, 37, 140, 0.1); border-radius: 6px; font-size: 0.9rem; color: #666;">
                <strong>⚠️ Importante:</strong> Este questionário é apenas uma ferramenta de autoavaliação e não substitui a consulta médica. Os resultados devem ser discutidos com um profissional de saúde qualificado.
            </div>
        </div>
    `;
    
    // Scroll suave para o resultado
    resultadoDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function limparQuestionarioMenopausa() {
    console.log('Limpando questionário de menopausa');
    
    // Limpar todas as seleções
    const radios = document.querySelectorAll('input[name="idade"], input[name="ciclo_menstrual"], input[name="ondas_calor"], input[name="suores_noturnos"], input[name="humor_energia"], input[name="qualidade_sono"], input[name="libido"], input[name="sintomas_fisicos"]');
    radios.forEach(radio => {
        radio.checked = false;
    });
    
    // Limpar resultado
    const resultadoDiv = document.getElementById('menopause-resultado');
    if (resultadoDiv) {
        resultadoDiv.innerHTML = '';
    }
    
    console.log('Questionário de menopausa limpo');
}

// Tornar as funções globais para que possam ser chamadas pelos botões onclick
window.calcularSintomasMenopausa = calcularSintomasMenopausa;
window.limparQuestionarioMenopausa = limparQuestionarioMenopausa;

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('Questionário de menopausa carregado');
    
    // Verificar se os elementos existem
    const container = document.querySelector('.menopause-quiz-container');
    if (container) {
        console.log('Container do teste de menopausa encontrado');
        
        // Anexar event listeners aos botões como alternativa ao onclick
        const btnAvaliar = container.querySelector('.quiz-btn-primary');
        const btnLimpar = container.querySelector('.quiz-btn-secondary');
        
        if (btnAvaliar) {
            btnAvaliar.addEventListener('click', function(e) {
                e.preventDefault();
                calcularSintomasMenopausa();
            });
        }
        
        if (btnLimpar) {
            btnLimpar.addEventListener('click', function(e) {
                e.preventDefault();
                limparQuestionarioMenopausa();
            });
        }
    }
});


// Função para verificar se o localStorage está disponível
function isLocalStorageAvailable() {
    try {
        const test = 'test';
        localStorage.setItem(test, test);
        localStorage.removeItem(test);
        return true;
    } catch (e) {
        console.warn('localStorage não está disponível:', e.message);
        return false;
    }
}

// Função para salvar dados de forma segura
function salvarDadosSeguro(chave, valor) {
    if (isLocalStorageAvailable()) {
        try {
            localStorage.setItem(chave, JSON.stringify(valor));
        } catch (e) {
            console.warn('Erro ao salvar no localStorage:', e.message);
        }
    }
}

// Função para recuperar dados de forma segura
function recuperarDadosSeguro(chave) {
    if (isLocalStorageAvailable()) {
        try {
            const dados = localStorage.getItem(chave);
            return dados ? JSON.parse(dados) : null;
        } catch (e) {
            console.warn('Erro ao recuperar do localStorage:', e.message);
            return null;
        }
    }
    return null;
}