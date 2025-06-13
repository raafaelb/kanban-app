// Configurações globais
const BOARD_ID = document.body.dataset.boardId;
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// Função para abrir o modal de coluna (criação/edição)
function openColumnModal(columnId = null) {
    const modal = $('#columnModal');
    const title = $('#columnModalLabel');
    const deleteBtn = $('#deleteColumnBtn');

    if (columnId) {
        // Modo edição
        $.ajax({
            url: `/boards/${BOARD_ID}/columns/${columnId}`,
            method: 'GET',
            success: function(column) {
                title.text('Editar Coluna');
                $('#columnId').val(column.id);
                $('#columnTitle').val(column.title);
                $('#columnColor').val(column.color);
                $('#colorPreview').css('background-color', column.color);
                deleteBtn.show();
                modal.modal('show');
            },
            error: function(error) {
                console.error('Erro ao carregar coluna:', error);
            }
        });
    } else {
        // Modo criação
        title.text('Nova Coluna');
        $('#columnId').val('');
        $('#columnTitle').val('');
        $('#columnColor').val('#2a5a8c');
        $('#colorPreview').css('background-color', '#2a5a8c');
        deleteBtn.hide();
        modal.modal('show');
    }
}

// Função para salvar uma coluna
function saveColumn() {
    const columnId = $('#columnId').val();
    const title = $('#columnTitle').val().trim();
    const color = $('#columnColor').val();

    if (!title) {
        alert('Por favor, informe o nome da coluna');
        return;
    }

    const method = columnId ? 'PUT' : 'POST';
    const url = columnId
        ? `/boards/${BOARD_ID}/columns/${columnId}`
        : `/boards/${BOARD_ID}/columns`;

    $.ajax({
        url: url,
        method: method,
        data: {
            title: title,
            color: color,
            board_id: BOARD_ID,
            _token: CSRF_TOKEN
        },
        success: function(response) {
            // Fechar o modal
            $('#columnModal').modal('hide');

            if (columnId) {
                // Modo edição: atualizar a coluna existente
                const columnElement = $(`#col-${columnId}`);

                // Atualizar o título
                columnElement.find('.kanban-column-header h5').text(title);

                // Atualizar a cor
                columnElement.find('.kanban-column-header').css('background-color', color);

                // Atualizar o botão de adicionar tarefa
                columnElement.find('.add-task-btn').attr('data-column', columnId);

                // Atualizar os IDs dos elementos
                columnElement.attr('id', `col-${response.column.id}`);
                columnElement.find('.tasks-container').attr('id', `tasks-${response.column.id}`);

                // Atualizar os links de edição/exclusão
                columnElement.find('.edit-column').attr('data-id', response.column.id);
                columnElement.find('.delete-column').attr('data-id', response.column.id);

                // Atualizar dropdown de colunas no modal de tarefas
                updateTaskColumnDropdown();

                showToast('Coluna atualizada com sucesso!', 'success');
            } else {
                // Modo criação: adicionar nova coluna
                const newColumnHtml = `
                    <div class="kanban-column" id="col-${response.column.id}">
                        <div class="kanban-column-header" style="background-color: ${color}">
                            <h5 class="mb-0">${title}</h5>
                            <span class="badge bg-light text-dark badge-column">0</span>
                            <div class="column-actions">
                                <button class="column-actions-btn" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item edit-column" href="#" data-id="${response.column.id}">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tasks-container" id="tasks-${response.column.id}">
                            <!-- Tarefas serão adicionadas aqui -->
                        </div>
                        <button class="add-task-btn" data-column="${response.column.id}">
                            <i class="fas fa-plus me-1"></i> Adicionar Tarefa
                        </button>
                    </div>
                `;

                // Inserir antes do botão de adicionar coluna
                $(newColumnHtml).insertBefore('#addColumnBtn');

                // Inicializar o drag-and-drop para a nova coluna
                initSortableForColumn(response.column.id);

                // Atualizar o drag-and-drop das colunas
                initColumnSortable();

                // Atualizar dropdown de colunas no modal de tarefas
                updateTaskColumnDropdown();

                // Mostrar mensagem
                showToast('Coluna criada com sucesso!', 'success');
            }
        },
        error: function(error) {
            console.error('Erro ao salvar coluna:', error);
            alert('Erro ao salvar coluna. Por favor, tente novamente.');
        }
    });
}

