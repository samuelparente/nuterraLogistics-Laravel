<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Criar roles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $gestor = Role::create(['name' => 'gestor']);
        $cliente = Role::create(['name' => 'cliente']);
        $externo = Role::create(['name' => 'externo']);
        $afiliado = Role::create(['name' => 'afiliado']);
        $revendedor = Role::create(['name' => 'revendedor']);

        // Associar permissões ao super-admin
        $superAdmin->syncPermissions(Permission::all());

        // Associar permissões ao admin
        $admin->syncPermissions([
            'criar-produto', 'editar-produto', 'ver-produto', 'eliminar-produto', 'gerir-inventario',
            'ver-pedidos', 'criar-pedido', 'editar-pedido', 'eliminar-pedido', 'gerir-pagamentos',
            'criar-cupao', 'editar-cupao', 'ver-cupao', 'eliminar-cupao', 'gerir-promocoes',
            'criar-utilizador', 'editar-utilizador', 'ver-utilizador', 'eliminar-utilizador',
            'gerir-roles', 'gerir-permissoes', 'ver-relatorios-de-vendas', 'ver-relatorios-financeiros',
            'ver-relatorios-de-trafego', 'editar-configuracoes-gerais', 'editar-configuracoes-de-pagamento',
            'gerir-categorias', 'ver-relatorios-de-marketing', 'gerir-campanhas-de-marketing', 'gerir-cupoes',
        ]);

        // Associar permissões ao gestor
        $gestor->syncPermissions([
            'ver-produto', 'gerir-inventario', 'ver-pedidos', 'criar-pedido', 'editar-pedido', 'ver-relatorios-de-vendas',
            'gerir-cupoes', 'gerir-categorias',
        ]);

        // Associar permissões ao cliente
        $cliente->syncPermissions([
            'ver-produto', 'criar-pedido', 'ver-pedidos',
        ]);

        // Associar permissões ao externo
        $externo->syncPermissions([
            'ver-relatorios-de-marketing', 'gerir-campanhas-de-marketing', 'gerir-cupoes',
        ]);

        // Associar permissões ao afiliado
        $afiliado->syncPermissions([
            'ver-dados-de-afiliado', 'gerir-descontos-de-afiliados',
        ]);

        // Associar permissões ao revendedor
        $revendedor->syncPermissions([
            'ver-dados-de-afiliado', 'gerir-descontos-de-afiliados',
        ]);
    }
}
