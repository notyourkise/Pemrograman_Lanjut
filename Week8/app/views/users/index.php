<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-people"></i> Users Management</h2>
                    <p class="text-muted">Manage system users (Doctors and Receptionists)</p>
                </div>
                <div>
                    <a href="<?= url('users/create') ?>" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill"></i> Create User
                    </a>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($users)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No users found. Create your first user!
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user->getId() ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($user->getFullName()) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($user->getUsername()) ?></td>
                                        <td><?= htmlspecialchars($user->getEmail()) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user->isAdmin() ? 'danger' : ($user->isDoctor() ? 'primary' : 'success') ?>">
                                                <?= $user->getRoleLabel() ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($user->isActive()): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user->getLastLogin()): ?>
                                                <?= date('M d, Y H:i', strtotime($user->getLastLogin())) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= url('users/edit/' . $user->getId()) ?>" 
                                                   class="btn btn-outline-primary"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if (!$user->isAdmin()): // Can't delete admin ?>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        onclick="confirmDelete(<?= $user->getId() ?>, '<?= htmlspecialchars($user->getFullName()) ?>')"
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="card text-center bg-danger text-white">
                        <div class="card-body">
                            <h3><?= count(array_filter($users, fn($u) => $u->isAdmin())) ?></h3>
                            <p class="mb-0">Administrators</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center bg-primary text-white">
                        <div class="card-body">
                            <h3><?= count(array_filter($users, fn($u) => $u->isDoctor())) ?></h3>
                            <p class="mb-0">Doctors</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center bg-success text-white">
                        <div class="card-body">
                            <h3><?= count(array_filter($users, fn($u) => $u->isReceptionist())) ?></h3>
                            <p class="mb-0">Receptionists</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function confirmDelete(userId, userName) {
    if (confirm(`Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= url('users/delete/') ?>' + userId;
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
