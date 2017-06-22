<?php
return [

    'authentication' => [
        'jwtKey' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJPbmxpbmUgSldUIEJ1aWxkZXIiLCJpYXQiOjE0ODc3NjUzMjgsImV4cCI6MTUxOTMwMTMyOCwiYXVkIjoid3d3LmV4YW1wbGUuY29tIiwic3ViIjoianJvY2tldEBleGFtcGxlLmNvbSIsIkdpdmVuTmFtZSI6IkpvaG5ueSIsIlN1cm5hbWUiOiJSb2NrZXQiLCJFbWFpbCI6Impyb2NrZXRAZXhhbXBsZS5jb20iLCJSb2xlIjpbIk1hbmFnZXIiLCJQcm9qZWN0IEFkbWluaXN0cmF0b3IiXX0.BgA_CW589s5HUjPAMV3ZFYa-4K-SyvzqOhnxyFr5vzk',
        'algorithm' => 'HS256'
    ],
    'databases' => [
        'mysql' => [
            'database' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'dbname' => 'fred',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
    ],
    'debug' => [
        'display_errors' => false,
        'error_reporting' => 0
    ]
];
?>
