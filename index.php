<?php

date_default_timezone_set('Asia/Ho_Chi_Minh'); //Thiết lập múi giờ VN
session_start(); // Tạo mới 1 phiên làm việc
ob_start(); //tránh th bị lỗi

require_once 'config.php';
require_once './includes/connect.php';
require_once './includes/database.php';

$module = _MODULES;
$action = _ACTION;
if (!empty($_GET['module'])) {
    $module = $_GET['module']; //Nếu có dữ liệu mới, thì gián dữ liệu mới vào $module
}
if (!empty($_GET['action'])) {
    $action = $_GET['action']; //Nếu có dữ liệu mới, thì gián dữ liệu mới vào $action
}

$path = 'modules/' . $module . '/' . $action . '.php';
if (!empty($path)) {
    if (file_exists($path)) {
        require_once($path);
    } else {
        require_once './modules/errors/404.php';
    }
} else {
    require_once './modules/errors/500.php';
};
$data = [
    'name' => 'Hùng Ngọc',
    'slug' => 'Hung-Ngoc',
];
$rel = insert('course_category', $data);
echo '<pre>';
print_r($rel);
echo '</pre>';