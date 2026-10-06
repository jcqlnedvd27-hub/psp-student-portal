<?php

if (!isset($admin)) {
    redirect('index.php?action=login');
}

$profilePicture = $admin['profile_picture'] ?? null;
$adminName      = $admin['admin_name'] ?? 'Administrator';
$adminIC        = $admin['admin_ic'] ?? '-';
$memberSince    = !empty($admin['created_at'])
    ? date('d M Y', strtotime($admin['created_at']))
    : '-';

$role   = $role ?? 'Administrator';
$status = $status ?? 'Active';

// Get flash message
$flash = getFlash();

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Profile - <?= e(APP_NAME) ?></title>

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
    <link rel="stylesheet" href="assets/style.css">

</head>

<body class="admin-page">


<!-- =====================================================
     NAVIGATION BAR
===================================================== -->

<nav class="navbar navbar-expand-lg navbar-dark portal-nav profile-navbar">

    <div class="container profile-navbar-container admin-navbar-container">

        <!-- Portal logo and name -->
        <a class="navbar-brand profile-brand" href="index.php?action=students">

            <span class="profile-logo">
                <i class="bi bi-mortarboard-fill"></i>
            </span>

            <span class="profile-brand-content">
                <span class="profile-brand-title">PSP Student Portal</span>
                <span class="profile-brand-subtitle">Learn • Grow • Succeed</span>
            </span>

        </a>


        <!-- Admin dropdown -->
        <div class="admin-navbar-right">

            <div class="admin-dropdown">

                <button
                    type="button"
                    class="admin-profile-dropdown-btn"
                    id="adminProfileDropdownBtn"
                    aria-expanded="false"
                    title="Admin Account"
                    onclick="toggleAdminDropdown(event)"
                >
                    <i class="bi bi-person-circle"></i>

                    <span>Admin: <?= e($_SESSION['admin_name'] ?? $adminName) ?></span>

                    <i class="bi bi-chevron-down admin-dropdown-arrow"></i>
                </button>

                <div class="admin-dropdown-menu" id="adminProfileDropdownMenu">

                    <a href="index.php?action=students" class="admin-dropdown-item">

                        <span class="admin-dropdown-icon">
                            <i class="bi bi-grid"></i>
                        </span>

                        <span>
                            <strong>Dashboard</strong>
                            <small>Back to student management</small>
                        </span>

                    </a>

                    <div class="admin-dropdown-divider"></div>

                    <a href="index.php?action=logout" class="admin-dropdown-item admin-dropdown-logout">

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

