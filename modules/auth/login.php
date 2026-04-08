<?php

if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Đăng nhập hệ thống',
];
layout('header-auth', $data);
$msg = '';
$msg_type = '';
$errors = [];
$errorArr = [];
$oldData = [];
if (isPost()) {
    $filter = filterData();
    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email bắt buộc phải nhập';
    } else {
        //Đúng định dạng email,
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
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
    if (empty($errors)) {
        //Kiểm tra dữ liệu 
        $email = $filter['email'];
        $password = $filter['password'];
        //Kiểm tra email 
        $checkEmail = getOne("SELECT * from users where email = '$email'");
        if (!empty($checkEmail)) {
            if (!empty($password)) {
                $checkStatus = password_verify($password, $checkEmail['password']);
                if ($checkStatus) {
                    //Tài khoản chỉ login một nới
                    $user_id = $checkEmail['id'];
                    $checkAllready = getRows("SELECT * FROM token_login where user_id = '$user_id'");
                    if ($checkAllready > 0) {
                        setSessionFlash('msg', 'Tài khoản đang được đăng nhập ở 1 nơi khác, vui lòng thử lại sau.');
                        setSessionFlash('msg_type', 'danger');
                        redirect('?module=auth&action=login');
                    } else {
                        //Tạo token và insert vào bảng token_login
                        $token = sha1(uniqid() . time());
                        //gán token lên session
                        setSessionFlash('token_login', $token);

                        $data = [
                            'token' => $token,
                            'created_at' => date('Y-m-d H:i:s'),
                            'user_id' => $checkEmail['id'],
                        ];
                        $insertToken = insert('token_login', $data);
                        if ($insertToken) {
                            setSessionFlash('msg', 'Đăng nhập thành công.');
                            setSessionFlash('msg_type', 'success');
                            redirect('/');
                        } else {
                            setSessionFlash('msg', 'Đăng nhập không thành công.');
                            setSessionFlash('msg_type', 'danger');
                        }
                    }
                } else {
                    setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
                    setSessionFlash('msg_type', 'danger');
                }
            }
        } else {
            setSessionFlash('msg', 'Chưa có tài khoản này, xin vui lòng đăng kí');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter); // lưu dữ liệu hợp lệ, để khi users nhập đúng trường nào thì f5 lại không 
        //mất dữ liệu đã nhập đúng
        setSessionFlash('errors', $errors); // Lưu nó vào session 
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorArr = getSessionFlash('errors'); // để lấy dữ liệu error ra

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
                        <h2 class="fw-normal mb-5 me-3">Đăng nhập hệ thống</h2>

                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" name="email" value="<?php oldData($oldData, 'email') ?>"
                            class="form-control form-control-lg" placeholder="Địa chỉ email" />
                        <?php displayErrors($errorArr, 'email') ?>
                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="Nhập mật khẩu" />
                        <?php displayErrors($errorArr, 'password') ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Checkbox -->
                        <a href="<?php echo _HOST_URL ?>?module=auth&action=forgot" class="text-body">Quên mật khẩu
                            ?</a>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng nhập</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Bạn chưa có tài khoản ? <a
                                href="<?php echo _HOST_URL ?>?module=auth&action=register" class="link-danger">Đăng kí
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

<!-- 
-Kiểm tra dữ liệu đầu vào
-Check dữ liệu email, password
-Dữ liệu khớp -> tokenlogin -> insert vào bảng  token_login ( để kiểm tra đăng nhập)

-Kiểm tra đăng nhập
  +Gán token_login lên session
  +Trong header -> lấy token từ session về và so khớp với token trong bảng token_login
  +Nếu khớp thì điều hướng đến trang đích (không khớp thì điều hướng về trang login)

-Điều hướng đến trang dashboard

-Chỉ cho phép đăng nhập tài khoản ở 1 nơi, tại một thời điểm
-->