<?php

if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Đăng kí tài khoản'
];
layout('header-auth', $data);
$msg = '';
$msg_type = '';
$errors = [];
$errorArr = [];
if (isPost()) {
    $filter = filterData();
    // $errors = [];

    //validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['require'] = 'Họ tên bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['fullname'])) < 5) {
            $errors['fullname']['length'] = 'Họ tên phải lớn hơn 5 kí tự';
        }
    }
    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email bắt buộc phải nhập';
    } else {
        // Đúng định dạng không, đã tồn tại trong database chưa
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
        } else {
            $email = $filter['email'];
            $checkEmail = getRows("SELECT * from users where email ='$email'");
            if ($checkEmail > 0) {
                $errors['email']['check'] = 'Email đã tồn tại!';
            }
        }
    }
    // validate sđt
    if (empty($filter['phone'])) {
        $errors['phone']['require'] = 'Số điện thoại bắt buộc phải nhập';
    } else {
        if (!isPhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'Số điện thoại không hợp lệ';
        }
    }

    // validate password > 6 kí tự
    if (empty(trim($filter['password']))) {
        $errors['password']['require'] = 'Mật khẩu bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 kí tự';
        }
    }

    // validate confirm password 
    if (empty(trim($filter['confirm_password']))) {
        $errors['confirm_password']['require'] = 'Vui lòng nhập lại mật khẩu';
    } else {
        if (trim($filter['confirm_password'] != trim($filter['password']))) {
            $errors['confirm_password']['like'] = 'Mật khẩu nhập lại không khớp';
        }
    }


    if (empty($errors)) {
        $msg = 'Đăng kí thành công';
        $msg_type = 'success';
    } else {
        $msg = 'Dữ liệu không hợp lệ, hãy kiểm tra lại!!!';
        $msg_type = 'danger';
        setSessionFlash('errors', $errors); // Lưu nó vào session 
    }

    $errorArr = getSessionFlash('errors'); // để lấy dữ liệu error ra

}
?>
<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATES ?>/assets/image/login.webp" class="img-fluid"
                    alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
                <form method='POST' action="" enctype='multipart/form-data'>
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class="fw-normal mb-5 me-3">Đăng kí tài khoản</h2>

                    </div>
                    <!-- Họ tên input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="fullname" class="form-control form-control-lg" placeholder="Họ tên" />
                        <?php displayErrors($errorArr, 'fullname') ?>
                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="email" class="form-control form-control-lg"
                            placeholder="Địa chỉ email" />
                        <?php displayErrors($errorArr, 'email') ?>
                    </div>
                    <!-- Phone input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="phone" class="form-control form-control-lg"
                            placeholder="Số điện thoại" />
                        <?php displayErrors($errorArr, 'phone') ?>
                    </div>
                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" name="password" id="form3Example4" class="form-control form-control-lg"
                            placeholder="Nhập mật khẩu" />
                        <?php displayErrors($errorArr, 'password') ?>
                    </div>
                    <!-- Password again -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" name="confirm_password" id="form3Example4"
                            class="form-control form-control-lg" placeholder="Nhập lại mật khẩu" />
                        <?php displayErrors($errorArr, 'confirm_password') ?>
                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng kí</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Bạn đã có tài khoản ? <a
                                href="<?php echo _HOST_URL ?>?module=auth&action=login" class="link-danger">Đăng nhập
                                ngay</a></p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
<?php
layout('footer');
?>