<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
$filter = filterData("GET");
if (!empty($filter)) {
    $course_id  = $filter['id'];

    $checkCourse = getOne("SELECT *  from course where id = '$course_id'");

    if (!empty($checkCourse)) {
        $condition = "id =" . $course_id;
        $checkDelete = delete('course', $condition);
        if ($checkCourse) {
            setSessionFlash('msg', 'Bạn đã xóa khóa học thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        }
    } else {
        setSessionFlash('msg', 'Bạn đã xóa khóa học thất bại.');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=course&action=list');
    }
} else {
    setSessionFlash('msg', 'Bạn có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'danger');
}