// Função para excluir uma coluna
function deleteColumn() {
    const columnId = $('#columnId').val();
    if (!columnId) return;

    if (confirm('Tem certeza que deseja excluir esta coluna? Todas as tarefas nela contidas serão removidas.')) {
        $.ajax({
            url: `/boards/${BOARD_ID}/columns/${columnId}`,
            method: 'DELETE',
            data: {
                _token: CSRF_TOKEN
            },
            success: function(response) {
                // Fechar o modal
                $('#columnModal').modal('hide');
                // Remover a coluna da interface
                $(`#col-${columnId}`).remove();

                // Atualizar dropdown de colunas no modal de tarefas
                updateTaskColumnDropdown();

                showToast('Coluna excluída com sucesso!', 'success');
            },
            error: function(error) {
                console.error('Erro ao excluir coluna:', error);
            }
        });
    }
}

// Função para atualizar o dropdown de colunas no modal de tarefas
function updateTaskColumnDropdown() {
    // Buscar as colunas atualizadas do board
    $.ajax({
        url: `/boards/${BOARD_ID}/columns`,
        method: 'GET',
        success: function(columns) {
            const columnSelect = $('#taskColumn');
            // Salvar o valor selecionado atual
            const selectedValue = columnSelect.val();

            // Limpar e recriar as opções
            columnSelect.empty();

            columns.forEach(column => {
                columnSelect.append(
                    $('<option></option>')
                        .val(column.id)
                        .text(column.title)
                );
            });

            // Restaurar a seleção anterior se ainda existir
            if (selectedValue && columnSelect.find(`option[value="${selectedValue}"]`).length) {
                columnSelect.val(selectedValue);
            }
        },
        error: function(error) {
            console.error('Erro ao buscar colunas:', error);
        }
    });
}

// Função para gerar o HTML de uma tarefa
function generateTaskHtml(task) {
    const priorityClass = `task-${task.priority}`;
    const priorityBadge = task.priority === 'high' ? 'bg-danger' :
                        (task.priority === 'medium' ? 'bg-warning text-dark' : 'bg-success');
    const priorityText = task.priority === 'high' ? 'Alta' :
                        (task.priority === 'medium' ? 'Média' : 'Baixa');
    const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('pt-BR') : '';

    return `
        <div class="kanban-task ${priorityClass}" data-task-id="${task.id}" draggable="true">
            <div class="d-flex justify-content-between">
                <h6>${task.title}</h6>
                <div class="dropdown">
                    <button class="btn btn-sm p-0" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item edit-task" href="#" data-id="${task.id}">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <p class="small mb-2">${task.description}</p>
            <div class="d-flex justify-content-between date-div">
                <span class="badge ${priorityBadge}">
                    ${priorityText}
                </span>
                ${dueDate ? `<small class="text-muted date-task">${dueDate}</small>` : ''}
            </div>
        </div>
    `;
}

// Função para abrir o modal de tarefa (criação/edição)
function openTaskModal(taskId = null, columnId = null) {
    const modal = $('#taskModal');
    const title = $('#taskModalLabel');
    const deleteBtn = $('#deleteTaskBtn');

    // Atualizar as colunas antes de abrir o modal
    updateTaskColumnDropdown();

    if (taskId) {
        // Modo edição
        title.text('Editar Tarefa');
        deleteBtn.show();

        // Buscar dados da tarefa
        $.ajax({
            url: `/boards/${BOARD_ID}/tasks/${taskId}`,
            method: 'GET',
            success: function(task) {
                // Preencher formulário
                $('#taskId').val(task.id);
                $('#taskTitle').val(task.title);
                $('#taskDescription').val(task.description);
                $('#taskPriority').val(task.priority);
                $('#taskDueDate').val(task.due_date ? task.due_date.split('T')[0] : '');
                $('#taskColumn').val(task.column_id);

                modal.modal('show');
            },
            error: function(error) {
                console.error('Erro ao carregar tarefa:', error);
            }
        });
    } else {
        // Modo criação
        title.text('Nova Tarefa');
        deleteBtn.hide();

        // Resetar formulário
        $('#taskId').val('');
        $('#taskTitle').val('');
        $('#taskDescription').val('');
        $('#taskPriority').val('medium');
        $('#taskDueDate').val('');

        // Se columnId foi passado, definir a coluna
        if (columnId) {
            $('#taskColumn').val(columnId);
        }

        modal.modal('show');
    }
}

