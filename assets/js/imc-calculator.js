// Calculadora de IMC - Versão Funcional
function calcularIMC() {
    console.log('Função calcularIMC chamada');
    
    // Obter valores dos inputs
    const peso = parseFloat(document.getElementById('peso').value);
    const altura = parseFloat(document.getElementById('altura').value);
    const resultadoDiv = document.getElementById('resultado');
    
    console.log('Peso:', peso, 'Altura:', altura);
    
    // Validação básica
    if (!peso || !altura || peso <= 0 || altura <= 0) {
        resultadoDiv.innerHTML = `
            <div style="background: #f8d7da; border-radius: 8px; padding: 20px; margin-top: 20px; border-left: 4px solid #dc3545;">
                <h4 style="margin: 0 0 10px 0; color: #333;">⚠️ Erro</h4>
                <p style="margin: 0;">Por favor, insira valores válidos para peso e altura.</p>
            </div>
        `;
        return;
    }
    
    // Converter altura para metros
    const alturaMetros = altura / 100;
    
    // Calcular IMC
    const imc = peso / (alturaMetros * alturaMetros);
    
    console.log('IMC calculado:', imc);
    
    // Determinar classificação
    let classificacao, corBorda, corFundo, recomendacao;
    
    if (imc < 18.5) {
        classificacao = 'Abaixo do peso';
        corBorda = '#17a2b8';
        corFundo = '#d1ecf1';
        recomendacao = 'Considere consultar um nutricionista para ganhar peso de forma saudável.';
    } else if (imc >= 18.5 && imc < 25) {
        classificacao = 'Peso normal';
        corBorda = '#28a745';
        corFundo = '#d4edda';
        recomendacao = 'Parabéns! Seu peso está dentro da faixa ideal. Mantenha hábitos saudáveis.';
    } else if (imc >= 25 && imc < 30) {
        classificacao = 'Sobrepeso';
        corBorda = '#ffc107';
        corFundo = '#fff3cd';
        recomendacao = 'Considere adotar uma dieta equilibrada e exercícios regulares.';
    } else if (imc >= 30 && imc < 35) {
        classificacao = 'Obesidade Grau I';
        corBorda = '#fd7e14';
        corFundo = '#ffeaa7';
        recomendacao = 'É recomendável procurar orientação médica e nutricional.';
    } else if (imc >= 35 && imc < 40) {
        classificacao = 'Obesidade Grau II';
        corBorda = '#dc3545';
        corFundo = '#f8d7da';
        recomendacao = 'Procure acompanhamento médico especializado urgentemente.';
    } else {
        classificacao = 'Obesidade Grau III (Mórbida)';
        corBorda = '#dc3545';
        corFundo = '#f8d7da';
        recomendacao = 'É essencial buscar tratamento médico imediato.';
    }
    
    // Exibir resultado
    resultadoDiv.innerHTML = `
        <div style="background: ${corFundo}; border-radius: 8px; padding: 20px; margin-top: 20px; border-left: 4px solid ${corBorda};">
            <h4 style="margin: 0 0 10px 0; color: #333;">📊 Resultado do seu IMC</h4>
            <div style="font-size: 28px; font-weight: bold; color: ${corBorda}; margin: 10px 0;">${imc.toFixed(1)}</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 15px;">${classificacao}</div>
            <div style="font-size: 14px; line-height: 1.5; color: #666;">${recomendacao}</div>
        </div>
    `;
    
    console.log('Resultado exibido');
}

// Função para limpar a calculadora
function limparCalculadora() {
    console.log('Limpando calculadora');
    document.getElementById('peso').value = '';
    document.getElementById('altura').value = '';
    document.getElementById('resultado').innerHTML = '';
}

// Verificar se a página carregou
console.log('Script da calculadora carregado!');

// Permitir cálculo com Enter
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('#peso, #altura');
    inputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                calcularIMC();
            }
        });
    });
});