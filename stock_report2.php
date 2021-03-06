<?php
session_start();
function getDimensionValue($db, $table, $gid, $name)
{
    $q = "SELECT * FROM $table WHERE id=$gid";
    $r = mysqli_query($db, $q);

    $res = mysqli_fetch_assoc($r);

    $value = $res[$name];

    return $value;
}
if (isset($_SESSION['user'])) {
    include_once 'db.php';
    echo <<<_END
    <html>
        <head>
            <title>FarmDB</title>
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css"/>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css"/>            
            <script src="https://use.fontawesome.com/d1f7bf0fea.js"></script>      
        </head>
        
        <body>    
_END;
    include_once 'nav.php';
    echo <<<_END
        <div class="container">
        <div class="row">
                <div class="col-lg-12">
                    <h2 class="h2">Inventory</h2><br>
                    <form action="stock_report2.php" method="get">
                            <div class="row">
                            <div class="col-lg">
                                <input type="date" class="form-control" name="start_date">
                            </div>
                            <div class="col-lg">
                            <input type="date" class="form-control" name="end_date">
                        </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Show inventory</button>
                    </form>
                </div>
                </div>
                </div>
_END;
    if (isset($_GET['start_date']) && $_GET['start_date'] != '' && isset($_GET['end_date']) && $_GET['end_date'] != '') {
        $start_date = mysqli_real_escape_string($db, $_GET['start_date']);
        $end_date = mysqli_real_escape_string($db, $_GET['end_date']);
        echo <<<_END
        <div class="container">
        <div class="row">
        <div class="col-lg-12">
        <div class="table table-responsive">
        <table id="table" class="table table-bordered">
        <thead>
        <tr>
        <th>Sno.</th>
        <th>Resource</th>
        <th>Opening Stock</th>
        <th>Consumed</th>
        <th>Purchased</th>
        <th>Produced</th>
        <th>Closing</th>
        </tr>
        </thead>
        <tbody>
_END;
        $produced1 = 0;
        $consumed1 = 0;
        $purchase1 = 0;
        $q = "SELECT id, resourcename,unit from resources where is_deleted=0";
        $r = mysqli_query($db, $q);
        while ($res = mysqli_fetch_assoc($r)) {
            $id = $res['id'];
            $resource = $res['resourcename'];
            $unit = $res['unit'];
            echo <<<_END
        <tr>
        <td>$id</td>
        <td>$resource</td>      
_END;
        
            $q5 = "SELECT r.id, a.resourceid,b.resourceid,c.resourceid,sum(a.produced) as produced, sum(b.consumed) as con, sum(c.purchase) as pur  from resources r
            LEFT JOIN (select resourceid,sum(qty) as produced from log_output where logid in (SELECT id FROM `logs` where cast(doe as date)>='$start_date' and TIMESTAMP(cast(doe as date))<='$end_date' and is_deleted=0) and is_deleted=0 GROUP BY resourceid) a on r.id = a.resourceid
            LEFT JOIN (select resourceid,sum(qty) as consumed from log_resource where  logid in (SELECT id FROM `logs` where cast(doe as date)>='$start_date' and TIMESTAMP(cast(doe as date))<='$end_date' and is_deleted=0) and is_deleted=0 GROUP BY resourceid) b on r.id = b.resourceid
            LEFT JOIN (select resourceid,sum(qty) as purchase from purchase_items where TIMESTAMP(cast(doe as date))>='$start_date' and TIMESTAMP(cast(doe as date))<='$end_date' and is_deleted=0 GROUP BY resourceid) c on r.id = c.resourceid where id='$id'";

            $r1 = mysqli_query($db, $q5);
            if (isset($r1)) {
                while ($res1 = mysqli_fetch_assoc($r1)) {
                    $produced = $res1['produced'];
                    $consumed = $res1['con'];
                    $purchase = $res1['pur'];
                    $produced1 += $produced;
                    $consumed1 += $consumed;
                    $purchase1 += $purchase;
                    $opening = ($produced + $purchase) - $consumed;
                    //$extra = $opening + $produced1;
                    echo <<<_END

                    <td>$opening</td>
                <td>$consumed $unit</td>
            <td>$purchase $unit</td>
            <td>$produced $unit</td>
            <td>$opening $unit</td>
            </tr> 
_END;
                }
            }


            if (date("Y/m/d") == $start_date) {
                //use for opening
                $q6 = "SELECT CURDATE()-1,r.id, a.resourceid,b.resourceid,c.resourceid,sum(a.produced) as produced, sum(b.consumed) as con, sum(c.purchase) as pur  from resources r
            LEFT JOIN (select resourceid,sum(qty) as produced from log_output where logid in (SELECT CURDATE()-1,id FROM `logs` where  is_deleted=0) and is_deleted=0 GROUP BY resourceid) a on r.id = a.resourceid
            LEFT JOIN (select resourceid,sum(qty) as consumed from log_resource where  logid in (SELECT CURDATE()-1,id FROM `logs` where  is_deleted=0) and is_deleted=0 GROUP BY resourceid) b on r.id = b.resourceid
            LEFT JOIN (select CURDATE()-1,resourceid,sum(qty) as purchase from purchase_items where is_deleted=0 GROUP BY resourceid) c on r.id = c.resourceid where id='$id'";

                $r2 = mysqli_query($db, $q6);
                while ($res1 = mysqli_fetch_assoc($r2)) {
                    $produced = $res1['produced'];
                    $consumed = $res1['con'];
                    $purchase = $res1['pur'];
                    $opening = ($produced + $purchase) - $consumed;
                    $closing = ($opening + $produced + $purchase) - $consumed;
                    echo <<<_END
                    <tr>
                <td>$opening</td>
                <td>$consumed $unit</td>
                <td>$purchase $unit</td>
                <td>$produced $unit</td>
                <td>$closing $unit</td>
                </tr> 
_END;
                    //use for closing
                }
            }
        }
        echo <<<_END
   
_END;
    } else {
        echo 'No items';
    }



    include_once 'foot.php';
    echo <<<_END
    
            </tbody>
        </table>
        </div>
        </div>
        </div>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
$('#table').DataTable({
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
});
});
</script> 
        </body>
        </html>
_END;
} else {
    $msg = "Please Login";
    echo <<<_END
    <meta http-equiv='refresh' content='0;url=index.php?msg=$msg'>
_END;
}
