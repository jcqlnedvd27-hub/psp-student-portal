<?php

require_once __DIR__ . '/../config/database.php';

class Student
{
    private PDO $db;

    // Real table name in the database ("students" or "student")
    private string $table;

    // Column names that exist in that table
    private array $columns = [];

    public function __construct()
    {
        $this->db = (new Database())->connect();

        // Use "students" if it exists, otherwise "student"
        $this->table = $this->tableExists('students') ? 'students' : 'student';

        $stmt = $this->db->query("SHOW COLUMNS FROM `{$this->table}`");
        $this->columns = array_column($stmt->fetchAll(), 'Field');
    }

    // Check if a table exists (works on MySQL and MariaDB)
    private function tableExists(string $name): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?"
        );
        $stmt->execute([$name]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Check if a column exists in the student table
    private function hasColumn(string $column): bool
    {
        return in_array($column, $this->columns, true);
    }


    /* =========================
       GET ALL STUDENTS
    ========================= */

    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM `{$this->table}` ORDER BY student_id ASC"
        );

        $rows = $stmt->fetchAll();

        // Never send password hashes to the views
        foreach ($rows as &$row) {
            unset($row['password']);
        }

        return $rows;
    }


    /* =========================
       FIND STUDENT BY ID (no password — for display)
    ========================= */

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE student_id = ?"
        );
        $stmt->execute([$id]);

        $student = $stmt->fetch();

        if (!$student) {
            return null;
        }

        unset($student['password']);

        return $student;
    }


    /* =========================
       FIND STUDENT BY IC (includes password hash — for login)
    ========================= */

    public function findByIC(string $ic): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE student_ic = ?"
        );
        $stmt->execute([$ic]);

        return $stmt->fetch() ?: null;
    }


    /* =========================
       CHECK DUPLICATE IC ($excludeId = student being edited)
    ========================= */

    public function icExists(string $ic, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}`
                 WHERE student_ic = ? AND student_id <> ?"
            );
            $stmt->execute([$ic, $excludeId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM `{$this->table}` WHERE student_ic = ?"
            );
            $stmt->execute([$ic]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }


    /* =========================
       CREATE STUDENT (password is hashed here)
    ========================= */

    public function create(
        string $name,
        string $ic,
        string $program,
        float $marks,
        string $password,
        int $adminId
    ): bool
    {
        $data = [
            'student_name' => $name,
            'student_ic'   => $ic,
            'program'      => $program,
            'marks'        => $marks,
            'password'     => password_hash($password, PASSWORD_DEFAULT),
        ];

        // Save the admin who created the student, if the table has a column for it
        foreach (['created_by', 'admin_id'] as $adminColumn) {
            if ($this->hasColumn($adminColumn)) {
                $data[$adminColumn] = $adminId;
                break;
            }
        }

        $fields       = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->table}` ($fields) VALUES ($placeholders)"
        );

        return $stmt->execute(array_values($data));
    }


    /* =========================
       UPDATE STUDENT
    ========================= */

    public function update(int $id, string $name, string $ic, string $program, float $marks): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}`
             SET student_name = ?, student_ic = ?, program = ?, marks = ?
             WHERE student_id = ?"
        );

        return $stmt->execute([$name, $ic, $program, $marks, $id]);
    }


    /* =========================
       DELETE STUDENT
    ========================= */

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM `{$this->table}` WHERE student_id = ?"
        );

        return $stmt->execute([$id]);
    }


    /* =========================
       UPDATE PROFILE PICTURE
       Stores the uploaded filename for the logged-in student (Part 1.1)
    ========================= */

    public function updateProfilePicture(int $studentId, string $filename): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET profile_picture = ? WHERE student_id = ?"
        );

        return $stmt->execute([$filename, $studentId]);
    }


    /* =========================
       UPDATE PASSWORD (expects an already-hashed password)
    ========================= */

    public function updatePassword(int $studentId, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET password = ? WHERE student_id = ?"
        );

        return $stmt->execute([$hashedPassword, $studentId]);
    }
}