// Função para salvar uma tarefa (criação/edição)
function saveTask() {
    const taskId = $('#taskId').val();
    const taskData = {
        title: $('#taskTitle').val().trim(),
        description: $('#taskDescription').val(),
        priority: $('#taskPriority').val(),
        due_date: $('#taskDueDate').val(),
        column_id: $('#taskColumn').val(),
        board_id: BOARD_ID,
        _token: CSRF_TOKEN
    };

    if (!taskData.title) {
        alert('Por favor, informe o título da tarefa');
        return;
    }

    const method = taskId ? 'PUT' : 'POST';
    const url = taskId
        ? `/boards/${BOARD_ID}/tasks/${taskId}`
        : `/boards/${BOARD_ID}/tasks`;

    $.ajax({
        url: url,
        method: method,
        data: taskData,
        success: function(response) {
            // Fechar o modal
            $('#taskModal').modal('hide');

            if (taskId) {
                // Obter elemento da tarefa e coluna atual
                const taskElement = $(`[data-task-id="${taskId}"]`);
                const currentColumn = taskElement.closest('.kanban-column');
                const currentColumnId = currentColumn.attr('id').replace('col-', '');
                const newColumnId = taskData.column_id;

                // Se a coluna foi alterada
                if (currentColumnId !== newColumnId) {
                    // Remover da coluna atual
                    taskElement.remove();

                    // Atualizar contador da coluna antiga
                    const currentBadge = currentColumn.find('.badge-column');
                    currentBadge.text(parseInt(currentBadge.text()) - 1);

                    // Adicionar na nova coluna
                    const newColumn = $(`#col-${newColumnId}`);
                    const newTasksContainer = $(`#tasks-${newColumnId}`);
                    const taskHtml = generateTaskHtml(response.task);
                    newTasksContainer.prepend(taskHtml);

                    // Atualizar contador da nova coluna
                    const newBadge = newColumn.find('.badge-column');
                    newBadge.text(parseInt(newBadge.text()) + 1);

                    // Re-inicializar drag-and-drop na nova coluna
                    initSortableForColumn(newColumnId);

                    // Mostrar mensagem de movimento
                    showToast('Tarefa movida para ' + newColumn.find('h5').text(), 'info');
                } else {
                    // Atualizar a tarefa na mesma coluna
                    taskElement.find('h6').text(taskData.title);
                    taskElement.find('p.small').text(taskData.description);
                    taskElement.find('.badge').text(
                        taskData.priority === 'high' ? 'Alta' :
                        (taskData.priority === 'medium' ? 'Média' : 'Baixa')
                    );
                    taskElement.find('.badge')
                        .removeClass('bg-danger bg-warning bg-success')
                        .addClass(
                            taskData.priority === 'high' ? 'bg-danger' :
                            (taskData.priority === 'medium' ? 'bg-warning text-dark' : 'bg-success')
                        );

                    // Atualizar data
                    const dueDateElement = taskElement.find('.date-task');
                    if (taskData.due_date) {
                        const dueDate = new Date(taskData.due_date).toLocaleDateString('pt-BR');
                        if (dueDateElement.length) {
                            dueDateElement.text(dueDate);
                        } else {
                            taskElement.find('.date-div').append(
                                `<small class="text-muted date-task">${dueDate}</small>`
                            );
                        }
                    } else if (dueDateElement.length) {
                        dueDateElement.remove();
                    }

                    // Atualizar classe de prioridade
                    taskElement.removeClass('task-high task-medium task-low')
                        .addClass(`task-${taskData.priority}`);
                }

                showToast('Tarefa atualizada com sucesso!', 'success');
            } else {
                // Adicionar nova tarefa na coluna selecionada
                const columnId = taskData.column_id;
                const taskHtml = generateTaskHtml(response.task);
                $(`#tasks-${columnId}`).prepend(taskHtml);

                // Atualizar o contador de tarefas na coluna
                const column = $(`#col-${columnId}`);
                const badge = column.find('.badge-column');
                badge.text(parseInt(badge.text()) + 1);

                // Re-inicializar o drag-and-drop na coluna
                initSortableForColumn(columnId);
                updateStats();
                showToast('Tarefa criada com sucesso!', 'success');
            }
        },
        error: function(error) {
            console.error('Erro ao salvar tarefa:', error);
            alert('Erro ao salvar tarefa. Por favor, tente novamente.');
        }
    });
}

