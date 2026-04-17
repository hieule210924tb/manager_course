<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Danh sách lĩnh vực'
];
layout('header', $data);
layout('sidebar');
$filter = filterData();
$chuoiWhere = '';
$cate = '0';
$keyword = '';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }

    if (!empty($keyword)) {
        if (strpos($chuoiWhere, 'WHERE') == false) { //strpos kiểm tra chuỗi trong chuỗi, kiểm tra xem có where chưa
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "name LIKE '%$keyword%' or slug LIKE '%$keyword%'";
    }
}
// Xử lý phân trang
// Lấy tổng dữ liệu có trong users
$maxData = getRows("SELECT id FROM course_category");
$perPage = 3; // cấu hình 1 trang mấy dòng
$maxPage = ceil($maxData / $perPage);
$offset = 0;
$page = 1;
//get page
if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 1) {
    $page = 1;
};
$offset = ($page - 1) * $perPage;

$getDetailCate = getAll("SELECT *
from course_category  $chuoiWhere
limit $offset, $perPage
");
if (!empty($keyword)) {
    $maxData = getRows("SELECT * from course_category  $chuoiWhere");
    $maxPage = ceil($maxData / $maxPage);
}
// Xử lý query
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page=' . $page, '', $queryString); // Xóa bỏ page lặp lại trên URL
}


$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>
<div class="container mt-4">
    <div class="container-fluid mt-3">
        <!-- <a href="?module=course&action=add" class="btn btn-success mb-3"><i class=" fa-solid fa-plus"></i>Thêm mới khóa
            học</a> -->
        <div class="row">
            <div class="col-6">
                <h2>Thêm mới hoặc chỉnh sửa</h2>
            </div>
            <div class="col-6">
                <h2>Danh sách lĩnh vực</h2>
                <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
                <form action="" class="mb-3" method="get">
                    <input type="hidden" name="module" value="course_category">
                    <input type="hidden" name="action" value="list">
                    <div class="row">
                        <div class="col-9">
                            <input type="text" class="form-control"
                                value="<?php echo (!empty($keyword)) ? $keyword : false ?>" name="keyword"
                                placeholder="Nhập thông tin tìm kiếm...">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Tên</th>
                            <th scope="col">Thời gian</th>
                            <th scope="col">Sửa</th>
                            <th scope="col">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($getDetailCate as $key => $item) : ?>
                        <tr>
                            <th scope="row"><?php echo $key + 1 ?></th>
                            <td><?php echo $item['name'] ?></td>
                            <td><?php echo $item['created_at'] ?></td>
                            <td><a href="?module=course_category&action=edit&id=<?php echo $item['id'] ?>"
                                    class="btn btn-warning"><i class="fa-solid fa-pencil"></i></a></td>
                            <td><a href="?module=course_category&action=delete&id=<?php echo $item['id'] ?> "
                                    onclick='return confirm("Bạn có chắc chắn muốn xóa <?php echo $item["name"] ?> không ?" )'
                                    class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination d-flex justify-content-center">
                        <!-- Xử lý nút trước -->
                        <?php if ($page > 1) : ?>
                        <li class='page-item'><a class='page-link'
                                href="?<?php echo $queryString; ?>&page=<?php echo $page - 1 ?>">Trước </a></li>
                        <?php endif ?>
                        <!-- Tính vị trí bắt đầu -->
                        <?php $start = $page - 1;
                        if ($start < 1) {
                            $start = 1;
                        }

                        ?>
                        <?php if ($start > 1) : ?>
                        <li class='page-item'><a class='page-link'
                                href="?<?php echo $queryString; ?>&page=<?php echo $page - 1 ?>">... </a></li>
                        <?php endif;
                        $end = $page + 1;
                        if ($end > $maxPage) {
                            $end = $maxPage;
                        }
                        ?>
                        <?php
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : '' ?>"><a class="page-link"
                                href="?<?php echo $queryString; ?>&page=<?php echo $i ?>"><?php echo $i ?></a></li>
                        <?php
                        endfor;
                        if ($end < $maxPage) : ?>
                        <li class='page-item'><a class='page-link'
                                href="?<?php echo $queryString; ?>&page=<?php echo $page + 1 ?>">... </a></li>
                        <?php endif; ?>
                        <!-- Xử lý nút sau -->
                        <?php if ($page  < $maxPage) : ?>
                        <li class='page-item'><a class='page-link'
                                href="?<?php echo $queryString; ?>&page=<?php echo $page + 1 ?>">Sau </a></li>
                        <?php endif ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php
layout('footer') ?>