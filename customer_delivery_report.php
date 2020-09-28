<?php
session_start();
function getDimensionValue($db,$table,$gid,$name){
    $q = "SELECT * FROM $table WHERE id=$gid";
    $r = mysqli_query($db,$q);
    $res = mysqli_fetch_assoc($r);
    $value = $res[$name];
    return $value;
}
if(isset($_SESSION['user'])){
    include_once 'db.php';

    echo <<<_END
<html>
    <head>
        <title>FarmDB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="https://use.fontawesome.com/d1f7bf0fea.js"></script>
        <style>
        @media print { 
            header,#report { 
               display:none; 
            } 
         } 
         </style>
    </head>
    
    <body>    
_END;

include_once 'nav.php';
    echo <<<_END
        <div class="container">
        <div class="row">
        <div class="col-lg-12" id="report">
        <h3 class="mb-4">Customer Delivery Report</h3>
        <form action="customer_delivery_report.php" method="get">
                        <div class="row">
                            <div class="col-lg">
                                <input type="date" class="form-control" name="start_date">
                            </div>
                            <div class="col-lg">
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Show Report</button>
                    </form>
                    </div>
_END;
if(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date']!='' && $_GET['end_date']!='')
{
    $start_date = mysqli_real_escape_string($db,$_GET['start_date']);
    $end_date = mysqli_real_escape_string($db,$_GET['end_date']);

    $q="SELECT t.cid,t.delivery_time,cast(t.dod as date) as d,COALESCE(t.CowMilk,0) as cow_milk, COALESCE(t.Sahiwal,0) as sahiwal_milk, COALESCE(t.buffalo,0) as buffalo_milk from (SELECT cd.id,cd.cid,cs.delivery_time,cd.dod,case when cs.milktype=1 then cd.delivered_qty end as CowMilk ,case when cs.milktype=2 then cd.delivered_qty end as Sahiwal ,case when cs.milktype=3 then cd.delivered_qty end as buffalo FROM customer_delivery_log cd INNER JOIN customer_subscription cs on cs.id=cd.csid where cs.is_active=1 and cs.is_deleted=0 and cast(cd.dod as date)>='$start_date' and cast(cd.dod as date)<='$end_date') as t";
    $r=mysqli_query($db,$q);
    $sdt=date("d-m-Y", strtotime($start_date));
        $edt=date("d-m-Y", strtotime($end_date));
$date='';
echo <<<_END
<div class="col-lg-12">
<div class="row">
<h4 class="mb-4">From $sdt to $edt</h4>
<button class="btn btn-primary" style="position: absolute;right:10;" onclick="window.print()">Print Report</button>
</div>
</div>
_END;
    if(mysqli_num_rows($r)>0){
        echo <<<_END
        <div class="table table-responsive">
        <table class="table table-bordered">
        <tr>
        <th>Date</th>
        <th>Customer</th>
        <th>Cow</th>
        <th>Sahiwal</th>
        <th>Buffalo</th>
_END;
    while($res=mysqli_fetch_assoc($r)){
        $cid=$res['cid'];
        $d=$res['d'];
        $qty=$res['cow_milk'];
        $qty1=$res['sahiwal_milk'];
        $qty2=$res['buffalo_milk'];
        $cust=getDimensionValue($db,'customer',$res['cid'],'fname');
            if($d!=$date){
                echo <<<_END
                <tr>
                <th>$d</th>
            
_END;
            }
            echo <<<_END
            <td>$cust</td>
            <td>$qty</td>
            <td>$qty1</td>
            <td>$qty2</td>
            </tr>
_END;
           }
    }
    else {
        echo 'No deliveries found';
    }
    echo <<<_END
    </tbody>
</table>
</div>
</div>
</div>
_END;

include_once 'foot.php';

echo <<<_END
    </body>
</html>
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