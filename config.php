<?php
const _HIEU = true; //Kiểm tra việc truy cập có hợp lệ hay không


const _MODULES = 'dashboard'; // 'dashboard/index' lấy ra để khi vào trang web, vào trang nào đầu tiên
const _ACTION = 'index';

//Khai báo database
const _HOST = 'localhost';
const _DB = 'manager_course';
const _USER = 'root';
const _PASS = '';
const _DRIVER = 'mysql';

//debug error
const _DEBUG = true; // nếu nó bị lỗi có để nó hiển thị ra hay ko, true : bật lên , false: tắt đi

//Thiết lập host
define('_HOST_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/manager_course');
define('_HOST_URL_TEMPLATES', _HOST_URL . '/templates');
// echo _HOST_URL_TEMPLATES;

//Thiết lập đến PATH
define('_PATH_URL', __DIR__);
define('_PATH_URL_TEMPLATES', _PATH_URL . '/templates');
// echo _PATH_URL_TEMPLATES;