<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Gestão de produtos
        Permission::create(['name' => 'criar-produto']);
        Permission::create(['name' => 'editar-produto']);
        Permission::create(['name' => 'ver-produto']);
        Permission::create(['name' => 'eliminar-produto']);
        Permission::create(['name' => 'gerir-inventario']);

        // Gestão de pedidos
        Permission::create(['name' => 'ver-pedidos']);
        Permission::create(['name' => 'criar-pedido']);
        Permission::create(['name' => 'editar-pedido']);
        Permission::create(['name' => 'eliminar-pedido']);
        Permission::create(['name' => 'gerir-pagamentos']);

        // Gestão de cupons
        Permission::create(['name' => 'criar-cupao']);
        Permission::create(['name' => 'editar-cupao']);
        Permission::create(['name' => 'ver-cupao']);
        Permission::create(['name' => 'eliminar-cupao']);
        Permission::create(['name' => 'gerir-promocoes']);

        // Gestão de utilizadores
        Permission::create(['name' => 'criar-utilizador']);
        Permission::create(['name' => 'editar-utilizador']);
        Permission::create(['name' => 'ver-utilizador']);
        Permission::create(['name' => 'eliminar-utilizador']);
        Permission::create(['name' => 'gerir-roles']);
        Permission::create(['name' => 'gerir-permissoes']);

        // Gestão de relatórios
        Permission::create(['name' => 'ver-relatorios-de-vendas']);
        Permission::create(['name' => 'ver-relatorios-financeiros']);
        Permission::create(['name' => 'ver-relatorios-de-trafego']);

        // Gestão de configurações
        Permission::create(['name' => 'editar-configuracoes-gerais']);
        Permission::create(['name' => 'editar-configuracoes-de-pagamento']);
        Permission::create(['name' => 'gerir-categorias']);

        // Acesso Externo
        Permission::create(['name' => 'ver-relatorios-de-marketing']);
        Permission::create(['name' => 'gerir-campanhas-de-marketing']);
        Permission::create(['name' => 'gerir-cupoes']);

        // Afiliados e Revendedores
        Permission::create(['name' => 'ver-dados-de-afiliado']);
        Permission::create(['name' => 'gerir-descontos-de-afiliados']);
    }
}
