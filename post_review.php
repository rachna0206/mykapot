<?php
  include("header.php");

$stmt_plist = $obj->con1->prepare("select p1.*, c1.name as sender_name from post p1, customer_reg c1 where p1.sender_id=c1.id");
$stmt_plist->execute();
$post_res = $stmt_plist->get_result();
$stmt_plist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $post_id = $_REQUEST['post_id'];
  $rating = $_REQUEST['rating'];
  $review = $_REQUEST['review'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `post_review`(`post_id`,`rating`,`review`) VALUES (?,?,?)");
	$stmt->bind_param("ids",$post_id,$rating,$review);
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
      header("location:post_review.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:post_review.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $post_id = $_REQUEST['post_id'];
  $rating  = $_REQUEST['rating'];
  $review = $_REQUEST['review'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update post_review set post_id=?, rating=?, review=?, action=? where pr_id=?");
  	$stmt->bind_param("idssi", $post_id,$rating,$review,$action,$id);
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
      header("location:post_review.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:post_review.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from post_review where pr_id='".$_REQUEST["n_id"]."'");
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
    header("location:post_review.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:post_review.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Post Review</h4>

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
                      <h5 class="mb-0">Add Post Review</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Post</label>
                          <select name="post_id" id="post_id" class="form-control" required>
                            <option value="">Select</option>
                    <?php    
                        while($post=mysqli_fetch_array($post_res)){
                    ?>
                        <option value="<?php echo $post["id"] ?>"><?php echo $post["sender_name"] ?>-<?php echo $post["id"] ?></option>
                    <?php
                      }
                    ?>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Rating</label>
                          <input type="number" min="0" max="5" step="0.5" class="form-control" name="rating" id="rating" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Review</label>
                          <textarea class="form-control" name="review" id="review" required></textarea>
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
                <h5 class="card-header">Post Review Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Post</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from post_review order by pr_id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($rev=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $rev["post_id"]?></td>
                        <td><?php echo $rev["rating"]?></td>
                        <td><?php echo $rev["review"]?></td>
                        <td>
                        	<a href="javascript:editdata('<?php echo $rev["pr_id"]?>','<?php echo $rev["post_id"]?>','<?php echo base64_encode($rev["rating"])?>','<?php echo base64_encode($rev["review"])?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $rev["pr_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $rev["pr_id"]?>','<?php echo $rev["post_id"]?>','<?php echo base64_encode($rev["rating"])?>','<?php echo base64_encode($rev["review"])?>');">View</a>
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
          var loc = "post_review.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,postid,rating,review) {
         
     	$('#ttId').val(id);
      $('#post_id').val(postid);
			$('#rating').val(atob(rating));
      $('#review').val(atob(review));
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);
  }
  function viewdata(id,postid,rating,review) {
           
		  $('#ttId').val(id);
      $('#post_id').val(postid);
      $('#rating').val(atob(rating));
      $('#review').val(atob(review));
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
      $('#btnupdate').attr('disabled',true);

  }
</script>
<?php 
  include("footer.php");
?>