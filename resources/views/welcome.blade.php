<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vox Tecnologia - Processo Seletivo</title>
    <link rel="icon" href="https://site.voxtecnologia.com.br/wp-content/uploads/2024/07/logo_vox_512px-150x150.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .vox-gradient {
            background: linear-gradient(135deg, #0c2340 0%, #1e3a5f 100%);
        }
        .vox-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .vox-card:hover {
            transform: translateY(-5px);
        }
        .vox-header {
            background-color: rgba(12, 35, 64, 0.95);
            backdrop-filter: blur(10px);
        }
        .vox-footer {
            background-color: #0c2340;
        }
        .btn-vox {
            background-color: #2a5a8c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-vox:hover {
            background-color: #1e3a5f;
            transform: translateY(-2px);
        }
        .hero-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <header class="vox-header navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-kanban me-2" viewBox="0 0 16 16">
                    <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h11zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2h-11z"/>
                    <path d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3z"/>
                </svg>
                <span class="fw-bold">VOX TECNOLOGIA</span>
            </a>

            <div class="d-flex align-items-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/boards') }}" class="btn btn-vox me-2">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-vox">
                                Registrar
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <section class="hero-section vox-gradient text-white">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-4">
                        Desafio Técnico<br>
                        <span class="text-warning">Desenvolvedor Fullstack</span>
                    </h1>
                    <p class="lead mb-4">
                        Projeto desenvolvido para o processo seletivo da Vox Tecnologia,
                        especializada em soluções digitais para gestão pública.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#sobre" class="btn btn-vox btn-lg px-4">
                            Sobre o Projeto
                        </a>
                        <a href="#tecnologias" class="btn btn-outline-light btn-lg px-4">
                            Tecnologias
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="vox-card bg-white text-dark p-4">
                        <h3 class="mb-3 text-primary">Sistema Kanban</h3>
                        <div class="d-flex gap-3 mb-4">
                            <div class="w-33">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-muted">TO DO</h6>
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small>Tarefa #1</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-33">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-muted">DOING</h6>
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small>Tarefa #2</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-33">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-muted">DONE</h6>
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small>Tarefa #3</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mb-0">
                            Sistema completo com autenticação, CRUD e drag-and-drop
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="sobre" class="py-5 bg-light">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="mb-4">Sobre a Vox Tecnologia</h2>
                    <div class="vox-card bg-white p-5">
                        <p class="lead mb-4">
                            Há mais de 14 anos, somos uma empresa especializada no desenvolvimento de
                            soluções digitais com foco na gestão pública.
                        </p>
                        <p>
                            Nossa missão é oferecer a melhor plataforma tecnológica para o aperfeiçoamento
                            do ambiente de negócios e a transparência da gestão pública de nossos clientes.
                        </p>
                        <div class="mt-4">
                            <span class="badge bg-primary me-2">Laravel</span>
                            <span class="badge bg-primary me-2">PHP 8.2+</span>
                            <span class="badge bg-primary me-2">PostgreSQL</span>
                            <span class="badge bg-primary me-2">Bootstrap</span>
                            <span class="badge bg-primary">jQuery</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="tecnologias" class="py-5 bg-white">
        <div class="container py-5">
            <h2 class="text-center mb-5">Tecnologias Utilizadas</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="vox-card h-100 p-4 text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#2a5a8c" viewBox="0 0 24 24">
                                <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 19h-2v-9h2v9zm4 0h-2v-9h2v9zm-6-11.5c0-.828.671-1.5 1.5-1.5s1.5.672 1.5 1.5c0 .829-.671 1.5-1.5 1.5s-1.5-.671-1.5-1.5zm10 4.5v4h-2v-4c0-1.104-.896-2-2-2h-4v-2h4c2.209 0 4 1.791 4 4z"/>
                            </svg>
                        </div>
                        <h4 class="mb-3">Laravel 11</h4>
                        <p class="text-muted">
                            Framework PHP moderno para desenvolvimento ágil com Eloquent ORM,
                            autenticação e APIs RESTful.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="vox-card h-100 p-4 text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#2a5a8c" viewBox="0 0 24 24">
                                <path d="M18.5 2h-13c-1.379 0-2.5 1.121-2.5 2.5v13c0 1.379 1.121 2.5 2.5 2.5h13c1.379 0 2.5-1.121 2.5-2.5v-13c0-1.379-1.121-2.5-2.5-2.5zm-10 5h2v10h-2v-10zm7 0h2v10h-2v-10zm-3.5 0h2v10h-2v-10z"/>
                            </svg>
                        </div>
                        <h4 class="mb-3">Sistema Kanban</h4>
                        <p class="text-muted">
                            Quadro de tarefas com drag-and-drop, categorias personalizáveis
                            e organização visual de fluxo de trabalho.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="vox-card h-100 p-4 text-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#2a5a8c" viewBox="0 0 24 24">
                                <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-1 17l-5-5.299 1.399-1.43 3.574 3.736 6.572-7.007 1.455 1.403-8 8.597z"/>
                            </svg>
                        </div>
                        <h4 class="mb-3">Requisitos</h4>
                        <ul class="text-start text-muted">
                            <li>Autenticação de usuários</li>
                            <li>CRUD completo</li>
                            <li>Drag-and-drop</li>
                            <li>PostgreSQL</li>
                            <li>Bootstrap + jQuery</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="vox-footer text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h4 class="mb-3">Vox Tecnologia</h4>
                    <p>
                        Especialistas em soluções digitais para gestão pública há mais de 14 anos.
                    </p>
                    <p>
                        Sede: João Pessoa - PB
                    </p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="mb-3">Processo Seletivo</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">Desenvolvedor Fullstack</li>
                        <li class="mb-2">Laravel | PHP | PostgreSQL</li>
                        <li class="mb-2">Home Office</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-3">Candidato</h5>
                    <p>Rafael Branco</p>
                    <p>
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </p>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="text-center">
                <p class="mb-0">© 2025 Vox Tecnologia - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
