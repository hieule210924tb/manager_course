<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Phân quyền người dùng tài khoản'
];
layout('header', $data);
layout('sidebar');
$filterGet = filterData("GET");
if (!empty($filterGet)) {
    $idUser = $filterGet['id'];
    $checkId = getOne("SELECT * from users where id ='$idUser'");
    if (empty($checkId)) {
        redirect("?module=users&action=list");
    }
} else {
    setSessionFlash('msg', 'Người dùng không tồn tại.');
    setSessionFlash('msg_type', 'danger');
}
if (isPost()) {
    $filter = filterData();
    if (!empty($filter['permission'])) {
        $permission = json_encode($filter['permission']); // chuyển từ dạng mảng sang json
    } else {
        $permission = [];
    }
    //update vào bảng users

    $dataUpdate = [
        'permission' => $permission,
        'updated_at' => date("Y-m-d H:i:s")
    ];
    $condition = "id=" . $idUser;
    $checkUpdate = update("users", $dataUpdate, $condition);
    if ($checkUpdate) {
        setSessionFlash('msg', 'Cập nhật thành công.');
        setSessionFlash('msg_type', 'success');
        redirect("?module=users&action=permission&id=$idUser");
    } else {
        setSessionFlash('msg', 'Phân quyền thất bại');
        setSessionFlash('msg_type', 'danger');
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
if (!empty($checkId['permission'])) {
    $permissionOld = json_decode($checkId['permission'], true); // chuyển từ json về array
}
?>

<div class="container">
    <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
    <form action="" method="POST">
        <table class="table table-borderd">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Khóa học</th>
                    <th>Phân quyền</th>
                </tr>
            </thead>
            <tbody>
                <?php $getDetailCourse = getAll("SELECT id , name from course");
                $dem = 1;
                foreach ($getDetailCourse as $item) :
                ?>
                <tr>
                    <td><?php echo $dem;
                            $dem++ ?></td>
                    <td><?php echo $item['name'] ?></td>
                    <td><input type="checkbox" name="permission[]"
                            <?php echo (!empty($permissionOld))  && in_array($item['id'], $permissionOld) ? "checked" : "false" ?>
                            value="<?php echo $item['id'] ?>"></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Xác nhận</button>
        <a href="?module=users&action=list" class="btn btn-success">Quay lại</a>
    </form>
</div>