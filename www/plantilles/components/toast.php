<?php
// plantilles/components/toast.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['toast'])):
    $toast = $_SESSION['toast'];
    $missatge = $toast['missatge'];
    $tipus = $toast['tipus'] ?? 'info'; // 'exit', 'error', 'info'

    $classe_bg = 'bg-primary text-white'; // Default
    $icona = 'fa-circle-info';
    $titol = 'Informació';

    if ($tipus === 'exit') {
        $classe_bg = 'bg-success text-white';
        $icona = 'fa-circle-check';
        $titol = 'Èxit';
    } elseif ($tipus === 'error') {
        $classe_bg = 'bg-danger text-white';
        $icona = 'fa-circle-xmark';
        $titol = 'Error';
    }
?>

<div class="toast align-items-center <?php echo e($classe_bg); ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div class="toast-body">
      <i class="fa-solid <?php echo e($icona); ?> me-2"></i>
      <?php echo e($missatge); ?>
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>

<?php
    unset($_SESSION['toast']);
endif;
?>