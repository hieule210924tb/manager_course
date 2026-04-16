<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Chỉnh sửa khóa học'
];
layout('header', $data);
layout('sidebar');
$getData = filterData("GET");
$course_id = $getData['id'];
$courseData = getOne("SELECT * from course where id = '$course_id'");
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
        $dataUpdate = [
            'name' => $filter['name'],
            'slug' => $filter['slug'],
            'price' => $filter['price'],
            'description' => $filter['description'],
            'category_id' => $filter['category_id'],
            'updated_at' => date("Y-m-d H:i:s")
        ];

        if (!empty($_FILES['thumbnail']['name'])) {
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
            $dataUpdate['thumbnail'] = $thumb;
        }
        $condition = "id =" . $course_id;
        $updateStatus = update('course', $dataUpdate, $condition);
        if ($updateStatus) {
            setSessionFlash('msg', 'Chỉnh sửa khóa học thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Chỉnh sửa khóa học thất bại.');
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
if (!empty($courseData)) {
    $oldData = $courseData;
}
$errorArr = getSessionFlash('errors');
?>
<div class="container px-5">
    <h2>Chỉnh sửa khóa học</h2>
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
            </div>
            <div class="col-6">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control" placeholder="Thumbnail">
                <?php displayErrors($errorArr, 'thumbnail') ?>
                <img src="<?php echo !empty(($oldData['thumbnail'])) ? $oldData['thumbnail'] : false ?>"
                    id="previewImage" class="previewImage mt-3" width="200px" alt="">
            </div>
            <div class="col-3">
                <label for="group">Lĩnh vực</label>
                <select name="category_id" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("SELECT * from  `course_category`");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id'] ?>"
                            <?php echo ($oldData['category_id'] == $item['id']) ? 'selected' : false ?>>
                            <?php echo $item['name'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Xác nhận</button>
        </div>
    </form>
</div>
<script>
    // đoạn js để xử lý xem trước ảnh
    const thumbInput = document.getElementById('thumbnail')
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
<script>
    //Hàm giúp chuyển text thành slug
    function createSlug(strig) {
        return strig.toLowerCase()
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
<?php layout('footer'); ?>