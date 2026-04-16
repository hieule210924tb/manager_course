<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Thêm mới khóa học'
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

    //validate name
    if (empty(trim($filter['name']))) {
        $errors['name']['require'] = 'Tên khóa học bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['name'])) < 5) {
            $errors['name']['length'] = 'Tên khóa học phải lớn hơn 5 kí tự';
        }
    }

    //validate slug
    if (empty(trim($filter['slug']))) {
        $errors['slug']['require'] = 'Đường dẫn bắt buộc phải nhập';
    }
    // validate giá
    if (empty($filter['price'])) {
        $errors['price']['require'] = 'Giá bắt buộc phải nhập';
    }

    // validate mô tả
    if (empty($filter['description'])) {
        $errors['description']['require'] = 'Mô tả khóa học bắt buộc phải nhập';
    }


    if (empty($errors)) {

        // Xử lý thumbnail lên
        $uploadDir = './templates/upload/'; // upload ảnh lên đâu
        if (!file_exists($uploadDir)) { // kiểm tra thư mục này đã tồn tại hay chưa
            mkdir($uploadDir, 0777, true); //Tạo mới thư mục upload nếu chưa có
        }
        $fileName = basename($_FILES['thumbnail']['name']);
        $targetFile =  $uploadDir . time() . '-' . $fileName; // thêm time() để tránh bị trùng lặp tên ảnh
        $thumb = "";
        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        if ($checkMove) {
            $thumb = $targetFile;
        }

        print_r($fileName);

        $dataInsert = [
            'name' => $filter['name'],
            'slug' => $filter['slug'],
            'price' => $filter['price'],
            'description' => $filter['description'],
            'thumbnail' => $thumb,
            'category_id' => $filter['category_id'],
            'created_at' => date("Y-m-d H:i:s")
        ];
        $insertStatus = insert('course', $dataInsert);
        if ($insertStatus) {
            setSessionFlash('msg', 'Thêm khóa học thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Thêm khóa học thất bại.');
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
    <h2>Thêm mới khóa học</h2>
    <hr>
    <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-6">
                <label for="name">Tên khóa học</label>
                <input type="text" id="name" name="name" value="<?php oldData($oldData, 'name') ?>" class="form-control"
                    placeholder="Tên khóa học">
                <?php displayErrors($errorArr, 'name') ?>
            </div>
            <div class="col-6">
                <label for="slug">Đường dẫn</label>
                <input type="text" id="slug" name="slug" value="<?php oldData($oldData, 'slug') ?>" class="form-control"
                    placeholder="Đường dẫn">
                <?php displayErrors($errorArr, 'slug') ?>
            </div>
            <div class="col-6">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" value="<?php oldData($oldData, 'description') ?>"
                    class="form-control" placeholder="Mô tả">
                <?php displayErrors($errorArr, 'description') ?>
            </div>
            <div class="col-6">
                <label for="price">Giá</label>
                <input type="text" name="price" value="<?php oldData($oldData, 'price') ?>" class="form-control"
                    placeholder="Giá">
                <?php displayErrors($errorArr, 'price') ?>
                <img src="" id="previewImage" class="previewImage" style="display:none" alt="">
            </div>
            <div class="col-6">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control" placeholder="Thumbnail">
                <?php displayErrors($errorArr, 'thumbnail') ?>
            </div>
            <div class="col-3">
                <label for="group">Lĩnh vực</label>
                <select name="category_id" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("SELECT * from  `course_category`");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id'] ?>">
                            <?php echo $item['name'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Xác nhận gửi</button>
        </div>
    </form>
</div>
<?php layout('footer'); ?>