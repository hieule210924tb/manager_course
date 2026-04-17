<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Thông tin người dùng'
];
layout('header', $data);
layout('sidebar');
$msg = '';
$msg_type = '';
$errors = [];
$errorArr = [];
$oldData = [];

$getData = filterData('GET');
// Lấy thông tin user 
$token = getSession('token_login');
if (!empty($token)) {
    $checkTokenLogin = getOne("SELECT * from token_login where token ='$token'");
    if (!empty($checkTokenLogin)) {
        $user_id = $checkTokenLogin['user_id'];
        $detailUser = getOne("SELECT * from users where id = '$user_id'");
    }
}
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


    if ($filter['email'] != $detailUser['email']) {
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
    if (!empty(trim($filter['password']))) {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 kí tự';
        }
    }

    if (empty($errors)) {
        $dataUpdate = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'address' => (!empty($filter['address']) ? $filter['address'] : null),
            'updated_at' => date("Y-m-d H:i:s")
        ];
        if (!empty($_FILES['avatar']['name'])) {
            // Xử lý avatar lên
            $uploadDir = './templates/upload/'; // upload ảnh lên đâu
            if (!file_exists($uploadDir)) { // kiểm tra thư mục này đã tồn tại hay chưa
                mkdir($uploadDir, 0777, true); //Tạo mới thư mục upload nếu chưa có
            }
            $fileName = basename($_FILES['avatar']['name']);
            $targetFile =  $uploadDir . time() . '-' . $fileName; // thêm time() để tránh bị trùng lặp tên ảnh
            $thumb = "";
            $checkMove = move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile);
            $targetFile = ltrim($targetFile, './');
            if ($checkMove) {
                $thumb = $targetFile;
            }

            $dataUpdate['avatar'] = $thumb;
        }
        if (!empty($filter['password'])) {
            $dataUpdate['password'] = password_hash($filter['password'], PASSWORD_DEFAULT);
        }
        $condition = "id=" . $user_id;
        $updateStatus = update('users', $dataUpdate, $condition);
        if ($updateStatus) {
            setSessionFlash('msg', 'Cập nhật thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=profile');
        } else {
            setSessionFlash('msg', 'Cập nhật thất bại.');
            setSessionFlash('msg_type', 'danger');
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
if (!empty($detailUser)) {
    $oldData = $detailUser;
}
$errorArr = getSessionFlash('errors');
?>
<div class="container px-5">
    <h2>Thông tin tài khoản</h2>
    <hr>
    <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-6">
                <label for="fullname">Họ và tên</label>
                <input type="text" id="fullname" name="fullname" value="<?php oldData($oldData, 'fullname') ?>"
                    class="form-control" placeholder="Họ tên">
                <?php displayErrors($errorArr, 'fullname') ?>
            </div>
            <div class="col-6">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php oldData($oldData, 'email') ?>"
                    class="form-control" placeholder="Email">
                <?php displayErrors($errorArr, 'email') ?>
            </div>
            <div class="col-6">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php oldData($oldData, 'phone') ?>"
                    class="form-control" placeholder="Số điện thoại">
                <?php displayErrors($errorArr, 'phone') ?>
            </div>
            <div class="col-6">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" value="<?php oldData($oldData, 'password') ?>"
                    class="form-control" placeholder="Mật khẩu">
                <?php displayErrors($errorArr, 'password') ?>
            </div>
            <div class="col-6">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" value="<?php oldData($oldData, 'address') ?>" id="address"
                    class="form-control" placeholder="Địa chỉ">
                <?php displayErrors($errorArr, 'address') ?>
            </div>
            <div class="col-6">
                <label for="avatar">Ảnh đại diện</label>
                <input type="file" name="avatar" id="avatar" class="form-control" placeholder="Thay ảnh đại diện">
                <?php displayErrors($errorArr, 'avatar') ?>
                <img src="<?php echo _HOST_URL  . '/' ?><?php echo !empty(($oldData['avatar'])) ? $oldData['avatar'] : false ?>"
                    id="previewImage" class="previewImage mt-3" width="200px" alt="">
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Xác nhận gửi</button>
        </div>
    </form>
</div>
<script>
// đoạn js để xử lý xem trước ảnh
const thumbInput = document.getElementById('avatar')
const previewImg = document.getElementById('previewImage')
thumbInput.addEventListener('change', () => {
    const file = thumbInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.setAttribute('src', e.target.result);
            previewImg.style.display = 'block !important';
        }
        reader.readAsDataURL(file)
    } else {
        previewImg.style.display = 'none';
    }
})
</script>
<?php layout('footer'); ?>