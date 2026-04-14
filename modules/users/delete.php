<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$getData = filterData('GET'); // lấy id từ url 
if (!empty($getData['id'])) { // kiểm tra xem id đó có dữ liệu ko
    $userId = $getData['id'];
    $checkUser = getRows("SELECT * from users where id ='$userId'");
    if ($checkUser > 0) {
        //Xóa tài khoản
        $checkToken = getRows("SELECT * from token_login where user_id = '$userId'");
        if ($checkUser > 0) { // Kiểm tra xem tài khoản đó có tồn tại trong token_login ko
            delete('token_login', "user_id ='$userId'");
        }
        $checkDelete = delete('users', "id='$userId'");
        if ($checkDelete) {
            setSessionFlash('msg', 'Bạn đã xóa tài khoản thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Xóa người dùng thất bại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Người dùng không tồn tại.');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=users&action=list');
    }
}
