<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}
require_once './includes/connect.php';
function getAll($sql)
{
    global $conn;
    $stm = $conn->prepare($sql); //prepare : đọc dữ liệu từ câu lệnh sql
    $stm->execute();
    $result = $stm->fetchAll(PDO::FETCH_ASSOC); //fetchAll trả về tất cả dữ liệu từ db
    return $result;
}
function getOne($sql)
{
    global $conn;
    $stm = $conn->prepare($sql); //prepare : đọc dữ liệu từ câu lệnh sql
    $stm->execute();
    $result = $stm->fetch(PDO::FETCH_ASSOC); //fetch trả về 1 dòng dữ liệu từ db
    return $result;
}