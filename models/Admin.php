<?php

require_once __DIR__ . '/../config/database.php';

class Admin
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }


    /* =========================
       FIND ADMIN BY IC
    ========================= */

    public function findByIC(string $ic): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
                admin_id,
                admin_name,
                admin_ic,
                password,
                profile_picture,
                created_at
             FROM admin
             WHERE admin_ic = ?"
        );

        $stmt->execute([$ic]);

        return $stmt->fetch() ?: null;
    }


    /* =========================
       FIND ADMIN BY ID
    ========================= */

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
                admin_id,
                admin_name,
                admin_ic,
                password,
                profile_picture,
                created_at
             FROM admin
             WHERE admin_id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }


    /* =========================
       UPDATE ADMIN NAME
    ========================= */

    public function updateName(
        int $adminId,
        string $adminName
    ): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE admin
             SET admin_name = ?
             WHERE admin_id = ?"
        );

        return $stmt->execute([
            $adminName,
            $adminId
        ]);
    }


    /* =========================
       UPDATE PASSWORD
    ========================= */

    public function updatePassword(
        int $adminId,
        string $hashedPassword
    ): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE admin
             SET password = ?
             WHERE admin_id = ?"
        );

        return $stmt->execute([
            $hashedPassword,
            $adminId
        ]);
    }


    /* =========================
       UPDATE PROFILE PICTURE
    ========================= */

    public function updateProfilePicture(
        int $adminId,
        string $filename
    ): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE admin
             SET profile_picture = ?
             WHERE admin_id = ?"
        );

        return $stmt->execute([
            $filename,
            $adminId
        ]);
    }
}