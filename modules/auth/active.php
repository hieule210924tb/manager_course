<?php

if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
require_once './templates/layout/header-auth.php';
?>
<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATES ?>/assets/image/login.webp" class="img-fluid"
                    alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                    <h2 class="fw-normal mb-5 me-3">Kích hoạt tài khoản thành công</h2>
                </div>
                <a href="<?php echo _HOST_URL ?>?module=auth&action=login"
                    style="font-size: 20ox !important; color: blue !important;" class="link-danger">Đăng
                    nhập ngay</a>
            </div>
        </div>
    </div>
</section>
<?php require_once './templates/layout/footer.php'; ?>