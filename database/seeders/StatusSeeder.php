<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'active', 'label_pt' => 'Ativo', 'color' => 'success'],
            ['code' => 'inactive', 'label_pt' => 'Inativo', 'color' => 'danger'],
            ['code' => 'pending', 'label_pt' => 'Pendente', 'color' => 'warning'],
            ['code' => 'blocked', 'label_pt' => 'Bloqueado', 'color' => 'secondary'],
            ['code' => 'archived', 'label_pt' => 'Arquivado', 'color' => 'dark'],
            ['code' => 'deleted', 'label_pt' => 'Removido', 'color' => 'light'],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
