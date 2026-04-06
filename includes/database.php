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
// Đếm số lượng dòng
function getRows($sql)
{
    global $conn;
    $stm = $conn->prepare($sql); //prepare : đọc dữ liệu từ câu lệnh sql
    $stm->execute();
    $result = $stm->rowCount(); //rowCount trả về số lượng dòng
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

//Insert dữ liệu
function insert($table, $data)
{
    global $conn;
    $keys = array_keys($data); //array_keys lấy ra các key trong mảng $data
    $cot = implode(',', $keys); // implode chuyển mảng thành chuỗi
    $place = ":" . implode(',:', $keys);
    $sql = "INSERT INTO $table($cot) values($place)";
    $stm = $conn->prepare($sql);
    // Data cần insert
    $rel = $stm->execute($data);
    return $rel;
}

//Update dữ liệu
function update($table, $data, $condition = '')
{
    global $conn;
    $update = '';
    foreach ($data as $key => $value) {
        $update .= $key . '=:' . $key . ',';
    };
    $update = trim($update, ','); // hàm trim giúp bỏ dấu phẩy ở cuối 

    if (!empty($condition)) {
        $sql = "update $table set $update where $condition";
    } else {
        $sql = "update $table set $update";
    }
    $tmp = $conn->prepare($sql);
    $rel = $tmp->execute($data);
    return $rel;
}

//Xóa dữ liệu
function delete($table, $condition = '')
{
    global $conn;
    if (!empty($condition)) {
        $sql = "DELETE  from $table where $condition";
    } else {
        $sql = "DELETE from $table";
    }
    $stm = $conn->prepare($sql);
    $rel = $stm->execute();
    return $rel;
}

// Hàm lấy ID mới insert
function lastId()
{
    global $conn;
    return $conn->lastInsertId();
}
