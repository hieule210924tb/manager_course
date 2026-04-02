<?php

date_default_timezone_set('Asia/Ho_Chi_Minh'); //Thiết lập múi giờ VN
session_start(); // Tạo mới 1 phiên làm việc
ob_start(); //tránh th bị lỗi

require_once 'config.php';
// require_once './modules/auth/login.php';