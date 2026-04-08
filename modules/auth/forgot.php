<?php

if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Quên mật khẩu'
];
layout('header-auth', $data);
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
    if (empty($errors)) {
        //Xử lý và gửi mail người dùng
        if (!empty($filter['email'])) {
            $email = $filter['email'];
            $checkEmail = getOne("SELECT * from users where email ='$email'");
            if (!empty($checkEmail)) {
                // update forgot_token vào bảng users
                $forgot_token = sha1(uniqid() . time()); // Tạo token mới
                $data = [
                    'forget_token' => $forgot_token,
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
                $condition = "id =" . $checkEmail['id'];
                $updateStatus = update('users', $data, $condition);
                if ($updateStatus) {
                    $emailTo = $email;
                    $subject = 'Reset mật khẩu tài khoản hệ thống!';

                    // Tạo đường dẫn link reset
                    $reset_link = _HOST_URL . '/?module=auth&action=reset&token=' . $forgot_token;

                    $content = '
                            <div style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; padding: 20px;">
                            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0;">
                            <div style="background-color: #007bff; padding: 20px; text-align: center;">
                                <h2 style="color: #ffffff; margin: 0;">Reset Mật Khẩu</h2>
                            </div>
                            <div style="padding: 30px;">
                                <p>Xin chào,</p>
                                <p>Bạn đã gửi yêu cầu reset lại mật khẩu cho tài khoản của mình trên hệ thống của chúng tôi.</p>
                                <p>Để thay đổi mật khẩu, vui lòng click vào nút bên dưới:</p>                  
                                <div style="text-align: center; margin: 30px 0;">
                                    <a href="' . $reset_link . '" 
                                        style="background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                                        Đổi mật khẩu ngay
                                    </a>
                                </div>
                                <p style="font-size: 0.9em; color: #666;">
                                    Nếu nút trên không hoạt động, bạn có thể copy và dán đường link sau vào trình duyệt: <br>
                                    <a href="' . $reset_link . '" style="color: #007bff;">' . $reset_link . '</a>
                                </p>                 
                                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                                <p>Cảm ơn bạn,<br><strong>Đội ngũ hỗ trợ hệ thống</strong></p>
                            </div>
                            <div style="background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 0.8em; color: #999;">
                                Đây là email tự động, vui lòng không trả lời email này.
                            </div>
                            </div>
                            </div>
                            ';
                    sendMail($emailTo, $subject, $content);
                    setSessionFlash('msg', 'Gửi yêu cầu thành công, vui lòng kiểm tra email.');
                    setSessionFlash('msg_type', 'success');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra/Vui lòng thử lại sau.');
                    setSessionFlash('msg_type', 'danger');
                }
            }
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
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
                        <h2 class="fw-normal mb-5 me-3">Quên mật khẩu</h2>

                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" name="email" value="<?php oldData($oldData, 'email') ?>"
                            class="form-control form-control-lg" placeholder="Địa chỉ email" />
                        <?php displayErrors($errorArr, 'email') ?>
                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
<?php layout('footer'); ?>