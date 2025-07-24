<?php

return [

    // Dashboard
    'backoffice.dashboard' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Dashboard', 'route' => 'backoffice.dashboard', 'icon' => 'bi-speedometer2'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-list'],
    ],

    // Users
    'users.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Utilizadores', 'route' => 'users.index', 'icon' => 'bi-people'],
        ['label' => 'Ver Todos', 'route' => null, 'icon' => 'bi-list'],
    ],
    'users.user.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Utilizadores', 'route' => 'users.index', 'icon' => 'bi-people'],
        ['label' => 'Criar Novo', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'users.user.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Utilizadores', 'route' => 'users.index', 'icon' => 'bi-people'],
        ['label' => 'Editar Utilizador', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Profile
    'profiles.profile.show' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Perfil', 'route' => 'profiles.profile.show', 'icon' => 'bi-person-circle', 'params' => ['user']],
        ['label' => 'Ver Perfil', 'route' => null, 'icon' => 'bi-eye'],
    ],
    'profiles.profile.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Perfil', 'route' => 'profiles.profile.show', 'icon' => 'bi-person-circle', 'params' => ['user']],
        ['label' => 'Editar Perfil', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Bonuses
    'bonuses.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Bonificações', 'route' => 'bonuses.index', 'icon' => 'bi-percent'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'bonuses.bonus.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Bonificações', 'route' => 'bonuses.index', 'icon' => 'bi-percent'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'bonuses.bonus.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Bonificações', 'route' => 'bonuses.index', 'icon' => 'bi-percent'],
        ['label' => 'Editar Bonificação', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Orders (Pedidos a Fornecedor)
    'orders.dashboard' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-speedometer2'],
    ],

    'orders.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Ver Todos', 'route' => null, 'icon' => 'bi-list'],
    ],

    'orders.order.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Criar Novo', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],

    'orders.order.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Editar Pedido', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Listagem dos produtos (associado a pedidos)
    'lists.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Listagens', 'route' => null, 'icon' => 'bi-journal-text'],
    ],

    // Ações específicas de pedidos
    'orders.order.createEmpty' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Criar Pedido Vazio', 'route' => null, 'icon' => 'bi-file-earmark-plus'],
    ],

    'orders.order.addItems' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Adicionar Itens', 'route' => null, 'icon' => 'bi-cart-plus'],
    ],

    'orders.order.cartItemCount' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Carrinho', 'route' => null, 'icon' => 'bi-basket'],
    ],

    'orders.order.order_item.destroy' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Remover Item', 'route' => null, 'icon' => 'bi-trash'],
    ],

    'orders.order.store' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Guardar Pedido', 'route' => null, 'icon' => 'bi-save'],
    ],

    'orders.order.update' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Atualizar Pedido', 'route' => null, 'icon' => 'bi-arrow-repeat'],
    ],

    'orders.order.destroy' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos a Fornecedor', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Eliminar Pedido', 'route' => null, 'icon' => 'bi-trash'],
    ],


];
