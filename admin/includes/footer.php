<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Quill.js CSS e JS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<!-- CSS único e otimizado -->
<style>
/* Reset e base */
* {
    box-sizing: border-box;
}

/* Container principal do editor */
.quill-editor-wrapper {
    background: #f5f5f5;
    padding: 20px;
    min-height: 100vh;
}

.quill-editor-container {
    max-width: 210mm; /* Tamanho A4 */
    margin: 0 auto;
    background: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-radius: 0;
    overflow: hidden;
    position: relative;
}

/* Barra de ferramentas redesenhada */
.ql-toolbar {
    border: none;
    border-bottom: 2px solid #e1e5e9;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 12px 20px;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
}

/* Grupos da toolbar */
.ql-toolbar .ql-formats {
    display: inline-flex;
    align-items: center;
    margin: 0 8px 0 0;
    padding: 0 8px 0 0;
    border-right: 1px solid rgba(255,255,255,0.2);
    gap: 4px;
}

.ql-toolbar .ql-formats:last-child {
    border-right: none;
    margin-right: 0;
    padding-right: 0;
}

/* Botões da toolbar */
.ql-toolbar button {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    background: rgba(255,255,255,0.1);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0 1px;
    padding: 0;
    backdrop-filter: blur(10px);
}

.ql-toolbar button:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.ql-toolbar button.ql-active {
    background: rgba(255,255,255,0.3);
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.ql-toolbar button svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

/* Dropdowns (Pickers) */
.ql-toolbar .ql-picker {
    color: white;
    border-radius: 6px;
    background: rgba(255,255,255,0.1);
    border: none;
    height: 32px;
    min-width: 90px;
    backdrop-filter: blur(10px);
    margin: 0 1px;
}

.ql-toolbar .ql-picker-label {
    border: none;
    border-radius: 6px;
    padding: 6px 10px;
    background: transparent;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    height: 32px;
    display: flex;
    align-items: center;
    font-size: 13px;
    font-weight: 500;
}

.ql-toolbar .ql-picker-label:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-1px);
}

.ql-toolbar .ql-picker.ql-expanded .ql-picker-label {
    background: rgba(255,255,255,0.3);
}

/* Dropdown options */
.ql-toolbar .ql-picker-options {
    background: white;
    border: none;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    padding: 8px;
    margin-top: 8px;
    max-height: 250px;
    overflow-y: auto;
    backdrop-filter: blur(20px);
}

.ql-toolbar .ql-picker-item {
    padding: 10px 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #333;
    font-size: 14px;
    border-radius: 4px;
    margin: 2px 0;
}

.ql-toolbar .ql-picker-item:hover {
    background: #f0f4ff;
    color: #667eea;
}

.ql-toolbar .ql-picker-item.ql-selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Área do editor */
.ql-container {
    border: none;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 12pt;
    line-height: 1.6;
    background: white;
}

.ql-editor {
    padding: 2.5cm 2cm;
    min-height: 29.7cm; /* Altura A4 */
    background: white;
    color: #2c3e50;
    border: none;
    outline: none;
    position: relative;
}

.ql-editor.ql-blank::before {
    color: #95a5a6;
    font-style: italic;
    left: 2cm;
    right: 2cm;
    font-size: 12pt;
    content: 'Comece a escrever seu artigo aqui...';
}

/* Estilos para elementos do texto */
.ql-editor h1 {
    font-size: 24pt;
    font-weight: 700;
    color: #2c3e50;
    margin: 24px 0 16px 0;
    line-height: 1.2;
}

.ql-editor h2 {
    font-size: 20pt;
    font-weight: 600;
    color: #34495e;
    margin: 20px 0 14px 0;
    line-height: 1.3;
}

.ql-editor h3 {
    font-size: 16pt;
    font-weight: 600;
    color: #34495e;
    margin: 16px 0 12px 0;
    line-height: 1.4;
}

