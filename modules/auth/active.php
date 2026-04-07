<?php

if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Kích hoạt tài khoản',
];
layout('header-auth', $data);

$filter = filterData('GET');
// xử lý đường link hợp lệ
if (!empty($filter['token'])) :
    $token = $filter['token'];
    $checkToken = getOne("SELECT * from users where active_token =  '$token' ");
?>
<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATES ?>/assets/image/login.webp" class="img-fluid"
                    alt="Sample image">
            </div>
            <?php if (!empty($checkToken)) :
                    //Thực hiện update dữ liệu trường status
                    $data = [
                        'status' => 1,
                        'active_token' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    update('users', $data, "id= " . $checkToken['id']);

                ?>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                    <h2 class="fw-normal mb-5 me-3">Kích hoạt tài khoản thành công</h2>
                </div>
                <a href="<?php echo _HOST_URL ?>?module=auth&action=login"
                    style="font-size: 20px !important; color: blue !important;" class="link-danger">Đăng
                    nhập ngay</a>
            </div>
            <?php else :
                ?>
            <section class="vh-100">
                <div class="container-fluid h-custom">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                        <div class="col-md-9 col-lg-6 col-xl-5">
                            <img src="<?php echo _HOST_URL_TEMPLATES ?>/assets/image/login.webp" class="img-fluid"
                                alt="Sample image">
                        </div>
                        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                            <div
                                class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                <h2 class="fw-normal mb-5 me-3">Kích hoạt tài khoản không thành công. Đường link đã hết
                                    hạn</h2>
                            </div>
                        </div>
                    </div>
            </section>
            <?php endif ?>
        </div>
    </div>
</section>
<?php
//Đường link sai, không hợp lệ
else :

?>
<div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
    <h2 class="fw-normal mb-5 me-3">
        Kích hoạt tài khoản không thành công. Đường link đã hết hạn
    </h2>
</div>
<?php
endif
?>

<?php layout('footer'); ?>

<!-- 
Kiểm tra xem active_token ở url có giống active_token trong database không (users)
Update trường dữ liệu status trong bảng users -> 1 (đã kích hoạt)

-->