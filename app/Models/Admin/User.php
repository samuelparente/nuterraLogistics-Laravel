<?php

namespace App\Models\Admin;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;  // Importa o trait SoftDeletes

/**
 * @method bool hasRole(string|array $roles)
 * @method bool hasAnyRole(array|string $roles)
 * @method \Illuminate\Support\Collection getRoleNames()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status_id',          // Novo campo
        'is_verified',        // Novo campo
        'avatar',             // Novo campo
        'email_verified_at',  // Novo campo
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roleLabels()
    {
        $map = [
            'super-admin'     => 'Super Administrador',
            'admin-sistema'   => 'Administrador do Sistema',
            'admin'           => 'Administrador',
            'gestor'          => 'Gestor',
            'operador'        => 'Operador',
            'utilizador'      => 'Utilizador',
            'externo'         => 'Colaborador Externo',
            'marketing'       => 'Marketing',
            'conteudo'        => 'Gestor de Conteúdo',
            'financeiro'      => 'Financeiro',
            'contabilista'    => 'Contabilista',
            'cliente'         => 'Cliente',
            'afiliado'        => 'Afiliado',
            'revendedor'      => 'Revendedor',
        ];

        // Retorna um array de labels de roles
        return $this->getRoleNames()
            ->map(fn($role) => $map[$role] ?? ucfirst(str_replace('-', ' ', $role)))
            ->toArray(); 
    }

    public static function roleLabelMap(): array
    {
        return [
            'super-admin'     => 'Super Administrador',
            'admin-sistema'   => 'Administrador do Sistema',
            'admin'           => 'Administrador',
            'gestor'          => 'Gestor',
            'operador'        => 'Operador',
            'utilizador'      => 'Utilizador',
            'externo'         => 'Colaborador Externo',
            'marketing'       => 'Marketing',
            'conteudo'        => 'Gestor de Conteúdo',
            'financeiro'      => 'Financeiro',
            'contabilista'    => 'Contabilista',
            'cliente'         => 'Cliente',
            'afiliado'        => 'Afiliado',
            'revendedor'      => 'Revendedor',
        ];
    }

    public function getRoleLabelAttribute()
    {
        $role = $this->getRoleNames()->first();

        return self::roleLabelMap()[$role] ?? ucfirst(str_replace('-', ' ', $role));
    }

    public function permissionLabel($permission)
    {
        $map = [
            'criar-produto'            => 'Criar Produto',
            'editar-produto'           => 'Editar Produto',
            'ver-produto'              => 'Ver Produto',
            'eliminar-produto'           => 'Eliminar Produto',
            'gerir-inventario'         => 'Gerir Inventário',

            'ver-pedidos'              => 'Ver Pedidos',
            'criar-pedido'             => 'Criar Pedido',
            'editar-pedido'            => 'Editar Pedido',
            'eliminar-pedido'            => 'Eliminar Pedido',
            'gerir-pagamentos'         => 'Gerir Pagamentos',

            'criar-cupao'              => 'Criar Cupão',
            'editar-cupao'             => 'Editar Cupão',
            'ver-cupao'                => 'Ver Cupão',
            'eliminar-cupao'             => 'Eliminar Cupao',
            'gerir-promocoes'          => 'Gerir Promoções',

            'criar-utilizador'         => 'Criar Utilizador',
            'editar-utilizador'        => 'Editar Utilizador',
            'ver-utilizador'           => 'Ver Utilizador',
            'eliminar-utilizador'        => 'Eliminar Utilizador',
            'gerir-roles'              => 'Gerir Roles',
            'gerir-permissoes'         => 'Gerir Permissões',

            'ver-relatorios-de-vendas' => 'Ver Relatórios de Vendas',
            'ver-relatorios-financeiros' => 'Ver Relatórios Financeiros',
            'ver-relatorios-de-trafego' => 'Ver Relatórios de Tráfego',

            'editar-configuracoes-gerais' => 'Editar Configurações Gerais',
            'editar-configuracoes-de-pagamento' => 'Editar Configurações de Pagamento',
            'gerir-categorias'         => 'Gerir Categorias',

            'ver-relatorios-de-marketing' => 'Ver Relatórios de Marketing',
            'gerir-campanhas-de-marketing' => 'Gerir Campanhas de Marketing',
            'gerir-cupoes'             => 'Gerir Cupões',

            'ver-dados-de-afiliado'    => 'Ver Dados de Afiliado',
            'gerir-descontos-de-afiliados' => 'Gerir Descontos de Afiliados',
        ];

        return $map[$permission] ?? ucfirst(str_replace('-', ' ', $permission)); // Fallback se faltar no mapa
    }

    public function scopeFilterByRole($query, $role)
    {
        if ($role) {
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role); // Filtro pelo papel (role)
            });
        }
        return $query;
    }

    public function scopeFilterByStatus($query, $statusId)
    {
        if ($statusId !== null) {
            return $query->where('status_id', $statusId);
        }
        return $query;
    }


    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%'); // Pesquisa por nome ou email
            });
        }
        return $query;
    }


    public function status()
    {
        return $this->belongsTo(Status::class);
    }


}
