<?php

return [
    'schema' => [
        'register' => base_path('graphql/schema.graphql'),
    ],

    'route' => [
        'uri' => '/graphql',
        'name' => 'graphql',
        'middleware' => [],
    ],

    'guards' => null,

    'namespaces' => [
        'models' => ['App\\Models'],
        'queries' => ['App\\GraphQL\\Queries'],
        'mutations' => ['App\\GraphQL\\Mutations'],
        'subscriptions' => ['App\\GraphQL\\Subscriptions'],
        'interfaces' => ['App\\GraphQL\\Interfaces'],
        'unions' => ['App\\GraphQL\\Unions'],
        'scalars' => ['App\\GraphQL\\Scalars'],
        'directives' => ['App\\GraphQL\\Directives'],
        'validators' => ['App\\GraphQL\\Validators'],
    ],

    'pagination_amount_argument' => 'first',
    'orderBy' => 'orderBy',
];
