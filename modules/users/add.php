<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Thêm mới người dùng'
];
layout('header', $data);
layout('sidebar');
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

    if (empty($errors)) {
        $dataInsert = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'group_id' => $filter['group_id'],
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
            'avatar' => 'templates/uploads/avatar.jpg',
            'address' => (!empty($filter['address']) ? $filter['address'] : null),
            'created_at' => date("Y-m-d H:i:s")
        ];
        $insertStatus = insert('users', $dataInsert);
        if ($insertStatus) {
            setSessionFlash('msg', 'Thêm người dùng thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Thêm người dùng thất bại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $oldData = getSessionFlash('oldData');
    $errorArr = getSessionFlash('errors');
}
?>
<div class="container px-5">
    <h2>Thêm mới người dùng</h2>
    <hr>
    <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
    <form action="" method="POST">
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
                <input type="text" name="address" id="address" class="form-control" placeholder="Địa chỉ">
                <?php displayErrors($errorArr, 'address') ?>
            </div>
            <div class="col-3">
                <label for="group">Phân cấp người dùng</label>
                <select name="group_id" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("SELECT * from  `groups`");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id'] ?>">
                            <?php echo $item['name'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-3">
                <label for="status">Trạng thái tài khoản</label>
                <select name="status" id="status" class="form-select form-control">
                    <option value="0">Chưa kích hoạt</option>
                    <option value="1">Đã kích hoạt</option>
                </select>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Xác nhận gửi</button>
        </div>
    </form>
</div>
<?php layout('footer'); ?>