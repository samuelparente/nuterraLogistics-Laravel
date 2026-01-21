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
    // Validades / Lotes
    'batchesexpirydates.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Validades', 'route' => null, 'icon' => 'bi-box-seam'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-calendar-x'],
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
    // Entrada de Mercadorias
    'receivings.dashboard' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-speedometer2'],
    ],

    'receivings.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Pendentes', 'route' => null, 'icon' => 'bi-list'],
    ],

    'receivings.pending' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Em Curso', 'route' => null, 'icon' => 'bi-hourglass-split'],
    ],

    'receivings.suppliers' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Fornecedores', 'route' => null, 'icon' => 'bi-people'],
    ],

    'receivings.form' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Listagem de Produtos', 'route' => null, 'icon' => 'bi-ui-checks'],
    ],

    'receivings.items.single' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Produto Extra', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],

    'receivings.items.singleScanner' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Entrada com Scanner', 'route' => null, 'icon' => 'bi-upc-scan'],
    ],

    'receivings.history' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Histórico', 'route' => null, 'icon' => 'bi-clock-history'],
    ],

    'receivings.details' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Histórico', 'route' => 'receivings.history', 'icon' => 'bi-clock-history'],
        ['label' => 'Detalhes', 'route' => null, 'icon' => 'bi-eye'],
    ],

    'receivings.divergences.brand' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Entrada de Mercadorias', 'route' => 'receivings.dashboard', 'icon' => 'bi-box-arrow-in-down'],
        ['label' => 'Histórico', 'route' => 'receivings.history', 'icon' => 'bi-clock-history'],
        ['label' => 'Divergências', 'route' => null, 'icon' => 'bi-exclamation-triangle'],
    ],
    'settings.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Configurações', 'route' => 'settings.edit', 'icon' => 'bi-gear'],
    ],


];
