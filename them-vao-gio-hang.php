<?php
session_start();

if (isset($_GET['ma_san_pham'])) {
    $productId = $_GET['ma_san_pham'];
    
    // Kết nối tới cơ sở dữ liệu
    include "config.php";

    // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
    $query = "SELECT * FROM giohang WHERE ma_san_pham = '$productId'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Sản phẩm đã tồn tại trong giỏ hàng, cập nhật số lượng
        $row = mysqli_fetch_assoc($result);
        $currentQuantity = $row['so_luong'];
        $newQuantity = $currentQuantity + 1;

        // Cập nhật số lượng
        $updateQuery = "UPDATE giohang SET so_luong = $newQuantity WHERE ma_san_pham = '$productId'";
        mysqli_query($conn, $updateQuery);

        // Tính thành tiền dựa trên số lượng mới
        // Lấy giá sản phẩm từ cơ sở dữ liệu
        $query_sp = "SELECT * FROM sanpham WHERE ma_san_pham = '$productId'";
        $result_sp = mysqli_query($conn, $query_sp);
        if ($row_sp = mysqli_fetch_assoc($result_sp)) {
            $gia = $row_sp['gia'];
            $newthanhTien = $gia * $newQuantity;
            $updateThanhTienQuery = "UPDATE giohang SET thanh_tien = $newthanhTien WHERE ma_san_pham = '$productId'";
            mysqli_query($conn, $updateThanhTienQuery);
        }
    } else {
        // Sản phẩm chưa tồn tại trong giỏ hàng, thêm mới
        $insertQuery = "INSERT INTO giohang (ma_san_pham, so_luong) VALUES ('$productId', 1)";
        mysqli_query($conn, $insertQuery);

        // Lấy giá sản phẩm từ cơ sở dữ liệu
        $query_sp = "SELECT * FROM sanpham WHERE ma_san_pham = '$productId'";
        $result_sp = mysqli_query($conn, $query_sp);
        if ($row_sp = mysqli_fetch_assoc($result_sp)) {
            $gia = $row_sp['gia'];
            $thanhTien = $gia;
            $updateThanhTienQuery = "UPDATE giohang SET thanh_tien = $thanhTien WHERE ma_san_pham = '$productId'";
            mysqli_query($conn, $updateThanhTienQuery);
        }
    }

    // Đóng kết nối với cơ sở dữ liệu
    mysqli_close($conn);

    echo 'success';
} else {
    echo 'error';
}
?>
