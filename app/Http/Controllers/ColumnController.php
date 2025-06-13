<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function getColumns(Board $board)
    {
        return response()->json($board->columns);
    }

    public function getColumn(Board $board, Column $column)
    {
        // Verifica se a coluna pertence ao board
        if ($column->board_id !== $board->id) {
            return response()->json(['error' => 'Coluna não encontrada no quadro'], 404);
        }

        return response()->json($column);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'board_id' => 'required|exists:boards,id'
        ]);

        // Obter a última ordem atual do board
        $lastOrder = Column::where('board_id', $request->board_id)
            ->max('order') ?? 0;

        $column = Column::create([
            'title' => $request->title,
            'color' => $request->color,
            'board_id' => $request->board_id,
            'order' => $lastOrder + 1 // Adiciona na próxima posição
        ]);

        return response()->json([
            'message' => 'Coluna criada com sucesso!',
            'column' => $column
        ], 201);
    }

    public function update(Request $request, Board $board, Column $column)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $column->update([
            'title' => $request->title,
            'color' => $request->color,
        ]);

        return response()->json([
            'message' => 'Coluna atualizada com sucesso!',
            'column' => $column
        ]);
    }

    public function destroy(Board $board, $columnId)
    {
        // Encontrar a coluna específica dentro do board
        $column = Column::where('board_id', $board->id)
                        ->where('id', $columnId)
                        ->firstOrFail();

        $column->tasks()->delete(); // Excluir todas as tarefas da coluna
        $column->delete(); // Excluir a coluna

        return response()->json(['message' => 'Coluna excluída com sucesso!']);
    }

    public function reorderColumns(Board $board, Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:columns,id'
        ]);

        foreach ($request->order as $index => $columnId) {
            Column::where('id', $columnId)
                ->where('board_id', $board->id)
                ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
