<?php 
    
    if (!isset($_SESSION['admin_email'])) {
        
        echo "<script>window.open('login.php','_self')</script>";
        
    } else {

?>

<?php
    if (isset($_GET['invoice_no'])) {
        $invoice_no = $_GET['invoice_no'];
    }

    if (isset($_GET['confirm_yes'])) {
        $confirm_product_id = $_GET['confirm_yes'];
        $order_status = "Complete";
        $update_customer_order = "update customer_orders set order_status='$order_status' where order_id='$confirm_product_id'";
        $row_update_customer_order = mysqli_query($conn, $update_customer_order);

        if ($row_update_customer_order) {
            echo "<script>alert('Bạn đã xác nhận sản phẩm này đã xác nhận')</script>";
            echo "<script>window.open('index.php?view_order_detail=$invoice_no','_self')</script>";
        }
        
    }
?>
<?php } ?>