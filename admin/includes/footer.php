<!-- Remover estas linhas antigas do Bootstrap 3.4.1:
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
-->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 5.3.7 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

<!-- TinyMCE Self-Hosted (Gratuito) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>

<script>
$(document).ready(function() {
    // Verificar se existe o elemento content
    const contentElement = document.getElementById('content');
    if (contentElement) {
        console.log('✅ Elemento content encontrado');
        
        // Inicializar TinyMCE Self-Hosted
        tinymce.init({
            selector: '#content',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: Inter, Arial, sans-serif; font-size: 14px }',
            language: 'pt_BR',
            branding: false,
            promotion: false,
            license_key: 'gpl', // Chave para versão open source
            setup: function (editor) {
                editor.on('change', function () {
                    // Auto-save functionality
                    const content = editor.getContent();
                    localStorage.setItem('post_backup_' + window.location.pathname, content);
                    console.log('💾 Backup automático salvo');
                });
                
                editor.on('init', function () {
                    console.log('✅ TinyMCE inicializado com sucesso');
                    
                    // Restore backup if exists
                    const backup = localStorage.getItem('post_backup_' + window.location.pathname);
                    if (backup && backup !== editor.getContent()) {
                        if (confirm('Encontramos um backup do seu post. Deseja restaurá-lo?')) {
                            editor.setContent(backup);
                        }
                    }
                });
            }
        });
    } else {
        console.log('❌ Elemento content não encontrado');
    }
});

// Sincronizar editor antes do envio do formulário
$(document).on('submit', '#editPostForm, #addPostForm', function(e) {
    // Sincronizar dados do TinyMCE
    if (tinymce.get('content')) {
        const content = tinymce.get('content').getContent();
        $('#content').val(content);
        
        // Remover backup após salvar
        localStorage.removeItem('post_backup_' + window.location.pathname);
    }
    
    // Desabilitar botão de salvar
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    }
});

// Atalhos de teclado
$(document).keydown(function(e) {
    // Ctrl+S para salvar
    if (e.ctrlKey && e.which === 83) {
        e.preventDefault();
        $('#editPostForm, #addPostForm').submit();
    }
});
</script>

</body>
</html>
<!-- CKEditor 5 CSS -->
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/46.0.1/ckeditor5.css">

<!-- CKEditor 5 JS -->
<script src="https://cdn.ckeditor.com/ckeditor5/46.0.1/ckeditor5.umd.js"></script>

<script>
$(document).ready(function() {
    // Aguardar um pouco para garantir que tudo carregou
    setTimeout(function() {
        // Verificar se existe o elemento content
        const contentElement = document.getElementById('content');
        if (contentElement) {
            console.log('✅ Elemento content encontrado');
            
            // Destruir instância existente se houver
            if (window.editorInstance) {
                window.editorInstance.destroy();
            }
            
            // Inicializar CKEditor 5
            const {
                ClassicEditor,
                Essentials,
                Bold,
                Italic,
                Underline,
                Strikethrough,
                Font,
                Paragraph,
                Heading,
                Link,
                List,
                Alignment,
                Image,
                ImageCaption,
                ImageStyle,
                ImageToolbar,
                ImageUpload,
                ImageResize,
                MediaEmbed,
                Table,
                TableToolbar,
                BlockQuote,
                CodeBlock,
                HorizontalLine,
                Indent,
                IndentBlock,
                RemoveFormat,
                SourceEditing
            } = CKEDITOR;
            
            ClassicEditor
                .create(contentElement, {
                    plugins: [
                        Essentials, Bold, Italic, Underline, Strikethrough,
                        Font, Paragraph, Heading, Link, List, Alignment,
                        Image, ImageCaption, ImageStyle, ImageToolbar, ImageUpload, ImageResize,
                        MediaEmbed, Table, TableToolbar, BlockQuote, CodeBlock,
                        HorizontalLine, Indent, IndentBlock, RemoveFormat, SourceEditing
                    ],
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                            'alignment', '|',
                            'numberedList', 'bulletedList', '|',
                            'outdent', 'indent', '|',
                            'link', 'imageUpload', 'mediaEmbed', 'insertTable', '|',
                            'blockQuote', 'codeBlock', 'horizontalLine', '|',
                            'removeFormat', 'sourceEditing', '|',
                            'undo', 'redo'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Parágrafo', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Título 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Título 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Título 3', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Título 4', class: 'ck-heading_heading4' }
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side',
                            '|',
                            'toggleImageCaption',
                            'imageTextAlternative',
                            '|',
                            'imageResize'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    },
                    language: 'pt-br'
                })
                .then(editor => {
                    window.editorInstance = editor;
                    console.log('✅ CKEditor 5 inicializado com sucesso');
                    
                    // Auto-save functionality
                    let autoSaveTimeout;
                    editor.model.document.on('change:data', () => {
                        clearTimeout(autoSaveTimeout);
                        autoSaveTimeout = setTimeout(() => {
                            const content = editor.getData();
                            localStorage.setItem('post_backup_' + window.location.pathname, content);
                            console.log('💾 Backup automático salvo');
                        }, 2000);
                    });
                    
                    // Restore backup if exists
                    const backup = localStorage.getItem('post_backup_' + window.location.pathname);
                    if (backup && backup !== editor.getData()) {
                        if (confirm('Encontramos um backup do seu post. Deseja restaurá-lo?')) {
                            editor.setData(backup);
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Erro ao inicializar CKEditor 5:', error);
                });
        } else {
            console.log('❌ Elemento content não encontrado');
        }
    }, 500);
});

// Sincronizar editor antes do envio do formulário
$(document).on('submit', '#editPostForm, #addPostForm', function(e) {
    if (window.editorInstance) {
        // Sincronizar dados do editor
        const content = window.editorInstance.getData();
        $('#content').val(content);
        
        // Remover backup após salvar
        localStorage.removeItem('post_backup_' + window.location.pathname);
        
        // Desabilitar botão de salvar
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
        }
    }
});

// Atalhos de teclado
$(document).keydown(function(e) {
    // Ctrl+S para salvar
    if (e.ctrlKey && e.which === 83) {
        e.preventDefault();
        $('#editPostForm, #addPostForm').submit();
    }
});
</script>

</body>
</html>
