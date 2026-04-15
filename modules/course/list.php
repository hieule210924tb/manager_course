<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Danh sách khóa học'
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
    if (isset($filter['cate'])) {
        $cate = $filter['cate'];
    }
    if (!empty($keyword)) {
        if (strpos($chuoiWhere, 'WHERE') == false) { //strpos kiểm tra chuỗi trong chuỗi, kiểm tra xem có where chưa
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "a.name LIKE '%$keyword%' or a.description LIKE '%$keyword%'";
    }

    if (!empty($cate)) {
        if (strpos($chuoiWhere, 'WHERE') == false) { //strpos kiểm tra chuỗi trong chuỗi, kiểm tra xem có where chưa
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= " a.category_id = $cate ";
    }
}
// Xử lý phân trang
// Lấy tổng dữ liệu có trong users
$maxData = getRows("
    SELECT a.id 
    FROM course a
    INNER JOIN course_category b 
    ON a.category_id = b.id
    $chuoiWhere
");
$perPage = 1; // cấu hình 1 trang mấy dòng
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

$getDetailUser = getAll("SELECT a.id, a.name , a.price, a.created_at, a.thumbnail , b.name as name_cate
from course  a
inner join `course_category` b 
on a.category_id = b.id  $chuoiWhere
order by a.created_at desc
limit $offset, $perPage
");

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
        <a href="?module=users&action=add" class="btn btn-success mb-3"><i class=" fa-solid fa-plus"></i>Thêm mới khóa
            học</a>
        <?php if (!empty($msg)) getMsg($msg, $msg_type); ?>
        <form action="" class="mb-3" method="get">
            <input type="hidden" name="module" value="course">
            <input type="hidden" name="action" value="list">
            <div class="row">
                <div class="col-3">
                    <select class="form-select form-control" name='cate' id=''>
                        <option value="">Lĩnh vực</option>
                        <?php
                        $getCate = getAll("SELECT * from course_category");
                        foreach ($getCate as $item): ?>
                            <option value="<?php echo $item['id'] ?>"
                                <?php echo ($cate == $item['id'] ? 'selected' : false) ?>>
                                <?php echo $item['name'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-7">
                    <input type="text" class="form-control" value="<?php echo (!empty($keyword)) ? $keyword : false ?>"
                        name="keyword" placeholder="Nhập thông tin tìm kiếm...">
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Tên khóa học</th>
                    <th scope="col">Thumbnail</th>
                    <th scope="col">Giá</th>
                    <th scope="col">Lĩnh vực</th>
                    <th scope="col">Sửa</th>
                    <th scope="col">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($getDetailUser as $key => $item) : ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1 ?></th>
                        <td><?php echo $item['name'] ?></td>
                        <td><img width="180px" src="<?php echo $item['thumbnail'] ?>" alt=""></td>
                        <td><?php echo $item['price'] ?></td>
                        <td><?php echo $item['name_cate'] ?></td>
                        <td><a href="?module=course&action=edit&id=<?php echo $item['id'] ?>" class="btn btn-warning"><i
                                    class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="?module=course&action=delete&id=<?php echo $item['id'] ?> "
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
<?php
layout('footer') ?>