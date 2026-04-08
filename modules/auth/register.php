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
$oldData = [];
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
        $activeToken = sha1(uniqid() . time());
        // table, data
        $data = [
            'fullname' => $filter['fullname'],
            // 'address' => $filter['address'],
            'phone' => $filter['phone'],
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
            'email' => $filter['email'],
            'active_token' => $activeToken,
            'group_id' => 1,
            'created_at' => date('Y:m:d H:i:s'),
        ];
        $insertStatus = insert('users', $data);
        if ($insertStatus) {
            //Gửi email
            $emailTo = $filter['email'];
            $subject = 'Kích hoạt tài khoản hệ thống!';

            // Tạo đường dẫn kích hoạt
            $active_link = _HOST_URL . '/?module=auth&action=active&token=' . $activeToken;

            $content = '
            <div style="font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; background-color: #f8f9fa; padding: 30px; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #e1e1e1;">
                
                <div style="background-color: #28a745; padding: 25px; text-align: center;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Chào mừng bạn mới!</h1>
                </div>

                <div style="padding: 30px;">
                    <p style="font-size: 18px; font-weight: bold; color: #28a745;">Chúc mừng Hiếu đã đăng ký thành công!</p>
                    <p>Cảm ơn bạn đã tin tưởng và tham gia vào hệ thống của chúng tôi. Chỉ còn một bước nữa thôi để bắt đầu trải nghiệm.</p>
                    <p>Vui lòng nhấn vào nút bên dưới để xác nhận và kích hoạt tài khoản của bạn:</p>
                    
                    <div style="text-align: center; margin: 35px 0;">
                        <a href="' . $active_link . '" 
                            style="background-color: #28a745; color: white; padding: 14px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block; font-size: 16px; box-shadow: 0 4px 6px rgba(40, 167, 69, 0.3);">
                            Kích hoạt tài khoản ngay
                        </a>
                    </div>

                    <p style="font-size: 13px; color: #777; background-color: #f1f3f5; padding: 10px; border-radius: 5px;">
                        Nếu nút trên không phản hồi, bạn hãy sao chép liên kết này vào trình duyệt: <br>
                        <a href="' . $active_link . '" style="color: #28a745; word-break: break-all;">' . $active_link . '</a>
                    </p>
                    
                    <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
                    <p style="margin-bottom: 5px;">Trân trọng,</p>
                    <p><strong>Đội ngũ vận hành hệ thống</strong></p>
                </div>

                <div style="background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee;">
                    Bạn nhận được email này vì đã đăng ký tài khoản tại ' . _HOST_URL . '. <br>
                    © 2026 Hệ thống của chúng tôi. All rights reserved.
                </div>
            </div>
            </div>
            ';
            sendMail($emailTo, $subject, $content);
            setSessionFlash('msg', 'Đăng kí thành công, vui lòng kích hoạt tài khoản.');
            setSessionFlash('msg_type', 'success');
        } else {
            setSessionFlash('msg', 'Đăng kí không thành công, vui lòng thử lại sau.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter); // lưu dữ liệu hợp lệ, để khi users nhập đúng trường nào thì f5 lại không 
        //mất dữ liệu đã nhập đúng
        setSessionFlash('errors', $errors); // Lưu nó vào session 
    }
    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $oldData = getSessionFlash('oldData');
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
                        <input type="text" name="fullname" value="<?php oldData($oldData, 'fullname') ?>"
                            class="form-control form-control-lg" placeholder="Họ tên" />
                        <?php displayErrors($errorArr, 'fullname') ?>
                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="email" value="<?php oldData($oldData, 'email') ?>"
                            class="form-control form-control-lg" placeholder="Địa chỉ email" />
                        <?php displayErrors($errorArr, 'email') ?>
                    </div>
                    <!-- Phone input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="phone" value="<?php oldData($oldData, 'phone') ?>"
                            class="form-control form-control-lg" placeholder="Số điện thoại" />
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