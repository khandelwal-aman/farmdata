<?php
session_start();

if(isset($_SESSION['user']))
{
    include_once 'db.php';
    if(isset($_POST['qty']) && $_POST['qty']!='' && isset($_POST['dlqty']) && $_POST['dlqty']!='' && isset($_POST['csid']) && isset($_POST['cid']))
    {
        $qty = mysqli_real_escape_string($db,$_POST['qty']);
        $dlqty = mysqli_real_escape_string($db,$_POST['dlqty']);
        $cid = mysqli_real_escape_string($db,$_POST['cid']);
        $csid = mysqli_real_escape_string($db,$_POST['csid']);
        $q = "INSERT INTO customer_delivery_log(qty,delivered_qty,cid,csid) VALUES('$qty','$dlqty','$cid','$csid')";
        $r = mysqli_query($db,$q);
        
        $msg = "Customer delivery log Added";
        echo <<<_END
        <meta http-equiv='refresh' content='0;url=customer_delivery_log.php?msg=$msg'>
_END;
        
    }
    else
    {
        $msg = "Please enter the details";
        echo <<<_END
        <meta http-equiv='refresh' content='0;url=customer_delivery_log.php?msg=$msg'>   
_END;
    }
}
else
{
    $msg = "Please Login";
    echo <<<_END
    <meta http-equiv='refresh' content='0;url=index.php?msg=$msg'>
_END;
}

?>