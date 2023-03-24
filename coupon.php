<?php
  include("header.php");

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $name = $_REQUEST['name'];
  $code = $_REQUEST['code'];
  $discount = $_REQUEST['discount'];
  $max_discount = $_REQUEST['max_discount'];
  $amt = $_REQUEST['amt'];
  $start_dt = $_REQUEST['start_dt'];
  $end_dt = $_REQUEST['end_dt'];
  $descrip = $_REQUEST['descrip'];
  $status = $_REQUEST['status'];

  try
  {
  	$stmt = $obj->con1->prepare("INSERT INTO `coupon`(`name`,`couponcode`,`discount`,`max_discount`,`min_amount`,`start_date`,`end_date`,`info`,`status`) VALUES (?,?,?,?,?,?,?,?,?)");
  	$stmt->bind_param("ssdddssss",$name,$code,$discount,$max_discount,$amt,$start_dt,$end_dt,$descrip,$status);
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
      header("location:coupon.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:coupon.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $name = $_REQUEST['name'];
  $code = $_REQUEST['code'];
  $discount = $_REQUEST['discount'];
  $max_discount = $_REQUEST['max_discount'];
  $amt = $_REQUEST['amt'];
  $start_dt = $_REQUEST['start_dt'];
  $end_dt = $_REQUEST['end_dt'];
  $descrip = $_REQUEST['descrip'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update coupon set name=?, couponcode=?, discount=?, max_discount=?, min_amount=?, start_date=?, end_date=?, info=?, status=?, action=? where c_id=?");
  	$stmt->bind_param("ssdddsssssi", $name,$code,$discount,$max_discount,$amt,$start_dt,$end_dt,$descrip,$status,$action,$id);
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
      header("location:coupon.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:coupon.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from coupon where c_id='".$_REQUEST["n_id"]."'");
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
    header("location:coupon.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:coupon.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Coupon Master</h4>

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
                      <h5 class="mb-0">Add Coupon</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Name</label>
                          <input type="text" class="form-control" name="name" id="name" onkeyup ="check_coupon_name(this.value)" required />
                          <div id="name_alert_div" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Coupon Code</label>
                          <input type="text" class="form-control" name="code" id="code" onkeyup ="check_coupon_code(this.value)" required />
                          <div id="code_alert_div" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Discount</label>
                          <input type="text" class="form-control" name="discount" id="discount" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Maximum Discount</label>
                          <input type="text" class="form-control" name="max_discount" id="max_discount" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Minimum Amount</label>
                          <input type="text" class="form-control" name="amt" id="amt" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Start Date</label>
                          <input type="date" class="form-control" name="start_dt" id="start_dt" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">End Date</label>
                          <input type="date" class="form-control" name="end_dt" id="end_dt" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Description</label>
                          <textarea class="form-control" name="descrip" id="descrip"></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="enable" value="enable" required checked>
                            <label class="form-check-label" for="inlineRadio1">Enable</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="disable" value="disable" required>
                            <label class="form-check-label" for="inlineRadio1">Disable</label>
                          </div>
                        </div>
                        
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                    
						<button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>

                      </form>
                    </div>
                  </div>
                </div>
                
              </div>
           

           <!-- grid -->

           <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Coupon Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Coupon Name</th>
                        <th>Coupon Code</th>
                        <th>Discount</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select *,DATE_FORMAT(start_date, '%d-%m-%Y') as s_dt, DATE_FORMAT(end_date, '%d-%m-%Y') as e_dt from coupon order by c_id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($coupon=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $coupon["name"]?></td>
                        <td><?php echo $coupon["couponcode"]?></td>
                        <td><?php echo $coupon["discount"]?></td>
                        <td><?php echo $coupon["s_dt"]?></td>
                        <td><?php echo $coupon["e_dt"]?></td>
                    <?php if($coupon["status"]=='enable'){	?>
                        <td style="color:green"><?php echo $coupon["status"]?></td>
                    <?php } else if($coupon["status"]=='disable'){	?>
                        <td style="color:red"><?php echo $coupon["status"]?></td>
                    <?php } ?>
                        <td>
                        	<a href="javascript:editdata('<?php echo $coupon["c_id"]?>','<?php echo base64_encode($coupon["name"])?>','<?php echo base64_encode($coupon["couponcode"])?>','<?php echo base64_encode($coupon["discount"])?>','<?php echo base64_encode($coupon["max_discount"])?>','<?php echo base64_encode($coupon["min_amount"])?>','<?php echo base64_encode($coupon["start_date"])?>','<?php echo base64_encode($coupon["end_date"])?>','<?php echo base64_encode($coupon["info"])?>','<?php echo $coupon["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $coupon["c_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $coupon["c_id"]?>','<?php echo base64_encode($coupon["name"])?>','<?php echo base64_encode($coupon["couponcode"])?>','<?php echo base64_encode($coupon["discount"])?>','<?php echo base64_encode($coupon["max_discount"])?>','<?php echo base64_encode($coupon["min_amount"])?>','<?php echo base64_encode($coupon["start_date"])?>','<?php echo base64_encode($coupon["end_date"])?>','<?php echo base64_encode($coupon["info"])?>','<?php echo $coupon["status"]?>');">View</a>
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


           <!-- / grid -->

            <!-- / Content -->
<script type="text/javascript">

  function check_coupon_name(name)
  {
    var id=$('#ttId').val();
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=check_coupon_name",
      data: "name="+name+"&id="+id,
      cache: false,
      success: function(result){
        if(result>0){
          $('#name_alert_div').html('Name already taken');
          document.getElementById('btnsubmit').disabled = true;
          document.getElementById('btnupdate').disabled = true;
        }
        else{
          $('#name_alert_div').html('');
          document.getElementById('btnsubmit').disabled = false;
          document.getElementById('btnupdate').disabled = false;
        }
      }
    });
  }

  function check_coupon_code(code)
  {
    var id=$('#ttId').val();
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=check_coupon_code",
      data: "code="+code+"&id="+id,
      cache: false,
      success: function(result){
        if(result>0){
          $('#code_alert_div').html('Coupon Code already taken');
          document.getElementById('btnsubmit').disabled = true;
          document.getElementById('btnupdate').disabled = true;
        }
        else{
          $('#code_alert_div').html('');
          document.getElementById('btnsubmit').disabled = false;
          document.getElementById('btnupdate').disabled = false;
        }
      }
    });
  }

  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "coupon.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,name,code,disc,max_disc,amt,start,end,desc,status) {
      $('#ttId').val(id);
			$('#name').val(atob(name));
      $('#code').val(atob(code));
      $('#discount').val(atob(disc));
      $('#max_discount').val(atob(max_disc));
      $('#amt').val(atob(amt));
      $('#start_dt').val(atob(start));
      $('#end_dt').val(atob(end));
      $('#descrip').val(atob(desc));
      if(status=="enable")
	   	{
			 $('#enable').attr("checked","checked");	
	   	}
	   	else if(status=="disable")
	   	{
			 $('#disable').attr("checked","checked");	
	   	}
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);
  }
  function viewdata(id,name,code,disc,max_disc,amt,start,end,desc,status) {
		  $('#ttId').val(id);
      $('#name').val(atob(name));
      $('#code').val(atob(code));
      $('#discount').val(atob(disc));
      $('#max_discount').val(atob(max_disc));
      $('#amt').val(atob(amt));
      $('#start_dt').val(atob(start));
      $('#end_dt').val(atob(end));
      $('#descrip').val(atob(desc));
			if(status=="enable")
		  {
				$('#enable').attr("checked","checked");	
		  }
		  else if(status=="disable")
		  {
				$('#disable').attr("checked","checked");	
		  }
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
      $('#btnupdate').attr('disabled',true);
  }
</script>
<?php 
  include("footer.php");
?>