<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - MyFormula Blog</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="shortcut icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="apple-touch-icon" href="../assets/images/favicon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Import Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Apply Inter as base font */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        
        /* Estilos para TinyMCE */
        .tox-tinymce {
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }
        
        .tox .tox-toolbar {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
        
        .tox .tox-edit-area {
            padding: 0 !important;
        }
        
        .tox .tox-edit-area__iframe {
            background: #fff !important;
        }
        
        /* Garantir que o textarea seja visível caso o TinyMCE falhe */
        #content {
            min-height: 400px;
            resize: vertical;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Estilo para botões personalizados */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
        }
        
        .btn-outline-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
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