<main class="admin-profile-main">

    <div class="admin-profile-wrapper">

        <!-- Page header -->
        <div class="admin-profile-header">
            <h1>Administrator Profile</h1>
            <p>Manage your account details, profile picture and password.</p>
        </div>


        <!-- Flash message -->
        <?php if ($flash): ?>

            <div class="alert alert-<?= e($flash['type']) ?> admin-alert" role="alert">

                <?php if ($flash['type'] === 'success'): ?>
                    <i class="bi bi-check-circle-fill"></i>
                <?php else: ?>
                    <i class="bi bi-exclamation-circle-fill"></i>
                <?php endif; ?>

                <?= e($flash['message']) ?>

            </div>

        <?php endif; ?>


        <div class="admin-profile-layout">


            <!-- =================================================
                 LEFT COLUMN
            ================================================== -->

            <div>

                <!-- PROFILE PICTURE + SUMMARY -->
                <div class="admin-profile-card mb-4">

                    <div class="admin-profile-card-header">

                        <div class="admin-profile-card-icon">
                            <i class="bi bi-camera-fill"></i>
                        </div>

                        <div>
                            <h2>Profile Picture</h2>
                            <p>JPG, JPEG or PNG — maximum 2 MB</p>
                        </div>

                    </div>


                    <div class="admin-profile-avatar">

                        <?php if (!empty($profilePicture)): ?>

                            <img
                                src="uploads/profile/<?= e($profilePicture) ?>"
                                alt="Administrator profile picture"
                            >

                        <?php else: ?>

                            <i class="bi bi-person-fill"></i>

                        <?php endif; ?>

                    </div>


                    <div class="text-center mb-3">

                        <h3 class="h5 mb-1"><?= e($adminName) ?></h3>

                        <span class="admin-profile-status">
                            <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i>
                            <?= e($status) ?>
                        </span>

                    </div>


                    <form
                        action="index.php?action=uploadAdminProfilePicture"
                        method="POST"
                        enctype="multipart/form-data"
                        class="admin-profile-picture-form"
                    >

                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                        <div class="mb-3">

                            <input
                                type="file"
                                name="profile_picture"
                                id="adminProfilePicture"
                                class="form-control"
                                accept=".jpg,.jpeg,.png"
                                required
                            >

                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            Upload Profile Picture
                        </button>

                    </form>

                </div>


                <!-- ACCOUNT INFORMATION -->
                <div class="admin-profile-card">

                    <div class="admin-profile-card-header">

                        <div class="admin-profile-card-icon">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>

                        <div>
                            <h2>Account Information</h2>
                            <p>Your administrator account details</p>
                        </div>

                    </div>

                    <div class="admin-profile-info-row">
                        <span class="admin-profile-info-label">IC Number</span>
                        <span class="admin-profile-info-value"><?= e($adminIC) ?></span>
                    </div>

                    <div class="admin-profile-info-row">
                        <span class="admin-profile-info-label">Role</span>
                        <span class="admin-profile-info-value"><?= e($role) ?></span>
                    </div>

                    <div class="admin-profile-info-row">
                        <span class="admin-profile-info-label">Member Since</span>
                        <span class="admin-profile-info-value"><?= e($memberSince) ?></span>
                    </div>

                </div>

            </div>


            <!-- =================================================
                 RIGHT COLUMN
            ================================================== -->

            <div>

                <!-- PERSONAL INFORMATION -->
                <div class="admin-profile-card mb-4">

                    <div class="admin-profile-card-header">

                        <div class="admin-profile-card-icon">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>

                        <div>
                            <h2>Personal Information</h2>
                            <p>Update your display name</p>
                        </div>

                    </div>

                    <form
                        action="index.php?action=updateAdminProfile"
                        method="POST"
                        class="admin-profile-form"
                    >

                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                        <div class="mb-3">

                            <label class="form-label" for="admin_name">Full Name</label>

                            <input
                                type="text"
                                name="admin_name"
                                id="admin_name"
                                class="form-control"
                                value="<?= e($adminName) ?>"
                                minlength="3"
                                maxlength="100"
                                required
                            >

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="admin_ic">IC Number</label>

                            <input
                                type="text"
                                id="admin_ic"
                                class="form-control"
                                value="<?= e($adminIC) ?>"
                                readonly
                            >

                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>
                            Save Changes
                        </button>

                    </form>

                </div>


                <!-- CHANGE PASSWORD -->
                <div class="admin-profile-card">

                    <div class="admin-profile-card-header">

                        <div class="admin-profile-card-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>

                        <div>
                            <h2>Change Password</h2>
                            <p>Keep your account secure</p>
                        </div>

                    </div>

                    <form
                        action="index.php?action=changeAdminPassword"
                        method="POST"
                        class="admin-profile-form"
                    >

                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                        <div class="mb-3">

                            <label class="form-label" for="current_password">Current Password</label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    name="current_password"
                                    id="current_password"
                                    class="form-control"
                                    autocomplete="current-password"
                                    required
                                >

                                <button type="button" class="btn toggle-password" data-target="current_password">
                                    Show
                                </button>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="new_password">New Password</label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    name="new_password"
                                    id="new_password"
                                    class="form-control"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >

                                <button type="button" class="btn toggle-password" data-target="new_password">
                                    Show
                                </button>

                            </div>

                            <div class="form-text">
                                Minimum 8 characters, including an uppercase letter,
                                a lowercase letter and a special character.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="confirm_password">Confirm New Password</label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    name="confirm_password"
                                    id="confirm_password"
                                    class="form-control"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >

                                <button type="button" class="btn toggle-password" data-target="confirm_password">
                                    Show
                                </button>

                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key-fill"></i>
                            Change Password
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- Custom JavaScript -->
<script src="assets/script.js"></script>

</body>

</html>
