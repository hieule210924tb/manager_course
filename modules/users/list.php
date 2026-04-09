<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Danh sách tài khoản'
];
layout('header', $data);
layout('sidebar')
?>
<div class="container mt-4">
    <div class="container-fluid mt-3">
        <a href="?module=users&action=add" class="btn btn-success mb-3"><i class=" fa-solid fa-plus"></i>Thêm mới người
            dùng</a>
        <form action="" class="mb-3" method="get">
            <div class="row">
                <div class="col-3">
                    <select class="form-select form-control" name='' id=''>
                        <option value="">Nhóm người dùng</option>
                        <option value="">1</option>
                    </select>
                </div>
                <div class="col-7">
                    <input type="text" class="form-control" placeholder="Nhập thông tin tìm kiếm...">
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
                    <th scope="col">Họ tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Nhóm </th>
                    <th scope="col">Ngày đăng kí</th>
                    <th scope="col">Phân quyền</th>
                    <!-- <th colspan="2" scope="col">Chức năng</th> -->
                    <th scope="col">Sửa</th>
                    <th scope="col">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                    <td>@mdo</td>
                    <td><a href="" class="btn btn-primary">Phân quyền</a></td>
                    <td><a href="" class="btn btn-warning"><i class="fa-solid fa-pencil"></i></a></td>
                    <td><a href="" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                </tr>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination d-flex justify-content-center">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>
</div>
<?php
layout('footer') ?>