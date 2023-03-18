<?php
  include("header.php");

$stmt_clist = $obj->con1->prepare("select * from customer_reg where status='enable'");
$stmt_clist->execute();
$cust_res = $stmt_clist->get_result();
$stmt_clist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $cust_id = $_REQUEST['cust_id'];
  $feedback = $_REQUEST['feedback'];
  
  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `customer_feedback`(`customer_id`,`feedback`) VALUES (?,?)");
	$stmt->bind_param("is",$cust_id,$feedback);
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
      header("location:customer_feedback.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_feedback.php");
  } 
}

if(isset($_REQUEST['btnupdate']))
{
  $cust_id = $_REQUEST['cust_id'];
  $feedback = $_REQUEST['feedback'];
  $id=$_REQUEST['ttId'];
  try
  {
    $stmt = $obj->con1->prepare("update customer_feedback set customer_id=?, feedback=? where feedback_id=?");
  	$stmt->bind_param("isi", $cust_id,$feedback,$id);
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
      header("location:customer_feedback.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_feedback.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from customer_feedback where feedback_id='".$_REQUEST["n_id"]."'");
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
    header("location:customer_feedback.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:customer_feedback.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Customer Feedback</h4>

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
                      <h5 class="mb-0">Add Customer Feedback</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Customer Name</label>
                          <select name="cust_id" id="cust_id" class="form-control" required>
                            <option value="">Select</option>
                    <?php    
                        while($cust=mysqli_fetch_array($cust_res)){
                    ?>
                        <option value="<?php echo $cust["id"] ?>"><?php echo $cust["name"] ?></option>
                    <?php
                      }
                    ?>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Feedback</label>
                          <textarea class="form-control" name="feedback" id="feedback" required></textarea>
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
                <h5 class="card-header">State Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Customer Name</th>
                        <th>Feedback</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select f1.*,c1.name from customer_feedback f1, customer_reg c1 where f1.customer_id=c1.id");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($feed=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $feed["name"]?></td>
                        <td><?php echo $feed["feedback"]?></td>
                        <td>
                        	<a href="javascript:editdata('<?php echo $feed["feedback_id"]?>','<?php echo $feed["customer_id"]?>','<?php echo base64_encode($feed["feedback"])?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $feed["feedback_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $feed["feedback_id"]?>','<?php echo $feed["customer_id"]?>','<?php echo base64_encode($feed["feedback"])?>');">View</a>
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
  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "customer_feedback.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,custid,feedback) {
         
     	$('#ttId').val(id);
      $('#cust_id').val(custid);
			$('#feedback').val(atob(feedback));
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);
  }
  function viewdata(id,custid,feedback) {
           
		  $('#ttId').val(id);
      $('#cust_id').val(custid);
      $('#feedback').val(atob(feedback));
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
      $('#btnupdate').attr('disabled',true);

  }
</script>
<?php 
  include("footer.php");
?>