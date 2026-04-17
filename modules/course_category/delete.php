<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$filter = filterData("GET");
if (!empty($filter)) {
    $cateId = $filter['id'];
    $checkCate = getOne("SELECT * from course_category where id ='$cateId'");
    if (!empty($checkCate)) {
        //Kiểm tra trong bảng course
        $checkCourse = getRows("SELECT * from course where category_id ='$cateId'");
        if ($checkCourse > 0) {
            //Còn tồn tại khóa học của lĩnh vực này
            setSessionFlash('msg', 'Lĩnh vực đang có khóa học.');
            setSessionFlash('msg_type', 'danger');
            redirect("?module=course_category&action=list");
        } else {
            //Nếu không có khóa học -> Xóa 
            $condition = "id=" . $cateId;
            $deleteStatus = delete("course_category", $condition);
            if ($deleteStatus) {
                setSessionFlash('msg', 'Bạn đã xóa thành công.');
                setSessionFlash('msg_type', 'success');
                redirect("?module=course_category&action=list");
            } else {
                setSessionFlash('msg', 'Bạn đã xóa thất bại.');
                setSessionFlash('msg_type', 'danger');
            }
        }
    } else {
        setSessionFlash('msg', 'Danh mục không tồn tại.');
        setSessionFlash('msg_type', 'danger');
        redirect("?module=course_category&action=list");
    }
} else {
    setSessionFlash('msg', 'Danh mục không tồn tại.');
    setSessionFlash('msg_type', 'danger');
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorArr = getSessionFlash('errors');
?>





<!-- 
-Lấy id trên url
-Kiểm tra trong bảng course_category có cái lĩnh vực có id này không
- Có -> check trong bảng course xem có khóa học  nào đang có category_id này không
-Có khóa học  -> Lĩnh vực này đang còn khóa học
-Nếu không có khóa học -> Xóa luôn đi
-->