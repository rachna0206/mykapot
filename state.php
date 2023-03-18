<?php
  include("header.php");





// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $state_name = $_REQUEST['state_name'];
  $status = $_REQUEST['status'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `state`(`state_name`,`status`) VALUES (?,?)");
	$stmt->bind_param("ss",$state_name,$status);
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
      header("location:state.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:state.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  
  $state_name = $_REQUEST['state_name'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update state set state_name=? ,status=?,action=? where state_id=?");
  	$stmt->bind_param("sssi", $state_name,$status,$action,$id);
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
      header("location:state.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:state.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from state where state_id='".$_REQUEST["n_id"]."'");
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
    header("location:state.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:state.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">State Master</h4>

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
                      <h5 class="mb-0">Add State</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">State Name</label>
                          <input type="text" class="form-control" name="state_name" id="state_name" required />
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
                <h5 class="card-header">State Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>State Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from state order by state_id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($state=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $state["state_name"]?></td>
                    <?php if($state["status"]=='enable'){	?>
                        <td style="color:green"><?php echo $state["status"]?></td>
                    <?php } else if($state["status"]=='disable'){	?>
                        <td style="color:red"><?php echo $state["status"]?></td>
                    <?php } ?>
                        <td>
                        	<a href="javascript:editdata('<?php echo $state["state_id"]?>','<?php echo base64_encode($state["state_name"])?>','<?php echo $state["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $state["state_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $state["state_id"]?>','<?php echo base64_encode($state["state_name"])?>','<?php echo $state["status"]?>');">View</a>
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
          var loc = "state.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,sname,status) {
         
     	$('#ttId').val(id);
			$('#state_name').val(atob(sname));
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
  function viewdata(id,sname,status) {
           
		  $('#ttId').val(id);
      $('#state_name').val(atob(sname));
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