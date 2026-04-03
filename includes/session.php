<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}

//set session
function setSession($key, $value)
{
    if (!empty(session_id())) { //session_id() trả về id của session hiện tại
        $_SESSION[$key] = $value;
        return true;
    }

    return false;
}

//Lấy dữ liệu từ session
function getSession($key = "")
{
    if (empty($key)) {
        return $_SESSION;
    } else {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    return false;
}

//Xóa session
function removeSession($key)
{
    if (empty($key)) {
        session_destroy($key);
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return true;
    }

    return false;
}

//Tạo session flash
function setSessionFlash($key, $value)
{
    $key = $key . 'Flash';
    $rel = setSession($key, $value);
    return $rel;
}

//Lấy ra value session flash  rồi xóa key session flash
function getSessionFlash($key)
{
    $key = $key . 'Flash';
    $rel = getSession($key);
    removeSession($key);
    return $rel;
}
