<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('users') ?>">Users</a></li>
                    <li class="breadcrumb-item active">Create User</li>
                </ol>
            </nav>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus-fill"></i> Create New User
                    </h4>
                </div>
                <div class="card-body p-4">
                    <!-- Info Alert -->
                    <div class="alert alert-info border-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Admin Only Feature</strong><br>
                        Create Doctor or Receptionist accounts. Only administrators can create user accounts.
                    </div>

                    <form method="POST" action="<?= url('users/store') ?>">
                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" 
                                   class="form-control <?= isset($_SESSION['errors']['full_name']) ? 'is-invalid' : '' ?>" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= $_SESSION['old']['full_name'] ?? '' ?>"
                                   placeholder="Enter full name"
                                   required>
                            <?php if (isset($_SESSION['errors']['full_name'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['full_name'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" 
                                   class="form-control <?= isset($_SESSION['errors']['username']) ? 'is-invalid' : '' ?>" 
                                   id="username" 
                                   name="username" 
                                   value="<?= $_SESSION['old']['username'] ?? '' ?>"
                                   placeholder="Choose a username"
                                   required>
                            <?php if (isset($_SESSION['errors']['username'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['username'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= $_SESSION['old']['email'] ?? '' ?>"
                                   placeholder="user@example.com"
                                   required>
                            <?php if (isset($_SESSION['errors']['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['email'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" 
                                   class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter password"
                                   required>
                            <?php if (isset($_SESSION['errors']['password'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['password'] ?>
                            </div>
                            <?php else: ?>
                            <small class="text-muted">Minimum 8 characters</small>
                            <?php endif; ?>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select <?= isset($_SESSION['errors']['role']) ? 'is-invalid' : '' ?>" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Select Role</option>
                                <option value="doctor" <?= ($_SESSION['old']['role'] ?? '') === 'doctor' ? 'selected' : '' ?>>
                                    Doctor
                                </option>
                                <option value="receptionist" <?= ($_SESSION['old']['role'] ?? '') === 'receptionist' ? 'selected' : '' ?>>
                                    Receptionist
                                </option>
                            </select>
                            <?php if (isset($_SESSION['errors']['role'])): ?>
                            <div class="invalid-feedback">
                                <?= $_SESSION['errors']['role'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       <?= (isset($_SESSION['old']['is_active']) || !isset($_SESSION['old'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (User can login)
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create User
                            </button>
                            <a href="<?= url('users') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>

                    <?php 
                    // Clear old input and errors
                    unset($_SESSION['old']);
                    unset($_SESSION['errors']);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
