<?php

// Load Student model
require_once __DIR__ . '/../models/Student.php';

class StudentController {

    // Validate student information
    private function validate(
        string $name,
        string $ic,
        string $program,
        string $marks
    ): ?string {

        // Check student name
        if ($name === '') {
            return 'Student name is required.';
        }

        // Check that name does not contain numbers
        if (preg_match('/\d/', $name)) {
            return 'Student name must not contain numbers.';
        }

        // Check name length
        if (mb_strlen($name) > 100) {
            return 'Student name cannot exceed 100 characters.';
        }

        // Check NRIC
        if ($ic === '') {
            return 'NRIC is required.';
        }

        // Check IC format
        if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $ic)) {
            return 'IC format must be XXXXXX-XX-XXXX.';
        }

        // Check program
        if ($program === '') {
            return 'Program is required.';
        }

        // Check marks
        if ($marks === '') {
            return 'Marks is required.';
        }

        // Check marks range
        if (!is_numeric($marks) || (float)$marks < 0 || (float)$marks > 100) {
            return 'Marks must be between 0 and 100.';
        }

        return null;
    }

    // Keep only safe form values for re-filling the form
    // (never store the password or CSRF token in the session)
    private function rememberInput(): void {
        $old = $_POST;
        unset($old['password'], $old['csrf_token']);
        setOldInput($old);
    }

    // Only accept POST for actions that change data
    private function requirePost(string $fallback): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect($fallback);
        }
    }

    // Display all students
    public function index(): void {
        requireAdmin();

        $studentModel = new Student();
        $students = $studentModel->getAll();

        require __DIR__ . '/../views/student_list.php';
    }

    // Display create student form
    public function create(): void {
        requireAdmin();

        $student = [
            'student_id' => '',
            'student_name' => '',
            'student_ic' => '',
            'program' => '',
            'marks' => ''
        ];

        // Re-fill with what the user last typed, if a previous submit failed
        $old = getOldInput();
        if ($old) {
            $student = array_merge($student, $old);
        }

        $mode = 'create';

        require __DIR__ . '/../views/form.php';
    }

    // Store a new student
    public function store(): void {
        requireAdmin();
        $this->requirePost('index.php?action=create');
        verifyCsrf();

        // Get student data from form
        $name = trim($_POST['student_name'] ?? '');
        $ic = trim($_POST['student_ic'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $marks = trim($_POST['marks'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate student data
        $error = $this->validate($name, $ic, $program, $marks);

        // Validate password
        if (!$error && $password === '') {
            $error = 'Password is required.';
        }

        if (!$error) {
            $error = validatePasswordStrength($password);
        }

        if ($error) {
            $this->rememberInput();
            setFlash('danger', $error);
            redirect('index.php?action=create');
        }

        $studentModel = new Student();

        // Check for duplicate IC
        if ($studentModel->icExists($ic)) {
            $this->rememberInput();
            setFlash('danger', 'This IC already exists.');
            redirect('index.php?action=create');
        }

        // Create student record
        $studentModel->create(
            $name,
            $ic,
            $program,
            (float)$marks,
            $password,
            (int)$_SESSION['admin_id']
        );

        setFlash('success', 'Student created successfully.');
        redirect('index.php?action=students');
    }

    // Display edit student form
    public function edit(): void {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        $studentModel = new Student();
        $student = $studentModel->findById($id);

        if (!$student) {
            setFlash('danger', 'Student not found.');
            redirect('index.php?action=students');
        }

        // Re-fill with what the user last typed, if a previous submit failed
        $old = getOldInput();
        if ($old) {
            $student = array_merge($student, $old);
        }

        $mode = 'edit';

        require __DIR__ . '/../views/form.php';
    }

    // Update student information
    public function update(): void {
        requireAdmin();
        $this->requirePost('index.php?action=students');
        verifyCsrf();

        // Get updated student data
        $id = (int)($_POST['student_id'] ?? 0);
        $name = trim($_POST['student_name'] ?? '');
        $ic = trim($_POST['student_ic'] ?? '');
        $program = trim($_POST['program'] ?? '');
        $marks = trim($_POST['marks'] ?? '');

        $studentModel = new Student();

        // Make sure the student exists
        if (!$studentModel->findById($id)) {
            setFlash('danger', 'Student not found.');
            redirect('index.php?action=students');
        }

        // Validate updated data
        $error = $this->validate($name, $ic, $program, $marks);

        if ($error) {
            $this->rememberInput();
            setFlash('danger', $error);
            redirect('index.php?action=edit&id=' . $id);
        }

        // Check for duplicate IC
        if ($studentModel->icExists($ic, $id)) {
            $this->rememberInput();
            setFlash('danger', 'This IC already exists.');
            redirect('index.php?action=edit&id=' . $id);
        }

        // Update student record
        $studentModel->update($id, $name, $ic, $program, (float)$marks);

        setFlash('success', 'Student updated successfully.');
        redirect('index.php?action=students');
    }

    // Delete student record
    public function delete(): void {
        requireAdmin();
        $this->requirePost('index.php?action=students');
        verifyCsrf();

        $id = (int)($_POST['student_id'] ?? 0);

        $studentModel = new Student();
        $student = $studentModel->findById($id);

        if (!$student) {
            setFlash('danger', 'Student not found.');
            redirect('index.php?action=students');
        }

        // Delete student from database
        $studentModel->delete($id);

        // Remove their profile picture file too
        if (!empty($student['profile_picture'])) {
            $file = __DIR__ . '/../uploads/profile/' . basename($student['profile_picture']);

            if (file_exists($file)) {
                unlink($file);
            }
        }

        setFlash('success', 'Student deleted successfully.');
        redirect('index.php?action=students');
    }
}
