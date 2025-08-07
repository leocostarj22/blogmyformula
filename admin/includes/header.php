<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - MyFormula Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=General+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Apply General Sans as base font */
        * {
            font-family: 'General Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 400;
        }
        
        /* Apply Poppins to headings and titles */
        h1, h2, h3, h4, h5, h6,
        .navbar-brand,
        .card-title,
        .sidebar .nav-link,
        .btn {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600;
        }
        
        /* Estilo para o botão sair */
        .navbar-nav .nav-link {
            color: #ffffff !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
        .border-left-primary {
            border-left: 0.25rem solid #2B80B9 !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #2B80B9 !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        .admin-logo {
            height: 32px;
            margin-right: 8px;
        }
        
        /* Personalização com cores do site */
        .btn-primary {
            background-color: #2B80B9 !important;
            border-color: #2B80B9 !important;
            color: #ffffff !important;
        }
        
        .btn-primary:hover {
            background-color: #1f5a82 !important;
            border-color: #1f5a82 !important;
            color: #ffffff !important;
        }
        
        .btn-primary:focus, .btn-primary.focus {
            box-shadow: 0 0 0 0.2rem rgba(43, 128, 185, 0.5) !important;
        }
        
        .btn-outline-primary {
            color: #ffffff !important;
            border-color: #2B80B9 !important;
            background-color: #2B80B9 !important;
        }
        
        .btn-outline-primary:hover {
            background-color: #1f5a82 !important;
            border-color: #1f5a82 !important;
            color: #ffffff !important;
        }

      
        .text-primary {
            color: #2B80B9 !important;
        }
        
        .bg-primary {
            background-color: #2B80B9 !important;
        }
        
        .navbar-brand {
            background-color: #2B80B9 !important;
        }
        
        .sidebar .nav-link.active {
            background-color: #2B80B9 !important;
            color: white !important;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(43, 128, 185, 0.1) !important;
            color: #2B80B9 !important;
        }
        
        .badge.bg-primary {
            background-color: #2B80B9 !important;
        }
        
        .alert-primary {
            color: #1f5a82;
            background-color: rgba(43, 128, 185, 0.1);
            border-color: rgba(43, 128, 185, 0.2);
        }
        
        .form-control:focus {
            border-color: #2B80B9 !important;
            box-shadow: 0 0 0 0.2rem rgba(43, 128, 185, 0.25) !important;
        }
        
        .form-select:focus {
            border-color: #2B80B9 !important;
            box-shadow: 0 0 0 0.2rem rgba(43, 128, 185, 0.25) !important;
        }
        
        .page-link {
            color: #2B80B9 !important;
        }
        
        .page-link:hover {
            color: #1f5a82 !important;
            background-color: rgba(43, 128, 185, 0.1) !important;
            border-color: #2B80B9 !important;
        }
        
        .page-item.active .page-link {
            background-color: #2B80B9 !important;
            border-color: #2B80B9 !important;
        }
        
        /* Links gerais */
        a {
            color: #2B80B9 !important;
        }
        
        a:hover {
            color: #1f5a82 !important;
        }
        
        /* Estilo para quando o editor está em foco */
        .ck-editor__editable:focus {
            border-color: #2B80B9 !important;
            box-shadow: 0 0 0 0.2rem rgba(43, 128, 185, 0.25) !important;
            outline: none !important;
        }
        
        /* Personalização da navbar */
        .navbar-dark {
            background-color: #2B80B9 !important;
        }
        
        .special-quote {
            border-left: 4px solid #2B80B9;
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            font-style: italic;
        }
        
        /* Estilos para CKEditor */
        .ck-editor {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        
        /* Estilos para o editor CKEditor com barra de rolagem */
        /* Estilos corrigidos para o editor CKEditor - Problema de Layout */
        .ck-editor {
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .ck-editor__editable {
            min-height: 250px !important;
            max-height: 400px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 15px !important;
            line-height: 1.6 !important;
            font-size: 14px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            
            /* CORREÇÃO PRINCIPAL - Garantir que o editor respeite os limites do container */
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
            resize: none !important; /* Desabilitar resize para evitar problemas */
            
            /* Garantir que não extrapole o card-body */
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Garantir que o container do editor respeite os limites */
        .ck-editor .ck-editor__main {
            min-height: 250px !important;
            max-height: 400px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        
        /* Corrigir a toolbar para não extrapolar */
        .ck-editor .ck-toolbar {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            flex-wrap: wrap !important;
        }
        
        /* Personalização da barra de rolagem - mais compacta */
        .ck-editor__editable::-webkit-scrollbar {
            width: 8px;
        }
        
        .ck-editor__editable::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .ck-editor__editable::-webkit-scrollbar-thumb {
            background: #6c757d;
            border-radius: 4px;
            border: 1px solid #f8f9fa;
        }
        
        .ck-editor__editable::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }
        
        /* Para Firefox */
        .ck-editor__editable {
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }
        
        /* Garantir que o card-body contenha o editor */
        .card-body {
            overflow: hidden !important;
            position: relative !important;
        }
        
        /* Espaçamento adequado para o editor dentro do card */
        .card-body .ck-editor {
            margin-bottom: 0 !important;
        }
        
        /* Melhorar a visibilidade do conteúdo */
        .ck-editor__editable p {
            margin-bottom: 1em;
        }
        
        .ck-editor__editable h1,
        .ck-editor__editable h2,
        .ck-editor__editable h3,
        .ck-editor__editable h4,
        .ck-editor__editable h5,
        .ck-editor__editable h6 {
            margin-top: 1em;
            margin-bottom: 0.5em;
        }
        
        /* Estilo para quando o editor está em foco */
        .ck-editor__editable:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            outline: none !important;
        }
        
        /* Garantir que o último elemento seja visível com scroll */
        .ck-editor__editable::after {
            content: '';
            display: block;
            height: 10px;
            clear: both;
        }
        
        /* Corrigir problemas de z-index com outros elementos */
        .ck-editor .ck-dropdown__panel {
            z-index: 9999 !important;
        }
        
        .ck-editor .ck-tooltip {
            z-index: 9999 !important;
        }
        
        /* Responsividade para telas menores */
        @media (max-width: 768px) {
            .ck-editor__editable {
                min-height: 200px !important;
                max-height: 300px !important;
                padding: 10px !important;
            }
            
            .ck-editor .ck-toolbar {
                font-size: 12px !important;
            }
        }
        
        /* Garantir que o layout do grid funcione corretamente */
        .col-md-8 {
            overflow: hidden !important;
        }
        
        .col-md-4 {
            overflow: visible !important;
        }
        
        /* Personalização da barra de rolagem */
        .ck-editor__editable::-webkit-scrollbar {
            width: 14px;
        }
        
        .ck-editor__editable::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .ck-editor__editable::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #6c757d 0%, #495057 100%);
            border-radius: 8px;
            border: 2px solid #f8f9fa;
            min-height: 30px;
        }
        
        .ck-editor__editable::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #495057 0%, #343a40 100%);
        }
        
        .ck-editor__editable::-webkit-scrollbar-thumb:active {
            background: #212529;
        }
        
        /* Para Firefox */
        .ck-editor__editable {
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }
        
        /* Garantir que o editor mantenha proporções corretas */
        .ck-editor .ck-editor__main {
            min-height: 300px;
            max-height: 600px;
        }
        
        /* Melhorar a visibilidade do conteúdo */
        .ck-editor__editable p {
            margin-bottom: 1em;
        }
        
        .ck-editor__editable h1,
        .ck-editor__editable h2,
        .ck-editor__editable h3,
        .ck-editor__editable h4,
        .ck-editor__editable h5,
        .ck-editor__editable h6 {
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        
        /* Estilo para quando o editor está em foco */
        .ck-editor__editable:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
            outline: none !important;
        }
        
        /* Garantir que o último elemento seja visível */
        .ck-editor__editable::after {
            content: '';
            display: block;
            height: 20px;
            clear: both;
        }
        
        /* Melhorar a área de clique no final do editor */
        .ck-editor__editable {
            padding-bottom: 40px !important;
        }
        
        /* Para Firefox */
        .ck-editor__editable {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
        
        /* Garantir que o editor tenha altura fixa */
        .ck-editor {
            height: auto !important;
        }
        
        .ck-editor .ck-editor__main {
            height: 400px !important;
        }
        
        /* Melhorar a aparência geral do editor */
        .ck-editor__editable {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        
        /* Estilo para quando o editor está em foco */
        .ck-editor__editable:focus {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }
        
        .ck-toolbar {
            background-color: #f8f9fc !important;
            border-bottom: 1px solid #d1d3e2 !important;
        }
        
        .ck-content h1, .ck-content h2, .ck-content h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .ck-content blockquote {
            border-left: 4px solid #5a5c69;
            padding-left: 1rem;
            margin: 1rem 0;
            font-style: italic;
            background-color: #f8f9fc;
        }
        /* Estilos personalizados para o editor */
        .highlight {
            background-color: yellow;
            padding: 2px 4px;
        }
        
        .small-text {
            font-size: 0.8em;
        }
        
        .large-text {
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .special-quote {
            border-left: 4px solid #007bff;
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top flex-md-nowrap p-0 shadow" style="background-color: #2B80B9 !important;">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 d-flex align-items-center" href="index.php" style="background-color: #2B80B9 !important;">
            <img src="../assets/images/logo-myformula-colors-branco.png" alt="MyFormula Logo" class="admin-logo">
            <span style="color:#d1d3e2; padding-left: 10px;">Admin</span>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">