<div class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 1050;">
    <?php if (isset($_SESSION['toast-success'])): ?>
        <div class="toast align-items-center bg-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-circle-check me-1 text-success"></i> <?= $_SESSION['toast-success']; ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['toast-success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['toast-warning'])): ?>
        <div class="toast align-items-center bg-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-triangle-exclamation me-1 text-warning"></i> <?= $_SESSION['toast-warning']; ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['toast-warning']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['toast-error'])): ?>
        <div class="toast align-items-center bg-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-circle-xmark me-1 text-danger"></i> <?= $_SESSION['toast-error']; ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['toast-error']); ?>
    <?php endif; ?>
</div>