// Função para excluir uma tarefa
function deleteTask() {
    const taskId = $('#taskId').val();
    if (!taskId) return;

    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        $.ajax({
            url: `/boards/${BOARD_ID}/tasks/${taskId}`,
            method: 'DELETE',
            data: {
                _token: CSRF_TOKEN
            },
            success: function(response) {
                // Fechar o modal
                $('#taskModal').modal('hide');

                // Remover a tarefa da interface
                const taskElement = $(`[data-task-id="${taskId}"]`);
                const column = taskElement.closest('.kanban-column');
                const columnId = column.attr('id').replace('col-', '');

                // Atualizar contador
                const badge = column.find('.badge-column');
                badge.text(parseInt(badge.text()) - 1);

                // Remover elemento
                taskElement.remove();

                updateStats();

                showToast('Tarefa excluída com sucesso!', 'success');
            },
            error: function(error) {
                console.error('Erro ao excluir tarefa:', error);
            }
        });
    }
}


// Função para atualizar estatísticas
function updateStats() {
    $.ajax({
        url: `/boards/${BOARD_ID}/stats`,
        method: 'GET',
        success: function(stats) {
            console.log('Estatísticas atualizadas:', stats);

            // Atualizar contador total de tarefas
            $('#totalTasks').text(stats.total_tasks);

            // // Atualizar contadores por coluna
            // stats.columns.forEach(column => {
            //     const badge = $(`#col-${column.id} .badge-column`);
            //     if (badge.length) {
            //         badge.text(column.tasks.length);
            //     }
            // });
        },
        error: function(error) {
            console.error('Erro ao carregar estatísticas:', error);
        }
    });
}

// Função para inicializar o drag-and-drop das colunas
function initColumnSortable() {
    const kanbanContainer = document.getElementById('kanbanContainer');
    new Sortable(kanbanContainer, {
        animation: 150,
        ghostClass: 'kanban-column-dragging',
        handle: '.kanban-column-header',
        onEnd: function(evt) {
            const columnIds = [];
            $('#kanbanContainer .kanban-column').each(function() {
                const id = this.id.replace('col-', '');
                if (id) columnIds.push(id);
            });

            // Enviar nova ordem para o servidor
            $.ajax({
                url: `/boards/${BOARD_ID}/columns/reorder`,
                method: 'PATCH',
                data: {
                    order: columnIds,
                    _token: CSRF_TOKEN
                },
                success: function(response) {
                    showToast('Colunas reorganizadas com sucesso!', 'success');
                },
                error: function(error) {
                    console.error('Erro ao reordenar colunas:', error);
                }
            });
        }
    });
}

