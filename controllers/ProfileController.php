<?php

require_once __DIR__ . '/../models/Student.php';

class ProfileController
{
    /**
     * Allow only logged-in students (admins go to their own dashboard)
     */
    private function requireStudent(): void
    {
        requireLogin();

        if (isAdmin()) {
            redirect('index.php?action=students');
        }
    }


    /**
     * Display student profile
     */
    public function profile(): void
    {
        $this->requireStudent();

        // Get current logged-in student ID
        $studentId = (int) $_SESSION['student_id'];

        // Get student information
        $studentModel = new Student();
        $student = $studentModel->findById($studentId);

        // Student not found
        if (!$student) {
            setFlash('danger', 'Student profile could not be found.');
            redirect('index.php?action=logout');
        }

        // Load profile view
        require __DIR__ . '/../views/profile.php';
    }


    /**
     * Upload student profile picture
     */
    public function uploadProfilePicture(): void
    {
        $this->requireStudent();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=profile');
        }

        verifyCsrf();

        if (!isset($_FILES['profile_picture'])) {
            setFlash('danger', 'Please select a profile picture.');
            redirect('index.php?action=profile');
        }

        $file = $_FILES['profile_picture'];

        // Clear message for each upload error
        // (e.g. a file over the server's upload_max_filesize arrives with size 0)
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = match ($file['error']) {
                UPLOAD_ERR_NO_FILE => 'Please select a profile picture.',
                UPLOAD_ERR_INI_SIZE,
                UPLOAD_ERR_FORM_SIZE => 'Profile picture must not exceed 2 MB.',
                default => 'Upload failed. Please try again.',
            };

            setFlash('danger', $message);
            redirect('index.php?action=profile');
        }

        // Maximum 2 MB
        $maxFileSize = 2 * 1024 * 1024;

        if ($file['size'] > $maxFileSize) {
            setFlash('danger', 'Profile picture must not exceed 2 MB.');
            redirect('index.php?action=profile');
        }

        // Allowed extensions
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($extension, $allowedExtensions, true)) {
            setFlash('danger', 'Only JPG, JPEG, and PNG files are allowed.');
            redirect('index.php?action=profile');
        }

        // Check actual MIME type (must also match the extension)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedMimeTypes = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png'  => ['png'],
        ];

        if (
            !isset($allowedMimeTypes[$mimeType]) ||
            !in_array($extension, $allowedMimeTypes[$mimeType], true)
        ) {
            setFlash('danger', 'Invalid image file type.');
            redirect('index.php?action=profile');
        }

        // Verify it is a real image
        if (@getimagesize($file['tmp_name']) === false) {
            setFlash('danger', 'The uploaded file is not a valid image.');
            redirect('index.php?action=profile');
        }

        // Current logged-in student
        $studentId = (int) $_SESSION['student_id'];

        // Upload directory
        $uploadDirectory = __DIR__ . '/../uploads/profile/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        // Secure rename with uniqid() to prevent overwriting
        $newFilename = uniqid('profile_', true) . '.' . $extension;
        $destination = $uploadDirectory . $newFilename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            setFlash('danger', 'Failed to save the uploaded profile picture.');
            redirect('index.php?action=profile');
        }

        // Remember the old picture so it can be removed afterwards
        $studentModel = new Student();
        $current = $studentModel->findById($studentId);
        $oldPicture = $current['profile_picture'] ?? null;

        // Save new filename to the database (linked to the session user)
        if (!$studentModel->updateProfilePicture($studentId, $newFilename)) {
            if (file_exists($destination)) {
                unlink($destination);
            }

            setFlash('danger', 'Failed to update profile picture information.');
            redirect('index.php?action=profile');
        }

        // Delete the old picture file
        if (!empty($oldPicture)) {
            $oldFile = $uploadDirectory . basename($oldPicture);

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        setFlash('success', 'Profile picture uploaded successfully.');
        redirect('index.php?action=profile');
    }


    /**
     * Change student password
     * Form fields: old_password, new_password, confirm_password
     */
    public function changePassword(): void
    {
        $this->requireStudent();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=profile');
        }

        verifyCsrf();

        $studentId = (int) $_SESSION['student_id'];

        $oldPassword     = $_POST['old_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // All fields required
        if ($oldPassword === '' || $newPassword === '' || $confirmPassword === '') {
            setFlash('danger', 'Please fill in all password fields.');
            redirect('index.php?action=profile');
        }

        // Get the student record (findByIC returns the password hash)
        $studentModel = new Student();
        $student = $studentModel->findById($studentId);

        if (!$student) {
            setFlash('danger', 'Student profile could not be found.');
            redirect('index.php?action=logout');
        }

        $account = $studentModel->findByIC($student['student_ic']);

        if (!$account || empty($account['password'])) {
            setFlash('danger', 'Unable to verify your account.');
            redirect('index.php?action=profile');
        }

        // Old password must be correct
        if (!password_verify($oldPassword, $account['password'])) {
            setFlash('danger', 'Old password is incorrect.');
            redirect('index.php?action=profile');
        }

        // Strength rule shared with the rest of the app (config.php)
        $strengthError = validatePasswordStrength($newPassword);

        if ($strengthError) {
            setFlash('danger', $strengthError);
            redirect('index.php?action=profile');
        }

        // Must differ from the old password
        if (password_verify($newPassword, $account['password'])) {
            setFlash('danger', 'New password must be different from the old password.');
            redirect('index.php?action=profile');
        }

        // Confirmation must match
        if ($newPassword !== $confirmPassword) {
            setFlash('danger', 'New password and confirm password do not match.');
            redirect('index.php?action=profile');
        }

        // Hash and save
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($studentModel->updatePassword($studentId, $hashedPassword)) {
            session_regenerate_id(true);
            setFlash('success', 'Password changed successfully.');
        } else {
            setFlash('danger', 'Unable to change password.');
        }

        redirect('index.php?action=profile');
    }
}