.ql-editor p {
    margin: 0 0 12px 0;
    text-align: justify;
}

.ql-editor blockquote {
    border-left: 4px solid #667eea;
    padding-left: 16px;
    margin: 16px 0;
    font-style: italic;
    background: #f8f9ff;
    padding: 16px;
    border-radius: 0 8px 8px 0;
}

.ql-editor ul, .ql-editor ol {
    padding-left: 24px;
    margin: 12px 0;
}

.ql-editor li {
    margin: 6px 0;
}

.ql-stroke {
  stroke: black;
  stroke-width: 1;
}

.ql-snow .ql-color-picker .ql-picker-label svg, .ql-snow .ql-icon-picker .ql-picker-label svg {
    right: 2px;
    height: 16px;
}

/* Responsividade */
@media (max-width: 768px) {
    .quill-editor-wrapper {
        padding: 10px;
    }
    
    .quill-editor-container {
        max-width: 100%;
        border-radius: 0;
    }
    
    .ql-toolbar {
        padding: 8px 12px;
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .ql-toolbar button {
        width: 30px;
        height: 30px;
    }
    
    .ql-toolbar button svg {
        width: 14px;
        height: 14px;
    }
    
    .ql-toolbar .ql-picker {
        height: 30px;
        min-width: 75px;
    }
    
    .ql-toolbar .ql-picker-label {
        height: 30px;
        padding: 5px 8px;
        font-size: 12px;
    }
    
    .ql-editor {
        padding: 1cm 0.5cm;
        min-height: auto;
    }
    
    .ql-editor.ql-blank::before {
        left: 0.5cm;
        right: 0.5cm;
    }
}

@media (max-width: 480px) {
    .ql-toolbar {
        padding: 6px 8px;
    }
    
    .ql-toolbar button {
        width: 28px;
        height: 28px;
    }
    
    .ql-toolbar button svg {
        width: 13px;
        height: 13px;
    }
    
    .ql-toolbar .ql-picker {
        height: 28px;
        min-width: 65px;
    }
    
    .ql-toolbar .ql-picker-label {
        height: 28px;
        padding: 4px 6px;
        font-size: 11px;
    }
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.quill-editor-container {
    animation: fadeIn 0.5s ease-out;
}

/* Scrollbar personalizada */
.ql-editor::-webkit-scrollbar {
    width: 8px;
}

.ql-editor::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.ql-editor::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

.ql-editor::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

/* Indicador de contagem de palavras */
.word-count {
    position: absolute;
    bottom: 10px;
    right: 20px;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

/* Notificação de auto-save */
.auto-save-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 500;
    z-index: 10000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
// Configuração única do Quill.js
document.addEventListener('DOMContentLoaded', function() {
    // Prevenir múltiplas inicializações
    if (window.quillInitialized) {
        return;
    }
    
    const editors = document.querySelectorAll('.summernote');
    
    editors.forEach(function(element) {
        // Evitar múltiplas inicializações por elemento
        if (element.dataset.quillProcessed) {
            return;
        }
        element.dataset.quillProcessed = '1';
        
        // Criar wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'quill-editor-wrapper';
        
        // Criar container
        const container = document.createElement('div');
        container.className = 'quill-editor-container';
        
        // Criar div para o editor
        const editorDiv = document.createElement('div');
        editorDiv.id = 'editor-' + Math.random().toString(36).substr(2, 9);
        
        // Criar contador de palavras
        const wordCount = document.createElement('div');
        wordCount.className = 'word-count';
        wordCount.textContent = '0 palavras';
        
        container.appendChild(editorDiv);
        container.appendChild(wordCount);
        wrapper.appendChild(container);
        
        element.style.display = 'none';
        element.parentNode.insertBefore(wrapper, element);
        
        // Configurar Quill
        const toolbar = [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            ['link', 'image', 'video'],
            ['clean']
        ];
        
        // Configurar Quill
        const quill = new Quill(editorDiv, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: toolbar,
                    handlers: {
                        'code-block': function() {
                            // Permitir inserção de HTML raw
                            const range = this.quill.getSelection();
                            if (range) {
                                const html = prompt('Insira o código HTML:');
                                if (html) {
                                    this.quill.clipboard.dangerouslyPasteHTML(range.index, html);
                                }
                            }
                        }
                    }
                }
            },
            formats: ['bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block', 'header', 'list', 'script', 'indent', 'direction', 'size', 'color', 'background', 'font', 'align', 'clean', 'link', 'image', 'video']
        });
        
        // Permitir inserção de HTML bruto
        quill.clipboard.addMatcher(Node.ELEMENT_NODE, function(node, delta) {
            return delta;
        });
        
        // Definir conteúdo inicial
        if (element.value) {
            quill.root.innerHTML = element.value;
        }
        
        // Função para contar palavras
        function updateWordCount() {
            const text = quill.getText().trim();
            const words = text ? text.split(/\s+/).length : 0;
            wordCount.textContent = words + (words === 1 ? ' palavra' : ' palavras');
        }
        
        // Sincronizar com textarea e atualizar contador
        quill.on('text-change', function() {
            element.value = quill.root.innerHTML;
            updateWordCount();
        });
        
        // Atualizar contador inicial
        updateWordCount();
        
        // Armazenar referência
        element.quillInstance = quill;
        
        // Auto-save
        let saveTimeout;
        quill.on('text-change', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                console.log('📝 Auto-save:', quill.getLength(), 'caracteres');
            }, 3000);
        });
        
        // Atalhos de teclado
        quill.keyboard.addBinding({
            key: 'D',
            ctrlKey: true,
            shiftKey: true
        }, function() {
            const range = quill.getSelection();
            if (range) {
                const now = new Date();
                const dateStr = now.toLocaleDateString('pt-BR');
                quill.insertText(range.index, dateStr);
            }
        });
        
        quill.keyboard.addBinding({
            key: 'H',
            ctrlKey: true,
            shiftKey: true
        }, function() {
            const range = quill.getSelection();
            if (range) {
                quill.insertText(range.index, '\n---\n');
            }
        });
    });
    
    window.quillInitialized = true;
});

