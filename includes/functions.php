<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}


function layout($layoutName, $data = [])
{
    if (file_exists(_PATH_URL_TEMPLATES . '/layout/' . $layoutName . '.php')) { // kiểm tra xem có tồn tại đường dẫn đó không
        require_once _PATH_URL_TEMPLATES . '/layout/' . $layoutName . '.php';
    }
}