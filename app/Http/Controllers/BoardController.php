<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class BoardController extends Controller
{
    public function index($board = null)
    {
        // Carregar quadros com contagem de colunas e ordenar por data de criação
        $boards = auth()->user()->boards()
            ->withCount('columns')
            ->orderBy('created_at', 'asc')
            ->get();

        // Se um quadro for informado, verificar se o quadro existe. Senão busca o primeiro quadro do usuário
        if ($board === null) {
            $board = $boards->first() ?? null;
        } else {
            $board = Board::where('slug', $board)->firstOrFail();
        }

        // Verificar se o usuário tem permissão para acessar o quadro
        abort_unless(auth()->user()->id === $board->user_id, 403);

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

        $teamMembers = User::all();

        return view('boards', [
            'boards' => $boards,
            'board' => $board,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'teamMembers' => $teamMembers
        ]);
    }

    public function store(Request $request)
    {
        // Validar os dados do formulário
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'columns' => 'required|array|min:1',
            'columns.*.title' => 'required|string|max:255',
            'columns.*.color' => 'required|string|max:7', // Cor em formato hexadecimal (#XXXXXX)
        ]);

        // Criar o novo quadro
        $board = new Board();
        $board->name = $validated['name'];
        $board->user_id = Auth::id();
        $board->save();

        // Criar as colunas iniciais
        $order = 0;
        foreach ($validated['columns'] as $columnData) {
            $column = new Column();
            $column->board_id = $board->id;
            $column->title = $columnData['title'];
            $column->color = $columnData['color'];
            $column->order = $order++;
            $column->save();
        }

        // Redirecionar para o novo quadro criado
        return response()->json([
            'success' => true,
            'redirect' => route('boards.show', $board->slug)
        ]);
    }
}
