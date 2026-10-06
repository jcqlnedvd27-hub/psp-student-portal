<?php

// Get flash message
$flash = getFlash();

// Normalize flash type
if ($flash) {

    $typeMap = [
        'error'  => 'danger',
        'ok'     => 'success',
        'notice' => 'info',
    ];

    $flash['type'] = $typeMap[$flash['type']] ?? $flash['type'];

    $allowedTypes = [
        'success',
        'danger',
        'warning',
        'info',
        'primary',
        'secondary',
        'dark',
        'light'
    ];

    if (!in_array($flash['type'], $allowedTypes, true)) {
        $flash['type'] = 'info';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Basic page settings -->
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <!-- Page title -->
    <title>
        Student Management - <?= e(APP_NAME) ?>
    </title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <!-- Custom CSS -->
    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>


<body class="admin-page">


<!-- =====================================================
     NAVIGATION BAR
===================================================== -->

<nav class="navbar navbar-expand-lg navbar-dark portal-nav profile-navbar">

    <div class="container profile-navbar-container admin-navbar-container">


        <!-- =================================================
             PORTAL LOGO AND NAME
        ================================================== -->

        <a
            class="navbar-brand profile-brand"
            href="index.php?action=students"
        >

            <span class="profile-logo">

                <i class="bi bi-mortarboard-fill"></i>

            </span>


            <span class="profile-brand-content">

                <span class="profile-brand-title">
                    PSP Student Portal
                </span>

                <span class="profile-brand-subtitle">
                    Learn • Grow • Succeed
                </span>

            </span>

        </a>



        <!-- =================================================
             ADMIN PROFILE DROPDOWN
        ================================================== -->

        <div class="admin-navbar-right">

            <div class="admin-dropdown">

                <!-- Admin dropdown button -->
                <button
                    type="button"
                    class="admin-profile-dropdown-btn"
                    id="adminProfileDropdownBtn"
                    aria-expanded="false"
                    title="Admin Account"
                    onclick="toggleAdminDropdown(event)"
                >

                    <i class="bi bi-person-circle"></i>

                    <span>
                        Admin: <?= e($_SESSION['admin_name']) ?>
                    </span>

                    <i class="bi bi-chevron-down admin-dropdown-arrow"></i>

                </button>


                <!-- Dropdown menu -->
                <div
                    class="admin-dropdown-menu"
                    id="adminProfileDropdownMenu"
                >

                    <!-- My Profile -->
                    <a
                        href="index.php?action=admin_profile"
                        class="admin-dropdown-item"
                    >

                        <span class="admin-dropdown-icon">
                            <i class="bi bi-person"></i>
                        </span>

                        <span>
                            <strong>My Profile</strong>
                            <small>View administrator profile</small>
                        </span>

                    </a>


                    <!-- Divider -->
                    <div class="admin-dropdown-divider"></div>


                    <!-- Logout -->
                    <a
                        href="index.php?action=logout"
                        class="admin-dropdown-item admin-dropdown-logout"
                    >

                        <span class="admin-dropdown-icon">
                            <i class="bi bi-box-arrow-right"></i>
                        </span>

                        <span>
                            <strong>Logout</strong>
                            <small>Sign out of your account</small>
                        </span>

                    </a>

                </div>

            </div>

        </div>

    </div>

</nav>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<main class="admin-main">

    <div class="admin-wrapper">


        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <div class="admin-header">

            <div>

                <h1>
                    Student Grade Management
                </h1>

                <p>
                    Create, read, update and delete student records.
                </p>

            </div>


            <!-- Add Student -->

            <a
                href="index.php?action=create"
                class="admin-add-btn"
            >

                <i class="bi bi-plus-lg"></i>

                Add Student

            </a>

        </div>



        <!-- =================================================
             FLASH MESSAGE
        ================================================== -->

        <?php if ($flash): ?>

            <div
                class="alert alert-<?= e($flash['type']) ?> admin-alert"
                role="alert"
            >

                <?php if ($flash['type'] === 'success'): ?>

                    <i class="bi bi-check-circle-fill"></i>

                <?php elseif ($flash['type'] === 'danger'): ?>

                    <i class="bi bi-exclamation-circle-fill"></i>

                <?php elseif ($flash['type'] === 'warning'): ?>

                    <i class="bi bi-exclamation-triangle-fill"></i>

                <?php else: ?>

                    <i class="bi bi-info-circle-fill"></i>

                <?php endif; ?>


                <?= e($flash['message']) ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             STUDENT TABLE CARD
        ================================================== -->

        <div class="admin-card">

            <div class="table-responsive">

                <table class="admin-table">


                    <!-- =================================================
                         TABLE HEADINGS
                    ================================================== -->

                    <thead>

                        <tr>

                            <th class="col-no">
                                No
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                NRIC
                            </th>

                            <th>
                                Program
                            </th>

                            <th class="col-marks">
                                Marks
                            </th>

                            <th class="col-action">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <!-- =================================================
                         TABLE BODY
                    ================================================== -->

                    <tbody>


                    <!-- No student records -->

                    <?php if (!$students): ?>

                        <tr>

                            <td
                                colspan="6"
                                class="admin-empty"
                            >

                                <i class="bi bi-inbox"></i>

                                <span>
                                    No student records found.
                                </span>

                            </td>

                        </tr>


                    <?php else: ?>


                        <!-- Student number -->

                        <?php $no = 1; ?>


                        <!-- Display student records -->

                        <?php foreach ($students as $item): ?>

                            <tr>


                                <!-- =====================================
                                     STUDENT NUMBER
                                ====================================== -->

                                <td class="col-no">

                                    <?= ($no++) ?>

                                </td>



                                <!-- =====================================
                                     STUDENT NAME
                                ====================================== -->

                                <td class="col-name">

                                    <?= e($item['student_name']) ?>

                                </td>



                                <!-- =====================================
                                     STUDENT NRIC
                                ====================================== -->

                                <td class="col-ic">

                                    <?= e($item['student_ic']) ?>

                                </td>



                                <!-- =====================================
                                     STUDENT PROGRAM
                                ====================================== -->

                                <td class="col-program">

                                    <?= e($item['program']) ?>

                                </td>



                                <!-- =====================================
                                     STUDENT MARKS
                                ====================================== -->

                                <td class="col-marks">

                                    <?= e($item['marks']) ?>

                                </td>



                                <!-- =====================================
                                     ACTION BUTTONS
                                ====================================== -->

                                <td class="col-action">


                                    <!-- Edit -->

                                    <a
                                        href="index.php?action=edit&id=<?= e($item['student_id']) ?>"
                                        class="admin-btn admin-btn-edit"
                                        title="Edit student"
                                    >

                                        <i class="bi bi-pencil"></i>

                                        Edit

                                    </a>



                                    <!-- Delete -->

                                    <form
                                        action="index.php?action=delete"
                                        method="POST"
                                        class="d-inline delete-form"
                                    >


                                        <!-- CSRF security token -->

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e(csrfToken()) ?>"
                                        >


                                        <!-- Student ID -->

                                        <input
                                            type="hidden"
                                            name="student_id"
                                            value="<?= e($item['student_id']) ?>"
                                        >


                                        <!-- Delete button -->

                                        <button
                                            type="submit"
                                            class="admin-btn admin-btn-delete"
                                            title="Delete student"
                                        >

                                            <i class="bi bi-trash"></i>

                                            Delete

                                        </button>


                                    </form>


                                </td>


                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>

</main>



<!-- =====================================================
     DELETE CONFIRMATION MODAL
===================================================== -->

<div
    class="modal-backdrop-custom"
    id="deleteModal"
    aria-hidden="true"
>

    <div
        class="modal-box"
        role="dialog"
        aria-modal="true"
        aria-labelledby="deleteModalTitle"
    >


        <!-- Modal Icon -->

        <div class="modal-icon">

            <i class="bi bi-exclamation-triangle-fill"></i>

        </div>



        <!-- Modal Title -->

        <h3
            class="modal-title"
            id="deleteModalTitle"
        >

            Delete Student?

        </h3>



        <!-- Modal Message -->

        <p class="modal-message">

            Are you sure you want to delete this student record?

            <strong>
                This action cannot be undone.
            </strong>

        </p>



        <!-- Modal Actions -->

        <div class="modal-actions">


            <!-- Cancel -->

            <button
                type="button"
                class="modal-btn modal-btn-cancel"
                data-modal-close
            >

                Cancel

            </button>



            <!-- Confirm Delete -->

            <button
                type="button"
                class="modal-btn modal-btn-confirm"
                id="confirmDeleteBtn"
            >

                <i class="bi bi-trash"></i>

                Delete

            </button>


        </div>

    </div>

</div>



<!-- =====================================================
     CUSTOM JAVASCRIPT
===================================================== -->

<script src="assets/script.js"></script>



</body>

</html>