// Scripts auxiliares
$(document).ready(function() {
    // Confirmação de exclusão
    $('.delete-btn').click(function(e) {
        if (!confirm('⚠️ Tem certeza que deseja eliminar este item?\n\nEsta ação não pode ser desfeita.')) {
            e.preventDefault();
        }
    });
    
    // Fade out de alertas
    setTimeout(function() {
        $('.alert').fadeOut(500);
    }, 5000);
    
    // Sincronizar dados antes do envio
    $('form').on('submit', function() {
        $('.summernote').each(function() {
            if (this.quillInstance) {
                this.value = this.quillInstance.root.innerHTML;
            }
        });
        
        // Mostrar indicador de carregamento
        const submitBtn = $(this).find('button[type="submit"]');
        if (submitBtn.length) {
            submitBtn.html('💾 Salvando...').prop('disabled', true);
        }
    });
    
    // Atalhos de teclado globais
    $(document).on('keydown', function(e) {
        // Ctrl+S para salvar
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const form = $('.summernote').closest('form');
            if (form.length) {
                form.submit();
            }
        }
        
        // Esc para limpar seleção
        if (e.key === 'Escape') {
            const activeEditor = document.querySelector('.ql-editor:focus');
            if (activeEditor) {
                activeEditor.blur();
            }
        }
    });
    
    // Notificação de auto-save
    let autoSaveNotification;
    $(document).on('quill-autosave', function() {
        clearTimeout(autoSaveNotification);
        
        const notification = $('<div class="auto-save-notification">💾 Rascunho salvo automaticamente</div>');
        $('body').append(notification);
        
        autoSaveNotification = setTimeout(() => {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    });
});
</script>

</body>
</html>
