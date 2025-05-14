<?php

namespace Src\Model;

use Core\Database;

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email)
    {
        return $this->db->table($this->table)
            ->select(['*'])
            ->where('email', '=', $email)
            ->first();
    }

    public function findById(int $id)
    {
        return $this->db->table($this->table)
            ->select(['*'])
            ->where('id', '=', $id)
            ->first();
    }

    public function create(array $data): int
    {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->db->table($this->table)->insert($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->table($this->table)
            ->where('id', '=', $id)
            ->update($data) > 0;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
