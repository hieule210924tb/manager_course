<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$filter = filterData('GET');
if (!empty($filter['id'])) {
    $cateId = $filter['id'];
    $checkCate = getOne("SELECT * from course_category where id =$cateId");
    if (empty($checkCate)) {
        redirect("?module=course_category&action=list");
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra.');
    setSessionFlash('msg_type', 'danger');
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorArr = getSessionFlash('errors');
$msg = '';
$msg_type = '';
$errors = [];
$errorArr = [];
$oldData = [];
if (isPost()) {
    $filter = filterData();
    // $errors = [];

    //validate name
    if (empty(trim($filter['name']))) {
        $errors['name']['require'] = 'Tên lĩnh vực bắt buộc phải nhập';
    }
    //validate slug
    if (empty(trim($filter['slug']))) {
        $errors['slug']['require'] = 'Đường dẫn bắt buộc phải nhập';
    }
    if (empty($errors)) {
        //insert data in course_category
        $data = [
            "name" => $filter['name'],
            "slug" => $filter['slug'],
            "updated_at" => date("Y-m-d H:i:s")
        ];
        $condition = "id =" . $cateId;
        $updateStatus = update('course_category', $data, $condition);
        if ($updateStatus) {
            setSessionFlash('msg', 'Sửa lĩnh vực thành công.');
            setSessionFlash('msg_type', 'success');
            redirect("?module=course_category&action=list");
        } else {
            setSessionFlash('msg', 'Sửa dữ liệu lĩnh vực thất bại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        // setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        // setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
if (!empty($checkCate)) {
    $oldData = $checkCate;
}
$errorArr = getSessionFlash('errors');
?>
<h2>Chỉnh sửa lĩnh vực</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="type" value="edit">
    <input type="hidden" name="id" value="<?php echo $cateId ?>">
    <div class="col-12">
        <label for="name">Tên lĩnh vực</label>
        <input type="text" id="name" name="name" value="<?php oldData($oldData, 'name') ?>" class="form-control"
            placeholder="Tên lĩnh vực">
        <?php displayErrors($errorArr, 'name') ?>
    </div>
    <div class="col-12">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" value="<?php oldData($oldData, 'slug') ?>" class="form-control"
            placeholder="Slug">
        <?php displayErrors($errorArr, 'slug') ?>
    </div>

    <button type="submit" class="btn btn-success mt-3">Cập nhật</button>
</form>


<script>
//Hàm giúp chuyển text thành slug
function createSlug(string) {
    return string.toLowerCase()
        .normalize('NFD') // chuyển ký tự có dấu thành tổ hợp
        .replace(/[\u0300-\u036f]/g, '') // xoá dấu
        .replace(/đ/g, 'd') // thay đ -> d
        .replace(/[^a-z0-9\s-]/g, '') // xoá ký tự đặc biệt
        .trim() // bỏ khoảng trắng đầu/cuối
        .replace(/\s+/g, '-') // thay khoảng trắng -> -
        .replace(/-+/g, '-'); // bỏ trùng dấu -
}
const name = document.getElementById('name')
name.addEventListener('input', () => {
    const getValue = name.value;
    document.getElementById('slug').value = createSlug(getValue)
})
</script>