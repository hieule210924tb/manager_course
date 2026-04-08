<?php

if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
};
$data = [
    'title' => 'Đặt lại mật khẩu'
];
layout('header-auth', $data);
$filterGet = filterData('GET');
$errors = [];
$errorArr = [];

if (!empty($filterGet['token'])) {
    $tokenRest = $filterGet['token'];
}
if (!empty($tokenRest)) {
    //Check token có chính xác hay không
    $checkToken = getOne("SELECT * from users where forget_token = '$tokenRest'");

    if (!empty($checkToken)) {
        if (isPost()) {
            $filter = filterData();
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
                $password = password_hash($filter['password'], PASSWORD_DEFAULT);
                $data = [
                    'password' => $password,
                    'forget_token' => null, // chỉ để lại token khi chưa reset
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $condition = "id=" . $checkToken['id'];

                $updateStatus = update('users', $data, $condition);
                if ($updateStatus) {
                    //Gửi mail thông báo đã đổi mk thành công
                    $emailTo =  $checkToken['email'];
                    $subject = 'Đổi mật khẩu thành công';

                    $content = '<div style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f7f6; padding: 30px;">
                    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        
                        <div style="background-color: #17a2b8; padding: 20px; text-align: center;">
                            <h2 style="color: #ffffff; margin: 0; font-size: 22px;">Cập Nhật Tài Khoản</h2>
                        </div>

                        <div style="padding: 30px; color: #444;">
                            <p style="font-size: 18px; color: #17a2b8; font-weight: bold;">Chúc mừng Hiếu!</p>
                            <p>Mật khẩu tài khoản của bạn đã được thay đổi thành công vào lúc <strong>' . date('H:i:s d/m/Y') . '</strong>.</p>
                            
                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 25px 0;">
                                <p style="margin: 0; color: #856404; font-size: 14px;">
                                    <strong>Cảnh báo bảo mật:</strong> Nếu bạn <b>không</b> thực hiện thay đổi này, tài khoản của bạn có thể đang gặp nguy hiểm. Hãy liên hệ ngay với Quản trị viên hoặc sử dụng chức năng quên mật khẩu để bảo vệ tài khoản.
                                </p>
                            </div>

                            <p>Nếu là bạn thực hiện, bạn có thể bỏ qua email này và tiếp tục sử dụng dịch vụ.</p>
                            
                            <div style="text-align: center; margin-top: 30px;">
                                <a href="' . _HOST_URL . '" 
                                style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                                Truy cập trang chủ
                                </a>
                            </div>

                            <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
                            <p>Trân trọng,<br><strong>Ban quản trị hệ thống</strong></p>
                        </div>

                        <div style="background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #999;">
                            Đây là thông báo bảo mật định kỳ. Vui lòng không chia sẻ email này với bất kỳ ai.
                        </div>
                    </div>
                </div>
                ';

                    sendMail($emailTo, $subject, $content);
                    setSessionFlash('msg', 'Đổi mật khẩu thành công.');
                    setSessionFlash('msg_type', 'success');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại sau.');
                    setSessionFlash('msg_type', 'danger');
                }
            } else {
                setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
                setSessionFlash('msg_type', 'danger');
                setSessionFlash('oldData', $filter);
                setSessionFlash('errors', $errors);
            }
        }
    } else {
        getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
    }
} else {
    getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
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
                        <h2 class="fw-normal mb-5 me-3">Đặt lại mật khẩu</h2>

                    </div>

                    <!-- Password mới input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="Nhập mật khẩu mới" />
                        <?php displayErrors($errorArr, 'password') ?>
                    </div>

                    <!--Nhập lại Password mới input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" name="confirm_password" class="form-control form-control-lg"
                            placeholder="Nhập lại mật khẩu mới" />
                        <?php displayErrors($errorArr, 'confirm_password') ?>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
                    </div>
                    <p class="mt-2"><a href="<?php echo _HOST_URL ?>?module=auth&action=login" class="link-danger">Quay
                            lại
                            đăng nhập -></a></p>
                </form>
            </div>
        </div>
    </div>
</section>
<?php layout('footer'); ?>