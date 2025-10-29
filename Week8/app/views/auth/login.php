<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    body {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        overflow: hidden;
        padding: 20px;
    }
    
    body::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
        animation: rotate 30s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .login-container {
        width: 100%;
        max-width: 440px;
        position: relative;
        z-index: 1;
    }
    
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        border: 1px solid rgba(212, 175, 55, 0.2);
    }
    
    .login-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        padding: 45px 35px 35px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .login-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #d4af37 0%, #f4e5b0 50%, #d4af37 100%);
    }
    
    .login-header h3 {
        margin: 0;
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    
    .login-header .subtitle {
        font-size: 13px;
        opacity: 0.85;
        font-weight: 300;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    
    .login-header .icon {
        font-size: 48px;
        margin-bottom: 18px;
        display: inline-block;
        color: #d4af37;
        filter: drop-shadow(0 2px 4px rgba(212, 175, 55, 0.3));
    }
    
    .login-body {
        padding: 40px 35px 35px;
        background: white;
    }
    
    .form-label {
        font-weight: 600;
        color: #2d2d2d;
        margin-bottom: 10px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 13px 16px;
        border: 2px solid #e8e8e8;
        transition: all 0.3s ease;
        font-size: 15px;
        background: #fafafa;
    }
    
    .form-control:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.15);
        background: white;
    }
    
    .input-group-text {
        background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        color: #d4af37;
        border: none;
        border-radius: 8px 0 0 8px;
        padding: 13px 16px;
    }
    
    .input-group .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0;
        padding-left: 12px;
    }
    
    .btn-login {
        background: linear-gradient(135deg, #d4af37 0%, #c9a428 100%);
        border: none;
        border-radius: 8px;
        padding: 14px;
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        background: linear-gradient(135deg, #e5c158 0%, #d4af37 100%);
        color: #1a1a1a;
    }
    
    .btn-login:active {
        transform: translateY(0);
    }
    
    .form-check-input:checked {
        background-color: #d4af37;
        border-color: #d4af37;
    }
    
    .form-check-label {
        color: #555;
        font-size: 14px;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        padding: 14px 18px;
        margin-bottom: 25px;
        font-size: 14px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }
    
    .footer-text {
        text-align: center;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 30px;
        font-size: 13px;
        letter-spacing: 0.5px;
    }
    
    .footer-text .year {
        color: #d4af37;
        font-weight: 600;
    }
    
    .secure-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: rgba(212, 175, 55, 0.15);
        border-radius: 20px;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.9);
    }
    
    /* Remove input autofill yellow background */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 30px #fafafa inset !important;
        -webkit-text-fill-color: #2d2d2d !important;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="icon">
                <i class="bi bi-hospital-fill"></i>
            </div>
            <h3>Hospital Management</h3>
            <p class="subtitle mb-0">System Access Portal</p>
        </div>
        
        <div class="login-body">
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?= $_SESSION['flash_type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= $_SESSION['flash_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>
            
            <form method="POST" action="<?= url('login') ?>">
                <div class="mb-4">
                    <label for="identifier" class="form-label">
                        <i class="bi bi-person-circle"></i> Username or Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" 
                               class="form-control <?= isset($_SESSION['errors']['identifier']) ? 'is-invalid' : '' ?>" 
                               id="identifier" 
                               name="identifier" 
                               placeholder="Enter your username or email"
                               value="<?= $_SESSION['old']['identifier'] ?? '' ?>"
                               required 
                               autofocus>
                        <?php if (isset($_SESSION['errors']['identifier'])): ?>
                        <div class="invalid-feedback">
                            <?= $_SESSION['errors']['identifier'] ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-shield-lock"></i> Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password"
                               required>
                        <?php if (isset($_SESSION['errors']['password'])): ?>
                        <div class="invalid-feedback">
                            <?= $_SESSION['errors']['password'] ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Keep me signed in
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="footer-text">
        <div class="secure-badge">
            <i class="bi bi-shield-check"></i>
            <span>Secure Login Protected</span>
        </div>
        <p class="mb-0 mt-3">
            <span class="year">© 2025</span> Hospital Management System • Version 8.0
        </p>
    </div>
</div>

<?php 
// Clear old input and errors
unset($_SESSION['old']);
unset($_SESSION['errors']);
?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
