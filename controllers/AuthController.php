<?php

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Admin.php';

class AuthController
{
    /**
     * Handle user login
     */
    public function login(): void
    {
        // Only allow POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=login');
        }

        // Get IC and password from form
        $ic = trim($_POST['ic'] ?? '');
        $password = $_POST['password'] ?? '';

        // Check IC field
        if ($ic === '') {
            setFlash('danger', 'Please enter your IC number.');
            redirect('index.php?action=login');
        }

        // Check IC format
        if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $ic)) {
            setFlash('danger', 'Invalid IC number format. Use format 010101-01-0101.');
            redirect('index.php?action=login');
        }

        // Check password field
        if ($password === '') {
            setFlash('danger', 'Please enter your password.');
            redirect('index.php?action=login');
        }

        // Check minimum password length
        if (strlen($password) < 8) {
            setFlash('danger', 'Password must be at least 8 characters.');
            redirect('index.php?action=login');
        }


        /*
         * ==========================================
         * ADMIN LOGIN
         * ==========================================
         */

        $adminModel = new Admin();
        $admin = $adminModel->findByIC($ic);

        if ($admin && password_verify($password, $admin['password'])) {

            // New session ID after login (prevents session fixation)
            session_regenerate_id(true);

            // Remove any student session data
            unset($_SESSION['student_id'], $_SESSION['student_name']);

            // Store only what the app needs (never the password or its hash)
            $_SESSION['admin_id']   = (int) $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['admin_name'] ?? 'PSP Administrator';

            redirect('index.php?action=students');
        }


        /*
         * ==========================================
         * STUDENT LOGIN
         * ==========================================
         */

        $studentModel = new Student();
        $student = $studentModel->findByIC($ic);

        if ($student && password_verify($password, $student['password'])) {

            // New session ID after login (prevents session fixation)
            session_regenerate_id(true);

            // Remove any admin session data
            unset($_SESSION['admin_id'], $_SESSION['admin_name']);

            // Store student information
            $_SESSION['student_id']   = (int) $student['student_id'];
            $_SESSION['student_name'] = $student['student_name'];

            redirect('index.php?action=profile');
        }


        /*
         * One generic message for both "IC not found" and "wrong password",
         * so attackers cannot find out which IC numbers are registered.
         */
        setFlash('danger', 'Invalid IC number or password.');
        redirect('index.php?action=login');
    }


    /**
     * Handle user logout
     */
    public function logout(): void
    {
        // Clear all session data
        $_SESSION = [];

        // Remove session cookie
        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        // Return to login page
        redirect('index.php?action=login');
    }
}
