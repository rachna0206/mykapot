<?php
  include("header.php");

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
  $pass = $_REQUEST['password'];
  $contact_no = $_REQUEST['contact'];
  $status = $_REQUEST['status'];
  $noti_status=1;
  $playstatus=1;
  $noti_type="customer_reg";

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `customer_reg`(`name`,`email`,`password`,`contact`,`status`) VALUES (?,?,?,?,?)");
	$stmt->bind_param("sssss",$name,$email,$pass,$contact_no,$status);
	$Resp=$stmt->execute();
  $insert_id = mysqli_insert_id($obj->con1);
  $stmt->close();
  //inert into notif
  echo "INSERT INTO `notification`( `noti_type`, `noti_type_id`, `status`, `playstatus`) VALUES ($noti_type,$insert_id,$noti_status,$playstatus)";
  $stmt_noti = $obj->con1->prepare("INSERT INTO `notification`( `noti_type`, `noti_type_id`, `status`, `playstatus`) VALUES (?,?,?,?)");
  $stmt_noti->bind_param("siii",$noti_type,$insert_id,$noti_status,$playstatus);
  $Resp_noti=$stmt_noti->execute();
  $stmt_noti->close();

    if(!$Resp)
    {
      throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
    }
    
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
	  setcookie("msg", "data",time()+3600,"/");
      header("location:customer_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_reg.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  
  $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
  $pass = $_REQUEST['password'];
  $contact_no = $_REQUEST['contact'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update customer_reg set name=?, email=?, password=?, contact=?, status=?, action=? where id=?");
  	$stmt->bind_param("ssssssi", $name,$email,$pass,$contact_no,$status,$action,$id);
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
      header("location:customer_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_reg.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from customer_reg where id='".$_REQUEST["n_id"]."'");
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
    header("location:customer_reg.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:customer_reg.php");
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
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Name</label>
                          <input type="text" class="form-control" name="name" id="name" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Email</label>
                          <input type="text" class="form-control" name="email" id="email" required onkeyup ="check_cust_email(this.value)" />
                          <div id="email_alert_div" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Password</label>
                          <input type="password" min="6" class="form-control" name="password" id="password" required/>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Contact No.</label>
                          <input type="tel" pattern="[0-9]{10}" class="form-control phone-mask" id="contact" name="contact" onkeyup ="check_cust_contact(this.value)" required/>
                          <div id="contact_alert_div" class="text-danger"></div>
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
                <h5 class="card-header">Customer Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No.</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from customer_reg order by id desc");
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
                        <td><?php echo $cust["email"]?></td>
                        <td><?php echo $cust["contact"]?></td>
                    <?php if($cust["status"]=='enable'){	?>
                        <td style="color:green"><?php echo $cust["status"]?></td>
                    <?php } else if($cust["status"]=='disable'){	?>
                        <td style="color:red"><?php echo $cust["status"]?></td>
                    <?php } ?>
                        <td>
                        	<a href="javascript:editdata('<?php echo $cust["id"]?>','<?php echo base64_encode($cust["name"])?>','<?php echo base64_encode($cust["email"])?>','<?php echo base64_encode($cust["password"])?>','<?php echo base64_encode($cust["contact"])?>','<?php echo $cust["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $cust["id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $cust["id"]?>','<?php echo base64_encode($cust["name"])?>','<?php echo base64_encode($cust["email"])?>','<?php echo base64_encode($cust["password"])?>','<?php echo base64_encode($cust["contact"])?>','<?php echo $cust["status"]?>');">View</a>
                        </td>
                        <td><a href="javascript:addAddress('<?php echo $cust["id"]?>');">Add Address</a></td>
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

  function check_cust_contact(contact_no)
  {
    var id=$('#ttId').val();
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=check_cust_contact",
      data: "contact_no="+contact_no+"&id="+id,
      cache: false,
      success: function(result){
        if(result>0){
          $('#contact_alert_div').html('Contact Number already taken');
          document.getElementById('btnsubmit').disabled = true;
          document.getElementById('btnupdate').disabled = true;
        }
        else{
          $('#contact_alert_div').html('');
          document.getElementById('btnsubmit').disabled = false;
          document.getElementById('btnupdate').disabled = false;
        }
      }
    });
  }

  function check_cust_email(email_id)
  {
    var id=$('#ttId').val();
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=check_cust_email",
      data: "email_id="+email_id+"&id="+id,
      cache: false,
      success: function(result){
        if(result>0)
        {
          $('#email_alert_div').html('Email ID already taken');
          document.getElementById('btnsubmit').disabled = true;
          document.getElementById('btnupdate').disabled = true;
        }
        else
        {
          $('#email_alert_div').html('');
          document.getElementById('btnsubmit').disabled = false;
          document.getElementById('btnupdate').disabled = false;
        }
      }
    });
  }


  function addAddress(id) {
      document.cookie = "cid="+id;
      var loc = "customer_address.php";
      window.location = loc;
  }

  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "customer_reg.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,name,email,pass,contact,status) {
         
     	$('#ttId').val(id);
			$('#name').val(atob(name));
      $('#email').val(atob(email));
      $('#password').val(atob(pass));
      $('#contact').val(atob(contact));
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
  function viewdata(id,name,email,pass,contact,status) {
           
		  $('#ttId').val(id);
      $('#name').val(atob(name));
      $('#email').val(atob(email));
      $('#password').val(atob(pass));
      $('#contact').val(atob(contact));
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