// Função para inicializar drag-and-drop para uma coluna específica
function initSortableForColumn(columnId) {
    const container = $(`#tasks-${columnId}`)[0];

    // Destruir instância existente se houver
    const sortable = Sortable.get(container);
    if (sortable) {
        sortable.destroy();
    }

    // Criar nova instância
    new Sortable(container, {
        group: 'tasks',
        animation: 150,
        ghostClass: 'task-dragging',
        onEnd: function(evt) {
            const taskId = evt.item.dataset.taskId;
            const getNumericColumnId = (element) => {
                const id = element.closest('.kanban-column').id;
                return id.replace('col-', '');
            };

            const oldColumnId = getNumericColumnId(evt.from);
            const newColumnId = getNumericColumnId(evt.to);

            // Sempre coletar a ordem antiga da coluna de origem
            const oldOrder = Array.from(evt.from.children).map((child, index) => {
                return {
                    id: child.dataset.taskId,
                    order: index
                };
            });

            // Coletar a nova ordem da coluna de destino
            const newOrder = Array.from(evt.to.children).map((child, index) => {
                return {
                    id: child.dataset.taskId,
                    order: index
                };
            });

            // Dados para enviar ao servidor
            const data = {
                new_column_id: newColumnId,
                old_column_id: oldColumnId,
                new_order: newOrder,
                old_order: oldOrder, // Agora sempre enviado
                _token: CSRF_TOKEN
            };


            // Atualizar no servidor
            $.ajax({
                url: `/boards/${BOARD_ID}/tasks/${taskId}/move`,
                method: 'PATCH',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    // Atualizar contadores
                    if (oldColumnId !== newColumnId) {
                        // Atualizar coluna de origem
                        const oldColumn = $(`#col-${oldColumnId}`);
                        const oldBadge = oldColumn.find('.badge-column');
                        oldBadge.text(parseInt(oldBadge.text()) - 1);

                        // Atualizar coluna de destino
                        const newColumn = $(`#col-${newColumnId}`);
                        const newBadge = newColumn.find('.badge-column');
                        newBadge.text(parseInt(newBadge.text()) + 1);
                    }

                    // Mostrar mensagem
                    showToast('Tarefa movida com sucesso!', 'success');
                },
                error: function(error) {
                    console.error('Erro ao mover tarefa:', error);
                    showToast('Erro ao mover tarefa', 'danger');
                }
            });
        }
    });
}

// Função para mostrar notificações
function showToast(message, type = 'success') {
    // Remover toasts existentes
    $('.toast').remove();

    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    $('body').append(toastHtml);
    $('.toast').toast({ autohide: true, delay: 3000 }).toast('show');
}

