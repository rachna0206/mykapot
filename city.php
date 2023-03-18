<?php
include("header.php");

$stmt_slist = $obj->con1->prepare("select * from state");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $state_id = $_REQUEST['state'];
  $city_name = $_REQUEST['city_name'];
  $status = $_REQUEST['status'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `city`(`city_name`,`state`,`status`) VALUES (?,?,?)");
	$stmt->bind_param("sis",$city_name,$state_id,$status);
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
      header("location:city.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:city.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $state_id = $_REQUEST['state'];
  $city_name = $_REQUEST['city_name'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update city set city_name=?, state=?, status=?,action=? where city_id=?");
	$stmt->bind_param("sissi", $city_name,$state_id,$status,$action,$id);
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
      header("location:city.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:city.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from city where city_id='".$_REQUEST["n_id"]."'");
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
    header("location:city.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:city.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">City Master</h4>

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
                      <h5 class="mb-0">Add City</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">State</label>
                          <select name="state" id="state" class="form-control" required>
                          	<option value="">Select State</option>
                    <?php    
                        while($state=mysqli_fetch_array($res)){
                    ?>
                    		<option value="<?php echo $state["state_id"] ?>"><?php echo $state["state_name"] ?></option>
                    <?php
						}
					?>
					      </select>
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City Name</label>
                          <input type="text" class="form-control" name="city_name" id="city_name" required />
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
                <h5 class="card-header">City Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>City Name</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select  c1.*,s1.state_name,s1.state_id from city c1, state s1 where c1.state=s1.state_id order by c1.city_id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($city=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $city["city_name"]?></td>
                        <td><?php echo $city["state_name"]?></td>
                        <?php if($city["status"]=='enable'){	?>
                          <td style="color:green"><?php echo $city["status"]?></td>
                        <?php } else if($city["status"]=='disable'){	?>
                          <td style="color:red"><?php echo $city["status"]?></td>
                        <?php } ?>
                    
                        <td>
                        
                        	<a href="javascript:editdata('<?php echo $city["city_id"]?>','<?php echo $city["state_id"]?>','<?php echo base64_encode($city["city_name"])?>','<?php echo $city["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        
							<a  href="javascript:deletedata('<?php echo $city["city_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        
                        	<a href="javascript:viewdata('<?php echo $city["city_id"]?>','<?php echo $city["state_id"]?>','<?php echo base64_encode($city["city_name"])?>','<?php echo $city["status"]?>');">View</a>
                        
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
          var loc = "city.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,stateid,cname,status) {
           
		   	$('#ttId').val(id);
            $('#state').val(stateid);
			$('#city_name').val(atob(cname));
			if(status=="enable")
		   	{
				$('#enable').prop("checked","checked");	
		   	}
		   	else if(status=="disable")
		   	{
				$('#disable').prop("checked","checked");	
		   	}
			
			$('#btnsubmit').attr('hidden',true);
            $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);

        }
  function viewdata(id,stateid,cname,status) {
           
	   	$('#ttId').val(id);
      $('#state').val(stateid);
			$('#city_name').val(atob(cname));
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