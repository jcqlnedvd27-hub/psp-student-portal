<?php

require_once __DIR__ . '/../models/Admin.php';

class AdminController
{
    /* =========================
       CHECK ADMIN LOGIN
    ========================= */

    private function requireAdmin(): void
    {
        if (empty($_SESSION['admin_id'])) {
            redirect('index.php?action=login');
        }
    }


    /* =========================
       ADMIN PROFILE PAGE
    ========================= */

    public function profile(): void
    {
        $this->requireAdmin();

        $adminModel = new Admin();
        $admin = $adminModel->findById((int) $_SESSION['admin_id']);

        if (!$admin) {
            setFlash('danger', 'Administrator account not found.');
            redirect('index.php?action=students');
        }

        $role = 'Administrator';
        $status = 'Active';

        require __DIR__ . '/../views/admin_profile.php';
    }


    /* =========================
       UPDATE ADMIN PROFILE
    ========================= */

    public function updateProfile(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=admin_profile');
        }

        verifyCsrf();

        $adminId = (int) $_SESSION['admin_id'];
        $adminName = trim($_POST['admin_name'] ?? '');

        /* VALIDATION */

        if ($adminName === '') {
            setFlash('danger', 'Please enter your name.');
            redirect('index.php?action=admin_profile');
        }

        if (preg_match('/\d/', $adminName)) {
            setFlash('danger', 'Name must not contain numbers.');
            redirect('index.php?action=admin_profile');
        }

        if (mb_strlen($adminName) < 3) {
            setFlash('danger', 'Name must be at least 3 characters.');
            redirect('index.php?action=admin_profile');
        }

        if (mb_strlen($adminName) > 100) {
            setFlash('danger', 'Name cannot exceed 100 characters.');
            redirect('index.php?action=admin_profile');
        }

        /* UPDATE */

        $adminModel = new Admin();

        if ($adminModel->updateName($adminId, $adminName)) {
            $_SESSION['admin_name'] = $adminName;
            setFlash('success', 'Profile updated successfully.');
        } else {
            setFlash('danger', 'Unable to update profile.');
        }

        redirect('index.php?action=admin_profile');
    }


    /* =========================
       UPLOAD PROFILE PICTURE
    ========================= */

    public function uploadProfilePicture(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=admin_profile');
        }

        verifyCsrf();

        $adminId = (int) $_SESSION['admin_id'];

        /* CHECK FILE */

        if (!isset($_FILES['profile_picture'])) {
            setFlash('danger', 'Please select a profile picture.');
            redirect('index.php?action=admin_profile');
        }

        $file = $_FILES['profile_picture'];

        // Give a clear message for each upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = match ($file['error']) {
                UPLOAD_ERR_NO_FILE => 'Please select a profile picture.',
                UPLOAD_ERR_INI_SIZE,
                UPLOAD_ERR_FORM_SIZE => 'Profile picture must not exceed 2 MB.',
                default => 'Upload failed. Please try again.',
            };

            setFlash('danger', $message);
            redirect('index.php?action=admin_profile');
        }

        /* MAX SIZE 2MB */

        $maxSize = 2 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            setFlash('danger', 'Profile picture must not exceed 2 MB.');
            redirect('index.php?action=admin_profile');
        }

        /* EXTENSION */

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($extension, $allowedExtensions, true)) {
            setFlash('danger', 'Only JPG, JPEG and PNG images are allowed.');
            redirect('index.php?action=admin_profile');
        }

        /* MIME TYPE (must also match the extension) */

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
            redirect('index.php?action=admin_profile');
        }

        /* VERIFY REAL IMAGE */

        if (@getimagesize($file['tmp_name']) === false) {
            setFlash('danger', 'Uploaded file is not a valid image.');
            redirect('index.php?action=admin_profile');
        }

        /* UPLOAD DIRECTORY */

        $uploadDirectory = __DIR__ . '/../uploads/profile/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        /* UNIQUE FILE NAME (uniqid, as required by the assignment) */

        $newFilename = uniqid('admin_' . $adminId . '_', true) . '.' . $extension;
        $destination = $uploadDirectory . $newFilename;

        /* MOVE FILE */

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            setFlash('danger', 'Failed to upload profile picture.');
            redirect('index.php?action=admin_profile');
        }

        /* GET OLD PICTURE */

        $adminModel = new Admin();
        $oldAdmin = $adminModel->findById($adminId);
        $oldPicture = $oldAdmin['profile_picture'] ?? null;

        /* SAVE NEW PICTURE */

        if (!$adminModel->updateProfilePicture($adminId, $newFilename)) {
            if (file_exists($destination)) {
                unlink($destination);
            }

            setFlash('danger', 'Unable to save profile picture.');
            redirect('index.php?action=admin_profile');
        }

        /* DELETE OLD PICTURE */

        if (!empty($oldPicture)) {
            $oldFile = $uploadDirectory . basename($oldPicture);

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        setFlash('success', 'Profile picture updated successfully.');
        redirect('index.php?action=admin_profile');
    }


    /* =========================
       CHANGE ADMIN PASSWORD
    ========================= */

    public function changePassword(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=admin_profile');
        }

        verifyCsrf();

        $adminId = (int) $_SESSION['admin_id'];

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        /* EMPTY */

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            setFlash('danger', 'Please fill in all password fields.');
            redirect('index.php?action=admin_profile');
        }

        /* GET ADMIN */

        $adminModel = new Admin();
        $admin = $adminModel->findById($adminId);

        if (!$admin) {
            setFlash('danger', 'Administrator account not found.');
            redirect('index.php?action=admin_profile');
        }

        /* CURRENT PASSWORD */

        if (!password_verify($currentPassword, $admin['password'])) {
            setFlash('danger', 'Current password is incorrect.');
            redirect('index.php?action=admin_profile');
        }

        /* STRENGTH (same rule as the rest of the app: config.php) */

        $strengthError = validatePasswordStrength($newPassword);

        if ($strengthError) {
            setFlash('danger', $strengthError);
            redirect('index.php?action=admin_profile');
        }

        /* NOT THE SAME AS CURRENT */

        if (password_verify($newPassword, $admin['password'])) {
            setFlash('danger', 'New password must be different from the current password.');
            redirect('index.php?action=admin_profile');
        }

        /* CONFIRM PASSWORD */

        if ($newPassword !== $confirmPassword) {
            setFlash('danger', 'New password and confirmation password do not match.');
            redirect('index.php?action=admin_profile');
        }

        /* HASH + UPDATE */

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($adminModel->updatePassword($adminId, $hashedPassword)) {
            session_regenerate_id(true);

            setFlash('success', 'Password changed successfully.');
        } else {
            setFlash('danger', 'Unable to change password.');
        }

        redirect('index.php?action=admin_profile');
    }
}
