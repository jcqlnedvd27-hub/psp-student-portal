<?php

require_once __DIR__ . '/config/config.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {

    /* =========================
       LOGIN
    ========================= */

    case 'login':
        // Already logged in? Go to the right page instead of the login form
        if (isAdmin()) {
            redirect('index.php?action=students');
        }
        if (isLoggedIn()) {
            redirect('index.php?action=profile');
        }
        require_once __DIR__ . '/views/login.php';
        break;

    case 'doLogin':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;


    /* =========================
       STUDENT PROFILE
    ========================= */

    case 'profile':
        require_once __DIR__ . '/controllers/ProfileController.php';
        (new ProfileController())->profile();
        break;

    case 'uploadProfilePicture':
        require_once __DIR__ . '/controllers/ProfileController.php';
        (new ProfileController())->uploadProfilePicture();
        break;

    case 'changePassword':
        require_once __DIR__ . '/controllers/ProfileController.php';
        (new ProfileController())->changePassword();
        break;


    /* =========================
       ADMIN PROFILE
    ========================= */

    case 'admin_profile':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->profile();
        break;

    case 'updateAdminProfile':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->updateProfile();
        break;

    case 'uploadAdminProfilePicture':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->uploadProfilePicture();
        break;

    case 'changeAdminPassword':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->changePassword();
        break;


    /* =========================
       STUDENT MANAGEMENT
    ========================= */

    case 'students':
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->index();
        break;

    case 'create':
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->create();
        break;

    case 'store':
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->store();
        break;

    case 'edit':
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->edit();
        break;

    case 'update':
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->update();
        break;

    case 'delete':
        require_once __DIR__ . '/controllers/StudentController.php';
        (new StudentController())->delete();
        break;


    /* =========================
       DEFAULT
    ========================= */

    default:
        redirect('index.php?action=login');
}