<?php

	include "header.php";


  $cust_list = $obj->con1->prepare("select * from customer_reg ");
  $cust_list->execute();
  $cust_result = $cust_list->get_result();
  $cust_list->close();

  $coup_list = $obj->con1->prepare("select * from coupon ");
  $coup_list->execute();
  $coup_result = $coup_list->get_result();
  $coup_list->close();


if(isset($_REQUEST['btnsubmit']))
{
  //$admin=$_REQUEST['ttId'];
  $admin=1;
  $name = $_REQUEST['customer'];
  $coupon = $_REQUEST['coupon'];
  $count = $_REQUEST['count'];

  try
  {
  $stmt = $obj->con1->prepare("INSERT INTO `coupon_counter`(`admin_id`,`customer_id`,`coupon_id`,`counter`) VALUES (?,?,?,?)");
  $stmt->bind_param("ssss",$admin,$name,$coupon,$count);
  $Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
    setcookie("msg", "data",time()+3600,"/");
      header("location:coupon_counter.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:coupon_counter.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  
//$admin=$_REQUEST['ttId'];
  $admin=1;
  $srno=$_REQUEST['ttId2'];
  $name = $_REQUEST['customer'];
  $coupon = $_REQUEST['coupon'];
  $count = $_REQUEST['count'];
  try
  {
    $stmt = $obj->con1->prepare("UPDATE `coupon_counter` SET `admin_id` = ?,`customer_id` = ?, `coupon_id` = ?, `counter` = ? WHERE `sr_no` =?");
    $stmt->bind_param("sssss",$admin,$name,$coupon,$count,$srno);
    $Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }

  if($Resp)
  {
    setcookie("msg", "update",time()+3600,"/");
      header("location:coupon_counter.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:coupon_counter.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from coupon_counter where sr_no='".$_REQUEST["n_id"]."'");
    $Resp=$stmt_del->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt_del->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
  setcookie("msg", "data_del",time()+3600,"/");
    header("location:coupon_counter.php");
  }
  else
  {
  setcookie("msg", "fail",time()+3600,"/");
    header("location:coupon_counter.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Customer Registration</h4>

<?php 
if(isset($_COOKIE["msg"]) )
{

  if($_COOKIE['msg']=="data")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data added succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="update")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data updated succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="data_del")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data deleted succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="fail")
  {
  ?>

  <div class="alert alert-danger alert-dismissible" role="alert">
    An error occured! Try again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
}
  if(isset($_COOKIE["sql_error"]))
  {
    ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <?php echo urldecode($_COOKIE['sql_error'])?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>

    <script type="text/javascript">eraseCookie("sql_error")</script>
    <?php
  }

?>

 <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Add Customer</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        
                        <input type="hidden" name="ttId" id="ttId">
                        <input type="hidden" name="ttId2" id="ttId2">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Customer</label>
                          <select name="customer" id="customer" class="form-control" required>
                            <option value="">Select Customer Name</option>
                    <?php    
                        while($result=mysqli_fetch_array($cust_result)){
                    ?>
                        <option value="<?php echo $result["id"] ?>"><?php echo $result["name"] ?></option>
                    <?php
                      }
                    ?>
                          </select>
                        </div>
                        <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Coupon</label>
                          <select name="coupon" id="coupon" class="form-control" required>
                            <option value="">Select Coupen Name</option>
                    <?php    
                        while($result=mysqli_fetch_array($coup_result)){
                    ?>
                        <option value="<?php echo $result["c_id"] ?>"><?php echo $result["name"] ?></option>
                    <?php
                      }
                    ?>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Counter</label>
                          <input type="text" class="form-control" name="count" id="count" required />

                        </div>
                    
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                    
            <button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>

                      </form>
                    </div>
                  </div>
                </div>
                
              </div>

			<!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Coupon Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Customer Name</th>
                        <th>Coupon I.D.</th>
                        <th>Counter</th>
                        <th>Date/Time</th>
                        <th>Admin I.D.</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select c1.*,c2.name from coupon_counter c1,customer_reg c2 where c1.customer_id=c2.id order by id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($cust=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $cust["name"]?></td>
                        <td><?php echo $cust["coupon_id"]?></td>
                        <td><?php echo $cust["counter"]?></td>
                        <td><?php echo $cust["date/time"]?></td>
                        <td><?php echo $cust["admin_id"]?></td>
                        <td>
                        	<a href="javascript:editdata('<?php echo $cust["sr_no"]?>','<?php echo $cust["admin_id"]?>','<?php echo $cust["customer_id"]?>','<?php echo $cust["coupon_id"]?>','<?php echo $cust["counter"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          	<a href="javascript:deletedata('<?php echo $cust["sr_no"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $cust["sr_no"]?>','<?php echo $cust["admin_id"]?>','<?php echo $cust["customer_id"]?>','<?php echo $cust["coupon_id"]?>','<?php echo $cust["counter"]?>');">View</a>
                        </td>
                      </tr>
                      <?php
                          $i++;
                        }
                      ?>
                      
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Basic Bootstrap Table -->

<script type="text/javascript">
  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "coupon_counter.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(srno,admin,customer,coupon,count) {
         
      $('#ttId').val(admin);
      $('#ttId2').val(srno);
      $('#customer').val(customer);
      $('#coupon').val(coupon);
      $('#count').val(count);
      $('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
      $('#btnsubmit').attr('disabled',true);
  }
  function viewdata(srno,admin,customer,coupon,count) {
         
      $('#ttId').val(admin);
      $('#ttId2').val(srno);
      $('#customer').val(customer);
      $('#coupon').val(coupon);
      $('#count').val(count);
      $('#btnsubmit').attr('hidden',true);
      $('#btnupdate').attr('hidden',true);
      $('#btnsubmit').attr('disabled',true);

  }
</script>
<?php

	include "footer.php";

?>