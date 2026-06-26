<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $table      = 'User';
    protected string $primaryKey = 'User_ID';

    /** Find a user by email */
    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM User WHERE Email = ? LIMIT 1",
            [$email]
        );
    }

    /** Verify password against stored hash */
    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    /** Hash a plain-text password */
    public function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    /** Register a new user and return the new User_ID */
    public function register(array $data): int
    {
        $data['Password'] = $this->hashPassword($data['Password']);
        $data['Created_At'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    /** Get all phone numbers for a user */
    public function getPhones(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT UPhone FROM User_Phones WHERE User_ID = ?",
            [$userId]
        );
    }

    /** Add a phone number for a user */
    public function addPhone(int $userId, string $phone): void
    {
        $this->db->query(
            "INSERT IGNORE INTO User_Phones (User_ID, UPhone) VALUES (?, ?)",
            [$userId, $phone]
        );
    }

    /** Remove a phone number */
    public function removePhone(int $userId, string $phone): void
    {
        $this->db->query(
            "DELETE FROM User_Phones WHERE User_ID = ? AND UPhone = ?",
            [$userId, $phone]
        );
    }

    /** Update profile image path */
    public function updateProfileImage(int $userId, string $imagePath): void
    {
        $this->update(['Profile_Img' => $imagePath], 'User_ID = ?', [$userId]);
    }
}
