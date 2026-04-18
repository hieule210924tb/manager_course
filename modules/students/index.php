<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Thêm mới khóa học'
];
layout('header', $data);
layout('sidebar');
$filter = filterData("GET");
$keyword = "";

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
}
$permissionArr = [];
//Lấy dữ liệu users
$userDetail = getAll("SELECT fullname, email, permission from users");
if (!empty($userDetail)) {
    foreach ($userDetail as $key => $item) {
        $permissionJson = json_decode($item['permission'], true);
        $permissionArr[$key] = $permissionJson;
    }
}

?>
<div class="container">
    <form action="" method="GET" enctype="multipart/form-data">
        <input type="hidden" value="students" name="module">
        <div class="row d-flex justify-content-center mt-2">
            <div class="col-7">
                <select name="keyword" id="" class="form-select form-control">
                    <option value="0">Chọn khóa học</option>
                    <?php $getCourseDetail = getAll("SELECT id , name from course");
                    foreach ($getCourseDetail as $item) :
                    ?>
                    <option value="<?php echo $item['id'] ?>" <?php echo ($keyword == $item['id']) ? "selected" : "" ?>>
                        <?php echo $item['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success">Duyệt</button>
            </div>
        </div>
    </form>
    <div class="row d-flex justify-content-center mt-2">
        <div class="col-9">
            <table class="table table-borderd">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên học viên</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $dem = 0;
                    foreach ($permissionArr as $key => $item) :
                        if (!empty($item)) :
                            if (in_array($keyword, $item)) :
                    ?>
                    <tr>
                        <td><?php echo $dem + 1;
                                        $dem++ ?></td>
                        <td><?php echo $userDetail[$key]['fullname'] ?></td>
                        <td><?php echo $userDetail[$key]['email'] ?></td>
                    </tr>
                    <?php
                            endif;
                        endif;
                    endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
layout('footer');
?>