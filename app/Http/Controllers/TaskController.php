<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(Request $request, Board $board)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:high,medium,low',
            'due_date' => 'nullable|date',
            'column_id' => 'required|exists:columns,id'
        ]);

        // Garantir que a coluna pertence a esse board
        $column = $board->columns()->findOrFail($validated['column_id']);

        // Criar a tarefa via relação de Column->tasks
        $task = $column->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    // Obter detalhes de uma tarefa
    public function showTask(Board $board, Task $task)
    {
        return response()->json($task);
    }

    public function updateTask(Request $request, Board $board, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:high,medium,low',
            'due_date' => 'nullable|date',
            'column_id' => 'required|exists:columns,id'
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    // Excluir uma tarefa
    public function destroyTask(Board $board, Task $task)
    {
        $task->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function move(Board $board, Task $task, Request $request)
    {
        $data = $request->validate([
            'new_column_id' => 'required|integer|exists:columns,id',
            'old_column_id' => 'required|integer|exists:columns,id',
            'new_order' => 'required|array',
            'old_order' => 'sometimes|array', // Alterado para "sometimes"
        ]);

        DB::transaction(function () use ($task, $data) {
            // Atualizar a coluna da tarefa movida
            $task->update(['column_id' => $data['new_column_id']]);

            // Atualizar a ordem na nova coluna
            $this->updateTasksOrder($data['new_order'], $data['new_column_id']);

            // Atualizar a ordem na coluna antiga apenas se houver tarefas
            if (!empty($data['old_order'])) {
                $this->updateTasksOrder($data['old_order'], $data['old_column_id']);
            }
        });

        return response()->json(['message' => 'Tarefa movida e ordem atualizada com sucesso!']);
    }

    // Método auxiliar para atualizar ordens em massa
    protected function updateTasksOrder(array $tasks, $columnId)
    {
        if (empty($tasks)) return;

        $cases = [];
        $ids = [];
        $params = [];

        foreach ($tasks as $task) {
            // Garantir que os valores sejam inteiros
            $taskId = (int) $task['id'];
            $order = (int) $task['order'];

            $cases[] = "WHEN id = ? THEN ?";
            $params[] = $taskId;
            $params[] = $order;
            $ids[] = $taskId;
        }

        $cases = implode(' ', $cases);
        $idList = implode(',', array_fill(0, count($ids), '?')); // Placeholders para IN()

        // Query corrigida com CAST explícito
        $query = "
            UPDATE tasks
            SET \"order\" = CASE {$cases} END::integer
            WHERE id IN ({$idList}) AND column_id = ?
        ";

        // Adicionar os IDs e o column_id aos parâmetros
        $params = array_merge($params, $ids, [$columnId]);

        DB::update($query, $params);
    }

    public function stats(Board $board)
    {
        // Carregar colunas e tarefas ordenadas
        $board->load([
            'columns' => function($query) {
                $query->orderBy('order');
            },
            'columns.tasks' => function($query) {
                $query->orderBy('order');
            }
        ]);

        // Calcular estatísticas
        $totalTasks = $board->columns->sum(function($column) {
            return $column->tasks->count();
        });

        $completedTasks = $board->columns->firstWhere('title', 'Concluído')
            ? $board->columns->firstWhere('title', 'Concluído')->tasks->count()
            : 0;

        return response()->json([
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'columns' => $board->columns
        ]);
    }
}
