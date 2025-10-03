<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MongoDB Configuration
    |--------------------------------------------------------------------------
    */

    'mongodb' => [
        'driver' => 'mongodb',
        'host' => env('MONGODB_HOST', '127.0.0.1'),
        'port' => env('MONGODB_PORT', 27017),
        'database' => env('MONGODB_DATABASE', 'apparel_store'),
        'username' => env('MONGODB_USERNAME'),
        'password' => env('MONGODB_PASSWORD'),
        'options' => [
            'ssl' => env('MONGODB_SSL', false),
            'replicaSet' => env('MONGODB_REPLICA_SET'),
            'authSource' => env('MONGODB_AUTH_SOURCE', 'admin'),
        ],
        
        // Replica Set Configuration for High Availability
        'replica_set' => [
            'primary' => env('MONGODB_PRIMARY', '127.0.0.1:27017'),
            'secondary_1' => env('MONGODB_SECONDARY_1', '127.0.0.1:27018'),
            'secondary_2' => env('MONGODB_SECONDARY_2', '127.0.0.1:27019'),
            'arbiter' => env('MONGODB_ARBITER', '127.0.0.1:27020'),
        ],

        // Sharding Configuration for Horizontal Scaling
        'sharding' => [
            'config_servers' => [
                env('MONGODB_CONFIG_1', '127.0.0.1:27021'),
                env('MONGODB_CONFIG_2', '127.0.0.1:27022'),
                env('MONGODB_CONFIG_3', '127.0.0.1:27023'),
            ],
            'mongos_routers' => [
                env('MONGODB_MONGOS_1', '127.0.0.1:27024'),
                env('MONGODB_MONGOS_2', '127.0.0.1:27025'),
            ],
            'shard_servers' => [
                'shard1' => ['127.0.0.1:27026', '127.0.0.1:27027'],
                'shard2' => ['127.0.0.1:27028', '127.0.0.1:27029'],
            ],
        ],
    ],
];