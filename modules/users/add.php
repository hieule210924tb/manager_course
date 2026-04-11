<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Thêm mới người dùng'
];
layout('header', $data);
layout('sidebar');
?>
<div class="container px-5">
    <h2>Thêm mới người dùng</h2>
    <hr>
    <form action="" method="POST">
        <div class="row g-3">
            <div class="col-6">
                <label for="fullname">Họ và tên</label>
                <input type="text" id="fullname" class="form-control" placeholder="Họ tên">
            </div>
            <div class="col-6">
                <label for="email">Email</label>
                <input type="text" id="email" class="form-control" placeholder="Email">
            </div>
            <div class="col-6">
                <label for="phone">Phone</label>
                <input type="text" id="phone" class="form-control" placeholder="Số điện thoại">
            </div>
            <div class="col-6">
                <label for="password">Mật khẩu</label>
                <input type="password" class="form-control" placeholder="Mật khẩu">
            </div>
            <div class="col-6">
                <label for="address">Địa chỉ</label>
                <input type="text" id="address" class="form-control" placeholder="Địa chỉ">
            </div>
            <div class="col-3">
                <label for="group">Phân cấp người dùng</label>
                <select name="group" id="group" class="form-select form-control">
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
            <button class="btn btn-success">Xác nhận gửi</button>
        </div>
    </form>
</div>
<?php layout('footer'); ?>