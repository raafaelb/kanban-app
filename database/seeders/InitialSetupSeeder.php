<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. Criar usuário admin padrão
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@voxtecnologia.com.br',
            'password' => bcrypt('password'),
        ]);

        // 2. Criar quadro inicial
        $board = Board::create([
            'user_id' => $admin->id,
            'name' => 'Quadro Principal',
            'slug' => 'quadro-principal',
        ]);

        // 3. Criar colunas padrão
        $defaultColumns = [
            ['title' => 'A Fazer', 'color' => '#2a5a8c', 'order' => 1],
            ['title' => 'Em Progresso', 'color' => '#f39c12', 'order' => 2],
            ['title' => 'Em Revisão', 'color' => '#9b59b6', 'order' => 3],
            ['title' => 'Concluído', 'color' => '#2ecc71', 'order' => 4]
        ];

        foreach ($defaultColumns as $column) {
            Column::create([
                'board_id' => $board->id,
                'title' => $column['title'],
                'color' => $column['color'],
                'order' => $column['order']
            ]);
        }

        $this->command->info('Setup inicial completo!');
        $this->command->info('Email: admin@voxtecnologia.com.br');
        $this->command->info('Senha: password');
    }
}
