<?php
    session_start();
    
    function getDimensionValue($db,$table,$gid,$name){
        $q = "SELECT * FROM $table WHERE id=$gid";
        $r = mysqli_query($db,$q);
        
        $res = mysqli_fetch_assoc($r);
        
        $value = $res[$name];
        
        return $value;
    }

    if(isset($_SESSION['user']))
{
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
            header,#report,#btn { 
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
                    <h2 class="h2">Cattle Activity Report</h2><br>
                    <form action="cattle_activity_report.php" method="get">
                        <div class="row">
                            <div class="col-lg">
                                <input type="date" class="form-control" name="start_date">
                            </div>
                            <div class="col-lg">
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-lg-6 mt-2">
                        <select class="form-control" name="caid">
                        <option value="">--select cattle activity--</option>
_END;
$q="SELECT * FROM cattle_activity where is_deleted=0";
$r=mysqli_query($db,$q);
while($res=mysqli_fetch_assoc($r))
{
$id=$res['id'];
$activity=$res['name'];
echo <<<_END
<option value="$id">$activity</option>
_END;
}
echo <<<_END
                    </select>
                    </div>
                    </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Show Report</button>
                    </form>
                </div>
_END;
if(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date']!='' && $_GET['end_date']!='' && isset($_GET['caid']) && $_GET['caid']!='')
{
    $start_date = mysqli_real_escape_string($db,$_GET['start_date']);
    $end_date = mysqli_real_escape_string($db,$_GET['end_date']);
    $cattle_activity = mysqli_real_escape_string($db,$_GET['caid']);
    $q = "SELECT t.id,t.cid,t.caid,cast(t.doa as date) as d,COALESCE(t.milkCollection,0) as milk_collection, COALESCE(t.feeding,0) as 'feeding' from (SELECT id,cid,caid,doa,case when caid=1 then activity_value end as milkCollection ,case when caid=6 then activity_value end as feeding  FROM cattle_activity_log where cast(doa as date)>='$start_date' and  cast(doa as date)<='$end_date' and caid='$cattle_activity' and is_deleted=0) as t order by doa";
    $r=mysqli_query($db,$q);
    $q1="SELECT t.id,t.cid,t.caid,cast(t.doa as date) as d,COALESCE(sum(t.milkCollection),0) as milk_collection, COALESCE(sum(t.feeding),0) as 'feeding' from (SELECT id,cid,caid,doa,case when caid=1 then activity_value end as milkCollection ,case when caid=6 then activity_value end as feeding  FROM cattle_activity_log where cast(doa as date)>='$start_date' and  cast(doa as date)<='$end_date' and caid='$cattle_activity' and is_deleted=0) as t";
    $r1=mysqli_query($db,$q1);
    $res1=mysqli_fetch_assoc($r1);
    $total1=$res1['milk_collection'];
    $total2=$res1['feeding'];

        echo <<<_END
    <div class="col-lg-12">
        <div class="row">
        <h2>Data</h2>
        <button id="btn" style="position: absolute; right:10;" class="btn btn-primary" onclick="window.print()">Print Report</button>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Date</th>
                        <th>Cattle Name</th>
_END;
                        if($cattle_activity==1){
                            echo <<<_END
                        <th>Milk Collection</th>
_END;
                        }
                        elseif($cattle_activity==6) {
                            echo <<<_END
                        <th>feeding(Bhusa+chara+dana)</th>
_END;
                        }
                        
                        echo <<<_END
                    </tr>
                </thead>
                <tbody>
_END;
$date = '';
while($res = mysqli_fetch_assoc($r)){
    $id=$res['id'];
    $cid = $res['cid'];
    $d = $res['d'];
    $dt=date("d-m-Y", strtotime($d));
    $cattle=getDimensionValue($db,'cattle',$res['cid'],'name');
    $cattle_activity_value1=$res['milk_collection'];
            $cattle_activity_value2=$res['feeding'];
    if($d != $date){
        echo <<<_END
        <tr>
            <td>$id</td>
            <td>$dt</td>
            <td>$cattle</td>
_END;
        if($cattle_activity==1){
            echo <<<_END
            <td>$cattle_activity_value1</td>
_END;
        }
        elseif($cattle_activity==6){
            echo <<<_END
            <td>$cattle_activity_value2</td>
_END;
        }
        echo <<<_END
        </tr>
_END;
    }
}
echo <<<_END
<tr>
            <th colspan="3">Total</th>
_END;
            if($cattle_activity==1){
            echo <<<_END
            <th>$total1</th>
_END;
            }
            elseif($cattle_activity==6){
                echo <<<_END
            <th>$total2</th>
_END;
            }
            echo <<<_END
            </tr>
_END;
    echo <<<_END
                </tbody>
            </table>
        </div>
    </div>    
    </div>    
_END;
    
}

elseif(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date']!='' && $_GET['end_date']!='')
{
    $start_date = mysqli_real_escape_string($db,$_GET['start_date']);
    $end_date = mysqli_real_escape_string($db,$_GET['end_date']);
    
    $q = "SELECT t.id,t.cid,t.caid,cast(t.doa as date) as d,COALESCE(sum(t.milkCollection),0) as milk_collection, COALESCE(sum(t.feeding),0) as 'feeding' from (SELECT id,cid,caid,doa,case when caid=1 then activity_value end as milkCollection ,case when caid=6 then activity_value end as feeding  FROM cattle_activity_log where cast(doa as date)>='$start_date' and  cast(doa as date)<='$end_date' and is_deleted=0) as t group by t.cid order by doa";
    $r=mysqli_query($db,$q);
    $q1="SELECT t.id,t.cid,t.caid,cast(t.doa as date) as d,COALESCE(sum(t.milkCollection),0) as milk_collection, COALESCE(sum(t.feeding),0) as 'feeding' from (SELECT id,cid,caid,doa,case when caid=1 then activity_value end as milkCollection ,case when caid=6 then activity_value end as feeding  FROM cattle_activity_log where cast(doa as date)>='$start_date' and  cast(doa as date)<='$end_date' and is_deleted=0) as t";
    $r1=mysqli_query($db,$q1);
    $res1=mysqli_fetch_assoc($r1);
    $total1=$res1['milk_collection'];
    $total2=$res1['feeding'];
    $date='';
        echo <<<_END
    <div class="col-lg-12">
        <div class="row">
        <h2>Data</h2>
        <button style="position: absolute; right:10;" class="btn btn-primary" onclick="window.print()">Print Report</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Date</th>
                        <th>Cattle Name</th>
                        <th>Milk Collection</th>
                        <th>Feeding(Bhusa+chara+dana)</th>
                    </tr>
                </thead>
                <tbody>
_END;
        while($res=mysqli_fetch_assoc($r)){
            $id=$res['id'];
            $d=$res['d'];
            $cattle=getDimensionValue($db,'cattle',$res['cid'],'name');
            $cattle_activity_value1=$res['milk_collection'];
            $cattle_activity_value2=$res['feeding'];
            $cattle_activity=getDimensionValue($db,'cattle_activity',$res['caid'],'name');
            if($d!=$date){
                echo <<<_END
                <tr>
                <td>$id</td>
                <td>$d</td>
                <td>$cattle</td>
                <td>$cattle_activity_value1</td>
                <td>$cattle_activity_value2</td>
                </tr>
_END;
            }
        }
        echo <<<_END
        <tr>
                    <th colspan="3">Total</th>
                    <th>$total1</th>
                    <th>$total2</th>
                    </tr>
_END;

    echo <<<_END
                </tbody>
            </table>
        </div>
    </div>        
_END;
}

else{
    echo <<<_END
    <p>Add fields are required</p>
_END;
}

echo <<<_END
            </div>
        </div>

_END;

include_once 'foot.php';

echo <<<_END
    </body>
</html>
_END;
}
else
{
    $msg = "Please Login";
    echo <<<_END
    <meta http-equiv='refresh' content='0;url=index.php?msg=$msg'>
_END;
}

?>