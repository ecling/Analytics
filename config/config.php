<?php

use Phalcon\Config;

return new Config(
    [
        'database'    => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'name'     => 'test',
        ],
        'application' => [
            'modelsDir' => __DIR__ . '/../apps/models/',
            'baseUri'   => '/micro-factory-default/',
        ],
        'models'      => [
            'metadata' => [
                'adapter' => 'Memory'
            ]
        ],
        'api' => [
            'api_user' => 'analytics',
            'api_key' => 'ssI3wz%CZb5ZHfJ7kk*h3anp7Luu1UCz'
        ]
    ]
);
