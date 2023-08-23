<?php

namespace Model;

defined("ROOTPATH") or exit('Доступ запрещен!');

class User
{
    use Model;

    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected string $loginUniqueColumn = 'email';

    protected array $allowedColumns = [
        'email',
        'username',
        'password'
    ];

    protected array $onInsertValidationRules = [
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

    protected array $onUpdateValidationRules = [
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

    public function signup($data): void
    {

        if ($this->validate($data)) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['createdAt'] = date("Y-m-d H:i:s");

            $this->create($data);

            redirect('login');
        }
    }

    public function login($data): void
    {
        $row = $this->getOne([$this->loginUniqueColumn => $data[$this->loginUniqueColumn]]);

        if (!empty($row)) {
            if (password_verify($data['password'], $row->password)) {

                $ses = new Session();
                $ses->auth($row);
                redirect('home');
            } else $this->errors[$this->loginUniqueColumn] = "Не правильный {$this->loginUniqueColumn} или пароль";
        } else $this->errors[$this->loginUniqueColumn] = "Не правильный {$this->loginUniqueColumn} или пароль";
    }
}
