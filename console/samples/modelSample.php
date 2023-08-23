<?php

namespace Model;

defined("ROOTPATH") or exit('Доступ запрещен!');

class {CLASSNAME}
{
    use Model;

    protected string $table = '{table}';
    protected string $primaryKey = 'id';
    protected string $loginUniqueColumn = 'email';

    protected array $allowedColumns = [
        'email',
        'username',
        'password'
    ];

    protected array $validationRules = [
        'email' => [
            'email',
            'unique',
            'required',
        ],
        'username' => [
            'alpha_space',
            'required',
        ],
        'password' => [
            'longer_than_8_chars',
            'required',
        ]
    ];
}
