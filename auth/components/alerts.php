<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success text-center mb-2" role="alert">
        <i class="bi bi-check-circle me-1"></i> <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']) ?>
<?php endif ?>

<?php if (isset($_SESSION['warning'])): ?>
    <div class="alert alert-warning text-center mb-2" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> <?= $_SESSION['warning']; ?>
    </div>
    <?php unset($_SESSION['warning']) ?>
<?php endif ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center mb-2" role="alert">
        <i class="bi bi-x-circle me-1"></i> <?= $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']) ?>
<?php endif ?>