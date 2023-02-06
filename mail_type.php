<?php

	include ("header.php");


 if(isset($_REQUEST['btnsubmit']))
{
  $ui=$_SESSION['id'];
  $mail=$_REQUEST['mail'];
  $status=$_REQUEST['status'];
  
  $action='added';
  try
  {
  $stmt = $obj->con1->prepare("INSERT INTO `mail_type` (`mail_type`, `status`, `user_id`, `action`) VALUES (?,?,?,?)");
  $stmt->bind_param("ssss",$mail,$status,$ui,$action);
  $Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in inserting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
    setcookie("msg", "data",time()+3600,"/");
      header("location:mail_type.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:mail_type.php");
  }
}

 if(isset($_REQUEST['btnupdate']))
{
  $ui=$_SESSION['id'];
  $id=$_REQUEST['ttId'];
  $mail=$_REQUEST['mail'];
  $status=$_REQUEST['status'];
  $action='updated';
  try
  {
  $stmt = $obj->con1->prepare("UPDATE `mail_type` SET `mail_type` = ?,`status` = ?,`user_id` = ?,`action` = ? WHERE `id` =?");
  $stmt->bind_param("ssisi",$mail,$status,$ui,$action,$id);
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
      header("location:mail_type.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:mail_type.php");
  }
}
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from mail_type where id='".$_REQUEST["n_id"]."'");
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
    header("location:mail_type.php");
  }
  else
  {
  setcookie("msg", "fail",time()+3600,"/");
    header("location:mail_type.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Mail Type</h4>

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
                      <h5 class="mb-0">Add Mail Type</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Mail type</label>
                          <input type="text" class="form-control" name="mail" id="mail" required />
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
                        
                        <div class="mb-3">
                          <input type="hidden" class="form-control" name="user" id="user" required />
                        </div>
                        
                        <div class="mb-3">
                          <input type="hidden" class="form-control" name="action" id="action" required />
                        </div>
                        
                        <div class="mb-3">
                          <input type="hidden" class="form-control" name="date" id="date"/>
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
                <h5 class="card-header">Mail Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Mail Type</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        //session_start();
                        $ui=$_SESSION['userid'];
                        $stmt_list = $obj->con1->prepare("select mt1.*,a1.name from mail_type mt1, admin a1 where mt1.user_id=a1.id order by mt1.id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($mail=mysqli_fetch_array($result))
                        {
                          ?>`

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $mail["mail_type"]?></td>
                        <td><?php echo $mail["status"]?></td>
                        <td><?php echo $mail["name"]?></td>
                        
                        <td>
                        	<a href="javascript:editdata('<?php echo($mail["id"]) ?>','<?php echo($mail["mail_type"])?>','<?php echo($mail["status"])?>')"><i class="bx bx-edit-alt me-1"></i> </a>
                    		<a  href="javascript:deletedata('<?php echo $mail["id"]?>')"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo($mail["mail_type"])?>','<?php echo($mail["status"])?>')">View</a>
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
            var loc = "mail_type.php?flg=del&n_id=" + id;
            window.location = loc;
        }
  }
  function editdata(id,mail,status,user) {
         
      $('#ttId').val(id);
      $('#mail').val(mail);
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
  function viewdata(mail,status,user) {
         
      $('#mail').val(mail);
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

	include ("footer.php");

?>