// Gerar preview das colunas baseado no template selecionado
function generateColumnsPreview(template) {
    const previewContainer = $('#columnsPreview');
    previewContainer.empty();

    const templates = {
        basic: [
            { title: "To Do", color: "#2a5a8c" },
            { title: "Doing", color: "#f39c12" },
            { title: "Done", color: "#2ecc71" }
        ],
        development: [
            { title: "Backlog", color: "#9b59b6" },
            { title: "To Do", color: "#3498db" },
            { title: "In Progress", color: "#f39c12" },
            { title: "Review", color: "#e74c3c" },
            { title: "Done", color: "#2ecc71" }
        ],
        support: [
            { title: "Novo", color: "#3498db" },
            { title: "Em Análise", color: "#9b59b6" },
            { title: "Em Progresso", color: "#f39c12" },
            { title: "Teste", color: "#1abc9c" },
            { title: "Resolvido", color: "#2ecc71" }
        ]
    };

    const selectedTemplate = templates[template];

    selectedTemplate.forEach(column => {
        previewContainer.append(`
            <div class="d-inline-flex align-items-center bg-white rounded p-2 shadow-sm">
                <div class="column-color-preview me-2" style="background-color: ${column.color}"></div>
                <span class="me-2">${column.title}</span>
                <button type="button" class="btn btn-sm btn-outline-danger p-0 remove-column" style="width:24px;height:24px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);
    });
}

// Adicionar coluna personalizada
function addCustomColumn() {
    const columnName = prompt("Nome da nova coluna:");
    if (!columnName) return;

    const colors = ['#2a5a8c', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];

    $('#columnsPreview').append(`
        <div class="d-inline-flex align-items-center bg-white rounded p-2 shadow-sm">
            <div class="column-color-preview me-2" style="background-color: ${randomColor}"></div>
            <span class="me-2">${columnName}</span>
            <button type="button" class="btn btn-sm btn-outline-danger p-0 remove-column" style="width:24px;height:24px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `);
}

// Salvar novo quadro
function saveNewBoard() {
    const boardName = $('#boardName').val().trim();
    if (!boardName) {
        alert('Por favor, informe o nome do quadro');
        return;
    }

    // Coletar colunas do preview
    const columns = [];
    $('#columnsPreview .d-inline-flex').each(function() {
        const title = $(this).find('span').text();
        const rgbColor = $(this).find('.column-color-preview').css('background-color');
        const color = rgbToHex(rgbColor);
        columns.push({ title, color });
    });

    if (columns.length === 0) {
        alert('Adicione pelo menos uma coluna ao quadro');
        return;
    }

    const formData = {
        name: boardName,
        columns: columns,
        _token: CSRF_TOKEN
    };

    $.ajax({
        url: '/boards',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success && response.redirect) {
                window.location.href = response.redirect;
            }
        },
        error: function(error) {
            console.error('Erro ao criar quadro:', error);
            alert('Erro ao criar quadro. Por favor, tente novamente.');
        }
    });
}

// Função para converter cores RGB para hexadecimal
function rgbToHex(rgb) {
    // Extrai os valores de r, g, b
    const result = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/.exec(rgb);
    if (!result) return '#2a5a8c'; // cor padrão se falhar

    const r = parseInt(result[1]).toString(16).padStart(2, '0');
    const g = parseInt(result[2]).toString(16).padStart(2, '0');
    const b = parseInt(result[3]).toString(16).padStart(2, '0');

    return `#${r}${g}${b}`;
}



// Inicializar quando o documento estiver pronto
$(document).ready(function() {

    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Abrir modal para nova tarefa
    $(document).on('click', '.add-task-btn', function() {
        const columnId = $(this).data('column');
        openTaskModal(null, columnId);
    });

    $(document).on('click', '#newTaskBtn', function() {
        openTaskModal();
    });

    // Abrir modal para editar tarefa
    $(document).on('click', '.edit-task', function(e) {
        e.preventDefault();
        const taskId = $(this).data('id');
        openTaskModal(taskId);
    });

    // Abrir modal para novo quadro
    $(document).on('click', '.add-board-btn', function() {
        $('#boardModal').modal('show');
    });

    // Abrir modal para nova coluna
    $(document).on('click', '#newColumnBtn, #addColumnBtn', function() {
        openColumnModal();
    });

    // Abrir modal para editar coluna
    $(document).on('click', '.edit-column', function() {
        const columnId = $(this).data('id');
        openColumnModal(columnId);
    });

    // Salvar coluna
    $('#saveColumnBtn').click(saveColumn);

    // Excluir coluna
    $('#deleteColumnBtn').click(deleteColumn);

    // Atualizar prévia de cor
    $('#columnColor').change(function() {
        $('#colorPreview').css('background-color', $(this).val());
    });

    // Salvar tarefa (criação/edição)
    $('#saveTaskBtn').click(saveTask);

    // Excluir tarefa
    $('#deleteTaskBtn').click(deleteTask);

    // Inicializar drag-and-drop para todas as colunas
    function initAllSortable() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const columnId = column.id.replace('col-', '');
            initSortableForColumn(columnId);
        });
    }

    initAllSortable();

    // Inicializar drag-and-drop para colunas
    initColumnSortable();

    // Filtro de prioridade
    $('#filterHigh, #filterMedium, #filterLow').change(function() {
        const showHigh = $('#filterHigh').is(':checked');
        const showMedium = $('#filterMedium').is(':checked');
        const showLow = $('#filterLow').is(':checked');

        $('.kanban-task').each(function() {
            const priority = $(this).hasClass('task-high') ? 'high' :
                            $(this).hasClass('task-medium') ? 'medium' : 'low';

            // Mostrar se o filtro correspondente estiver selecionado
            if ((priority === 'high' && showHigh) ||
                (priority === 'medium' && showMedium) ||
                (priority === 'low' && showLow)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Alternar sidebar
    $('#toggleSidebar').click(function() {
        $('body').toggleClass('sidebar-collapsed');
    });

    // Abrir modal para novo quadro
    $(document).on('click', '#createBoardBtn', function(e) {
        e.preventDefault();
        $('#boardModal').modal('show');
        // Carregar template padrão
        generateColumnsPreview('basic');
        // Selecionar o radio button do template básico
        $('#templateBasic').prop('checked', true);
    });

    // Mudar template
    $('input[name="template"]').change(function() {
        generateColumnsPreview($(this).val());
    });

    // Adicionar coluna personalizada
    $('#addColumnBtnModal').click(addCustomColumn);

    // Remover coluna
    $(document).on('click', '.remove-column', function() {
        $(this).closest('.d-inline-flex').remove();
    });

    // Salvar quadro
    $('#saveBoardBtn').click(saveNewBoard);
});
