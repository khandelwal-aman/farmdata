<?php
session_start();

if(isset($_SESSION['user']))
{
    include_once 'db.php';
    
    
    echo <<<_END
<html>
    <head>
        <title>FarmDB</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.js"></script>        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="https://use.fontawesome.com/d1f7bf0fea.js"></script>
    </head>
    
    <body>    
_END;

include_once 'nav.php';

echo <<<_END
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2>Cattle Activity Log</h2>
                    <form action="cattle_activity_log_master_add.php" method="post">
                    <div class="form-group">
                        <label for="cattle">Cattle</label>
                        <select id="cattle" name="cattle" class="form-control">
                        <option value="">--Select Cattle--</option>
_END;
                    $q1="SELECT * from cattle where is_deleted=0";
                    $r1=mysqli_query($db,$q1);
                    while($res1=mysqli_fetch_assoc($r1)){
                        $id=$res1['id'];
                        $name=$res1['name'];
                        echo <<<_END
                        <option value="$id">$name</option>
_END;
                    }
                        echo <<<_END
                        </select>
                        </div>
                        <div class="form-group">
                            <label for="cattle activity">Cattle Activity</label>
                            <select name="cactivity" class="form-control">
                            <option value="">--Select Cattle Activity--</option>
_END;
        $q="SELECT * from cattle_activity where is_deleted=0 order by name asc";
        $r=mysqli_query($db,$q);
        while($res=mysqli_fetch_assoc($r)){
            $id=$res['id'];
            $name=$res['name'];
            echo <<<_END
            <option value="$id">$name</option>
_END;
        }
                                    
        echo <<<_END
                        </select>
                        </div>
                        <div class="form-group">
                            <label>Activity Value</label>
                            <input type="text" name="acvalue" class="form-control"/>
                        </div>
                        <div class="form-group">
                        <label>Comments</label>
                        <input type="text" name="comments" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>Date of Activity</label>
                        <input type="date" name="doa" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Cattle Activity</button>
                        </div>
                    </form>
                </div>
_END;
                echo <<<_END
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Cattle</th>
                                    <th>Cattle Activity</th>
                                    <th>Activity Value</th>
                                    <th>Comments</th>
                                    <th>Activity Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
_END;

$q = "SELECT * FROM cattle_activity_log where is_deleted=0 order by doe desc limit 20";
$r = mysqli_query($db,$q);

while($res = mysqli_fetch_assoc($r))
{
    $sn = $res['id'];
    $c_activity = $res['caid'];
    $doa=$res['doa'];
    $cid=$res['cid'];
    $q1="SELECT name from cattle_activity where id='$c_activity' and is_deleted=0";
    $r1=mysqli_query($db,$q1);
    $re1=mysqli_fetch_assoc($r1);
    $q2="SELECT name from cattle where id='$cid' and is_deleted=0";
    $r2=mysqli_query($db,$q2);
    $re2=mysqli_fetch_assoc($r2);
    $cattle=$re2['name'];
    $ct_activity=$re1['name'];
    $acvalue=$res['activity_value'];
    $comments=$res['comments'];

    echo <<<_END
    <tr>
        <td>$sn</td>
        <td>$cattle</td>
        <td>$ct_activity</td>
        <td>$acvalue</td>
        <td>$comments</td>
        <td>$doa</td>
        <td><a href="delete.php?table=cattle_activity_log&rid=$sn&return=cattle_activity_log_master">Delete</a> | <a href="cattle_view.php?vid=$sn">View cattle report</a></td>
    </tr>
_END;
}

echo <<<_END
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>

_END;

include_once 'foot.php';

echo <<<_END
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
      $('#cattle').select2();
    });
</script>
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
