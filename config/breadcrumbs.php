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

    // Clients
    'clients.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Clientes', 'route' => 'clients.index', 'icon' => 'bi-person-lines-fill'],
        ['label' => 'Ver Todos', 'route' => null, 'icon' => 'bi-list'],
    ],
    'clients.client.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Clientes', 'route' => 'clients.index', 'icon' => 'bi-person-lines-fill'],
        ['label' => 'Criar Novo', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'clients.client.show' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Clientes', 'route' => 'clients.index', 'icon' => 'bi-person-lines-fill'],
        ['label' => 'Ver Cliente', 'route' => null, 'icon' => 'bi-eye'],
    ],
    'clients.client.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Clientes', 'route' => 'clients.index', 'icon' => 'bi-person-lines-fill'],
        ['label' => 'Editar Cliente', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Stores
    'stores.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Lojas', 'route' => 'stores.index', 'icon' => 'bi-shop'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'stores.store.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Lojas', 'route' => 'stores.index', 'icon' => 'bi-shop'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'stores.store.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Lojas', 'route' => 'stores.index', 'icon' => 'bi-shop'],
        ['label' => 'Editar Loja', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Statistics
    'statistics.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Estatísticas', 'route' => 'statistics.index', 'icon' => 'bi-bar-chart-line'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-list'],
    ],

    // Marketing
    'marketing.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Marketing', 'route' => 'marketing.index', 'icon' => 'bi-megaphone'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-list'],
    ],

    // Taxonomies
    'taxonomies.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-list'],
    ],

    // Brands
    'brands.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Marcas', 'route' => 'brands.index', 'icon' => 'bi-award'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'brands.brand.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Marcas', 'route' => 'brands.index', 'icon' => 'bi-award'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'brands.brand.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Marcas', 'route' => 'brands.index', 'icon' => 'bi-award'],
        ['label' => 'Editar Marca', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Labels
    'labels.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Etiquetas', 'route' => 'labels.index', 'icon' => 'bi-tags'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'labels.label.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Etiquetas', 'route' => 'labels.index', 'icon' => 'bi-tags'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'labels.label.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Etiquetas', 'route' => 'labels.index', 'icon' => 'bi-tags'],
        ['label' => 'Editar Etiqueta', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Tags
    'tags.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Tags', 'route' => 'tags.index', 'icon' => 'bi-bookmark'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'tags.tag.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Tags', 'route' => 'tags.index', 'icon' => 'bi-bookmark'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'tags.tag.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Tags', 'route' => 'tags.index', 'icon' => 'bi-bookmark'],
        ['label' => 'Editar Tag', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Attributes
    'attributes.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Atributos', 'route' => 'attributes.index', 'icon' => 'bi-sliders'],
        ['label' => 'Ver Todos', 'route' => null, 'icon' => 'bi-list'],
    ],
    'attributes.attribute.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Atributos', 'route' => 'attributes.index', 'icon' => 'bi-sliders'],
        ['label' => 'Criar Novo', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'attributes.attribute.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Atributos', 'route' => 'attributes.index', 'icon' => 'bi-sliders'],
        ['label' => 'Editar Atributo', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Categories
    'categories.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Categorias', 'route' => 'categories.index', 'icon' => 'bi-grid-3x3'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'categories.tree' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Categorias', 'route' => 'categories.index', 'icon' => 'bi-grid-3x3'],
        ['label' => 'Árvore de Categorias', 'route' => null, 'icon' => 'bi-diagram-3'],
    ],
    'categories.category.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Categorias', 'route' => 'categories.index', 'icon' => 'bi-grid-3x3'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'categories.category.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Categorias', 'route' => 'categories.index', 'icon' => 'bi-grid-3x3'],
        ['label' => 'Editar Categoria', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Families
    'families.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Famílias', 'route' => 'families.index', 'icon' => 'bi-collection'],
        ['label' => 'Ver Todas', 'route' => null, 'icon' => 'bi-list'],
    ],
    'families.tree' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Famílias', 'route' => 'families.index', 'icon' => 'bi-collection'],
        ['label' => 'Árvore de Famílias', 'route' => null, 'icon' => 'bi-diagram-3'],
    ],
    'families.family.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Famílias', 'route' => 'families.index', 'icon' => 'bi-collection'],
        ['label' => 'Criar Nova', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'families.family.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Taxonomias', 'route' => 'taxonomies.index', 'icon' => 'bi-diagram-3'],
        ['label' => 'Famílias', 'route' => 'families.index', 'icon' => 'bi-collection'],
        ['label' => 'Editar Família', 'route' => null, 'icon' => 'bi-pencil'],
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

    // Settings
    'settings.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Definições', 'route' => 'settings.index', 'icon' => 'bi-gear'],
        ['label' => 'Ver Definições', 'route' => null, 'icon' => 'bi-list'],
    ],

    // Support
    'support.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Apoio ao Cliente', 'route' => 'support.index', 'icon' => 'bi-headset'],
        ['label' => 'Ver Apoio', 'route' => null, 'icon' => 'bi-list'],
    ],

    // Menus
    'menus.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Menus', 'route' => 'menus.index', 'icon' => 'bi-menu-button'],
        ['label' => 'Ver Todos', 'route' => null, 'icon' => 'bi-list'],
    ],
    'menus.menu.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Menus', 'route' => 'menus.index', 'icon' => 'bi-menu-button'],
        ['label' => 'Criar Novo', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],
    'menus.menu.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Menus', 'route' => 'menus.index', 'icon' => 'bi-menu-button'],
        ['label' => 'Editar Menu', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Menu Items
    'menus.items.editItems' => [
    ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
    ['label' => 'Menus', 'route' => 'menus.index', 'icon' => 'bi-menu-button'],
    ['label' => 'Itens do Menu', 'route' => null, 'icon' => 'bi-list', 'params' => ['menu']],
    ],
    'menus.items.item.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Menus', 'route' => 'menus.index', 'icon' => 'bi-menu-button'],
        ['label' => 'Itens do Menu', 'route' => 'menus.items.editItems', 'icon' => 'bi-list', 'params' => ['menu']],
        ['label' => 'Editar Item', 'route' => null, 'icon' => 'bi-pencil'],
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

    // Orders (Pedidos a fornecedor)
    'orders.dashboard' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Visão Geral', 'route' => null, 'icon' => 'bi-speedometer2'],
    ],

    'orders.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos', 'route' => 'orders.index', 'icon' => 'bi-truck'],
        ['label' => 'Ver Todos', 'route' => null, 'icon' => 'bi-list'],
    ],

    'orders.order.create' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos', 'route' => 'orders.index', 'icon' => 'bi-truck'],
        ['label' => 'Criar Novo', 'route' => null, 'icon' => 'bi-plus-circle'],
    ],

    'orders.order.edit' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos', 'route' => 'orders.index', 'icon' => 'bi-truck'],
        ['label' => 'Editar Pedido', 'route' => null, 'icon' => 'bi-pencil'],
    ],

    // Listagem dos produtos
    'lists.index' => [
        ['label' => 'Backoffice', 'route' => 'backoffice.dashboard', 'icon' => 'bi-house-door'],
        ['label' => 'Pedidos', 'route' => 'orders.dashboard', 'icon' => 'bi-truck'],
        ['label' => 'Listagens', 'route' => 'lists.index', 'icon' => 'bi-journal-text'],
    ],


];
