<?php
// plantilles/login.php (Nou disseny visual)

if (!function_exists('e')) {
    require_once __DIR__ . '/../src/ajudants.php';
}
?>
<!DOCTYPE html>
<html lang="ca" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sessió - Agenda Osona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .login-image-side {
            flex: 1;
            background: url('img/Sau.jpg') no-repeat center center;
            background-size: cover;
            position: relative;
        }
        .login-form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
        }
        .logo-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }
        .logo-footer .logos-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
        }
        .logo-footer img {
            max-height: 45px;
            width: auto;
            opacity: 0.7;
        }
        @media (max-width: 768px) {
            .login-image-side {
                display: none;
            }
            .login-form-side {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="login-image-side d-none d-md-flex"></div>

        <div class="login-form-side">
            <div class="login-card">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mb-3">
                        <i class="fa-regular fa-calendar-days me-2"></i>Agenda Osona
                    </h1>
                    <p class="text-muted">Inicia sessió al panell de gestió</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa-solid fa-circle-xmark me-2"></i><?php echo e($error); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?accio=login" method="POST">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="correu@exemple.com" required>
                        <label for="email">Correu electrònic</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contrasenya" required>
                        <label for="password">Contrasenya</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Entra
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none">
                        <i class="fa-solid fa-arrow-left me-1"></i> Tornar a la web pública
                    </a>
                </div>

                <div class="logo-footer">
                    <p class="text-muted small mb-3">Amb el suport de:</p>
                    <div class="logos-container">
                        <img src="img/logoturisme.jpg" alt="Logo Osona Turisme">
                        <img src="img/consell apaisat.png" alt="Logo Consell Comarcal d'Osona">
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <?php include __DIR__ . '/components/toast.php'; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>