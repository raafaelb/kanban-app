<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $board->name }} - Vox Tecnologia</title>
    <link rel="icon" href="https://site.voxtecnologia.com.br/wp-content/uploads/2024/07/logo_vox_512px-150x150.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SortableJS para drag-and-drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <link href="{{ asset('css/kanban.css') }}" rel="stylesheet">

</head>
<body data-board-id="{{ $board->id }}">
    <header class="vox-header navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar-btn me-3" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <a class="navbar-brand d-flex align-items-center me-auto" href="#">
                    <i class="fas fa-kanban me-2 text-white"></i>
                    <span class="fw-bold text-white d-none d-md-inline">VOX KANBAN</span>
                </a>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown me-2 me-lg-3">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle d-flex align-items-center"
                            type="button" id="boardsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-table me-1"></i>
                        <span class="d-none d-sm-inline">{{ Str::limit($board->name, 15) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="boardsDropdown">
                        @foreach($boards as $userBoard)
                            <li>
                                <a class="dropdown-item {{ $board->id === $userBoard->id ? 'active' : '' }}"
                                   href="{{ route('boards.show', $userBoard->slug) }}">
                                   {{ $userBoard->name }}
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item add-board-btn">
                                <i class="fas fa-plus me-2"></i>Criar novo quadro
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="user-avatar me-2 me-lg-3" data-bs-toggle="tooltip" title="{{ auth()->user()->name }}">
                    {{ substr(auth()->user()->name, 0, 1) }}{{ substr(explode(' ', auth()->user()->name)[1] ?? '', 0, 1) }}
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        <span class="d-none d-sm-inline">Sair</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-2" id="sidebar">
                <div class="vox-card p-3 mb-4">
                    <h5 class="mb-3 text-center">Filtrar Tarefas</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="filterHigh" checked>
                        <label class="form-check-label" for="filterHigh">
                            <span class="badge bg-danger">Alta Prioridade</span>
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="filterMedium" checked>
                        <label class="form-check-label" for="filterMedium">
                            <span class="badge bg-warning text-dark">Média Prioridade</span>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="filterLow" checked>
                        <label class="form-check-label" for="filterLow">
                            <span class="badge bg-success">Baixa Prioridade</span>
                        </label>
                    </div>
                    <hr>
                    <h5 class="mb-3 text-center">Estatísticas</h5>
                    <div class="stats-card text-center">
                        <i class="fas fa-tasks fa-2x mb-2"></i>
                        <h4 id="totalTasks">{{ $totalTasks }}</h4>
                        <p class="mb-0">Tarefas Totais</p>
                    </div>
                </div>

                @if($teamMembers->isNotEmpty())
                <div class="vox-card p-3">
                    <h5 class="mb-3 text-center">Equipe</h5>
                    @foreach($teamMembers as $member)
                    <div class="d-flex align-items-center mb-3">
                        <div class="user-avatar me-2" style="background-color: {{ $member->color ?? '#2a5a8c' }};">
                            {{ substr($member->name, 0, 1) }}{{ substr(explode(' ', $member->name)[1] ?? '', 0, 1) }}
                        </div>
                        <div>
                            <p class="mb-0">{{ $member->name }}</p>
                            <small class="text-muted">{{ $member->role ?? 'Membro' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="col-lg-10">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div class="mb-2 mb-md-0">
                        <h2 class="mb-0">{{ $board->name }}</h2>
                        <p class="text-muted mb-0">Dashboard Kanban - {{ now()->format('d/m/Y') }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                        <button class="btn btn-primary flex-grow-1 flex-md-grow-0" id="newTaskBtn">
                            <i class="fas fa-plus me-1"></i>
                            <span class="d-none d-sm-inline">Nova Tarefa</span>
                            <span class="d-inline d-sm-none">Tarefa</span>
                        </button>
                        <button class="btn btn-outline-primary flex-grow-1 flex-md-grow-0" id="newColumnBtn">
                            <i class="fas fa-columns me-1"></i>
                            <span class="d-none d-sm-inline">Nova Coluna</span>
                            <span class="d-inline d-sm-none">Coluna</span>
                        </button>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Arraste as tarefas entre as colunas para atualizar seu status.
                    Clique no ícone <i class="fas fa-ellipsis-v"></i> para gerenciar suas colunas.
                </div>

                <div class="kanban-container-wrapper">
                    <div class="kanban-container" id="kanbanContainer">
                        @foreach($board->columns as $column)
                        <div class="kanban-column" id="col-{{ $column->id }}">
                            <div class="kanban-column-header" style="background-color: {{ $column->color }}">
                                <h5 class="mb-0">{{ $column->title }}</h5>
                                <span class="badge bg-light text-dark badge-column">{{ $column->tasks->count() }}</span>

                                <div class="column-actions">
                                    <button class="column-actions-btn" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item edit-column" href="#" data-id="{{ $column->id }}">
                                                <i class="fas fa-edit me-2"></i>Editar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tasks-container" id="tasks-{{ $column->id }}">
                                @foreach($column->tasks as $task)
                                <div class="kanban-task task-{{ $task->priority }}"
                                     data-task-id="{{ $task->id }}"
                                     draggable="true">
                                    <div class="d-flex justify-content-between">
                                        <h6>{{ $task->title }}</h6>
                                        <div class="dropdown">
                                            <button class="btn btn-sm p-0" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item edit-task" href="#" data-id="{{ $task->id }}">
                                                        <i class="fas fa-edit me-2"></i>Editar
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="small mb-2">{{ $task->description }}</p>
                                    <div class="d-flex justify-content-between date-div">
                                        <span class="badge {{ $task->priority === 'high' ? 'bg-danger' : ($task->priority === 'medium' ? 'bg-warning text-dark' : 'bg-success') }}">
                                            {{ $task->priority === 'high' ? 'Alta' : ($task->priority === 'medium' ? 'Média' : 'Baixa') }}
                                        </span>
                                        @if($task->due_date)
                                        <small class="text-muted date-task">
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        </small>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button class="add-task-btn" data-column="{{ $column->id }}">
                                <i class="fas fa-plus me-1"></i> Adicionar Tarefa
                            </button>
                        </div>
                        @endforeach

                        <div class="add-column-btn" id="addColumnBtn">
                            <i class="fas fa-plus-circle me-2"></i> Adicionar Coluna
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Novo Quadro -->
    <div class="modal fade" id="boardModal" tabindex="-1" aria-labelledby="boardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="boardModalLabel">Criar Novo Quadro Kanban</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="boardForm">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="boardName" class="form-label">Nome do Quadro</label>
                                    <input type="text" class="form-control" id="boardName" name="name" placeholder="Ex: Projeto Vox" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="vox-card p-3">
                                    <h6 class="mb-3">Modelos Prontos</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="template" id="templateBasic" value="basic">
                                        <label class="form-check-label" for="templateBasic">
                                            Básico (To Do, Doing, Done)
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="template" id="templateDevelopment" value="development">
                                        <label class="form-check-label" for="templateDevelopment">
                                            Desenvolvimento (Backlog, To Do, In Progress, Review, Done)
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="template" id="templateSupport" value="support">
                                        <label class="form-check-label" for="templateSupport">
                                            Suporte (Novo, Em Análise, Em Progresso, Teste, Resolvido)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Colunas Iniciais</h5>
                            <div id="columnsPreview" class="d-flex flex-wrap gap-2 mb-3">
                                <!-- Preview das colunas será gerado aqui -->
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2" id="addColumnBtnModal">
                                    <i class="fas fa-plus me-1"></i> Adicionar Coluna
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveBoardBtn">Criar Quadro</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nova/Edição Tarefa -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="taskModalLabel">Nova Tarefa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        @csrf
                        <input type="hidden" name="task_id" id="taskId">
                        <input type="hidden" name="board_id" value="{{ $board->id }}">

                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Título da Tarefa</label>
                            <input type="text" class="form-control" id="taskTitle" name="title" placeholder="Ex: Implementar autenticação" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Descrição</label>
                            <textarea class="form-control" id="taskDescription" name="description" rows="3" placeholder="Descreva a tarefa..."></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="taskPriority" class="form-label">Prioridade</label>
                                <select class="form-select" id="taskPriority" name="priority" required>
                                    <option value="high">Alta</option>
                                    <option value="medium" selected>Média</option>
                                    <option value="low">Baixa</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="taskDueDate" class="form-label">Prazo</label>
                                <input type="date" class="form-control" id="taskDueDate" name="due_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="taskColumn" class="form-label">Coluna</label>
                            <select class="form-select" id="taskColumn" name="column_id" required>
                                @foreach($board->columns as $column)
                                <option value="{{ $column->id }}">{{ $column->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="deleteTaskBtn" style="display:none;">Excluir</button>
                    <button type="button" class="btn btn-primary" id="saveTaskBtn">Salvar Tarefa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nova Coluna -->
    <div class="modal fade" id="columnModal" tabindex="-1" aria-labelledby="columnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="columnModalLabel">Nova Coluna</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="columnForm">
                        @csrf
                        <input type="hidden" id="columnId" name="id">
                        <input type="hidden" name="board_id" value="{{ $board->id }}">

                        <div class="mb-3">
                            <label for="columnTitle" class="form-label">Nome da Coluna</label>
                            <input type="text" class="form-control" id="columnTitle" name="title" placeholder="Ex: Em Teste" required>
                        </div>
                        <div class="mb-3">
                            <label for="columnColor" class="form-label">Cor da Coluna</label>
                            <select class="form-select" id="columnColor" name="color" required>
                                <option value="#2a5a8c" selected>Azul Vox</option>
                                <option value="#e74c3c">Vermelho</option>
                                <option value="#2ecc71">Verde</option>
                                <option value="#f39c12">Amarelo</option>
                                <option value="#9b59b6">Roxo</option>
                                <option value="#1abc9c">Turquesa</option>
                            </select>
                            <div class="mt-2">
                                <span class="column-color-preview" id="colorPreview" style="background-color: #2a5a8c;"></span>
                                <span>Prévia da cor</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="deleteColumnBtn" style="display:none;">Excluir</button>
                    <button type="button" class="btn btn-primary" id="saveColumnBtn">Salvar Coluna</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/kanban.js') }}" defer></script>
</body>
</html>
