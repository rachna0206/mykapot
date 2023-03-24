<?php
include("header.php");
error_reporting(E_ALL);

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  $order_dt=isset($_REQUEST['order_dt'])?$_REQUEST['order_dt']:"";
  $dispatch_dt=isset($_REQUEST['dispatch_dt'])?$_REQUEST['dispatch_dt']:"";
  

  
  $name_str=($name!="")?"and c1.name like '%".$name."%'":"";
  $contact_str=($contact!="")?"and c1.contact like '%".$contact."%'":"";
  $order_dt_str=($order_dt!="")?"and p1.order_date like '%".$order_dt."%'":"";
  $dispatch_dt_str=($dispatch_dt!="")?"and p1.dispatch_date='".$dispatch_dt."'":"";
  
  $stmt_list = $obj->con1->prepare("SELECT p1.*,c1.*,m1.mail_type as mail_type_name,p1.id as post_id from customer_reg c1,post p1,mail_type m1 where p1.sender_id=c1.id and p1.mail_type=m1.id ".$name_str.$contact_str.$order_dt_str.$dispatch_dt_str);
  $stmt_list->execute();
  $result = $stmt_list->get_result();
  
  $stmt_list->close();

}
else if(isset($_REQUEST["typ"]))
{
  if($_REQUEST['typ']=="today")
  {
    $order_dt=$_COOKIE['selected_date'];
    $stmt_list = $obj->con1->prepare("SELECT p1.*,c1.*,m1.mail_type as mail_type_name,p1.id as post_id from customer_reg c1,post p1,mail_type m1 where p1.sender_id=c1.id and p1.mail_type=m1.id and p1.order_date='".$order_dt."' ");
  }
  else if($_REQUEST['typ']=="upcoming")
  {
    $stmt_list = $obj->con1->prepare("SELECT p1.*,c1.*,m1.mail_type as mail_type_name,p1.id as post_id from customer_reg c1,post p1,mail_type m1 where p1.sender_id=c1.id and p1.mail_type=m1.id and cast(p1.dispatch_date as date)>'".date('Y-m-d')."' ");
  }
  else if($_REQUEST['typ']=="today_dispatch")
  {
    $dispatch_dt=$_COOKIE['selected_date'];
    $stmt_list = $obj->con1->prepare("SELECT p1.*,c1.*,m1.mail_type as mail_type_name,p1.id as post_id from customer_reg c1,post p1,mail_type m1 where p1.sender_id=c1.id and p1.mail_type=m1.id and p1.dispatch_date='".$dispatch_dt."' ");
  }
  
  $stmt_list->execute();
  $result = $stmt_list->get_result(); 
  $stmt_list->close();

}

?>

<h4 class="fw-bold py-3 mb-4">Post Job Report</h4>



<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
          
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="row">
            
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($_REQUEST['name'])?$_REQUEST['name']:""?>"  />
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Contact</label>
              <input type="text" class="form-control" name="contact" id="contact"  value="<?php echo isset($_REQUEST['contact'])?$_REQUEST['contact']:""?>"/>
              
            </div>
            
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Order Date</label>
              <input type="date" class="form-control" name="order_dt" id="order_dt"  value="<?php echo isset($order_dt)?$order_dt:"" ?>"/>
            </div>
            
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Dispatch Date</label>
              <input type="date" class="form-control" name="dispatch_dt" id="dispatch_dt"  value="<?php echo isset($dispatch_dt)?$dispatch_dt:"" ?>"/>
            </div>
            
           
          </div>

          <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
        
          <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='customer_report.php'">Cancel</button>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Post Job Records</h5>
               
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Recipient Name</th>
                        <th>Address</th>
                        <th>Mail Type</th>
                        <th>Weight</th>
                        <th>Acknowledgement</th>
                        <th>Priority</th>
                        <th>Dispatch Date</th>
                        <th>Action</th>
                        
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="grid">
                      <?php 
                     if(isset($_REQUEST['btnsubmit']) || isset($_REQUEST["typ"]))
                      {
                      
                        $i=1;
                        while($row=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        
                        <td><?php echo $row["name"]?>-<?php echo $row["post_id"]?></td>
                        
                        <td><?php echo $row["contact"]?></td>
                        <td><?php echo $row["receiver_name"]?></td>
                        <td><?php echo $row["house_no"].",".$row["street_1"].",".$row["area"]?></td>
                        
                        <td><?php echo $row["mail_type_name"]?></td>
                        <td><?php echo $row["weight"]?></td>
                        <td><?php echo $row["acknowledgement"]?></td>
                        <td><?php echo $row["priority"]?></td>
                        <td><?php echo $row["dispatch_date"]?></td>

                        <td ><a href="javascript:view_post_data('<?php echo $row["post_id"]?>')">View</a></td>
                        
                    
                          
                    <?php 
                    $i++;
                  } ?>
                      </tr>
                      <?php
                          
                        }
                        
                      ?>
                      
                    </tbody>
                  </table>
                </div>
              </div>



              <!--/ Basic Bootstrap Table -->

<script type="text/javascript">
  function view_post_data(pid)
  {
    createCookie("post_id",pid,1);
    window.open('customer_report_detail.php', '_blank');
  }
</script>

<?php 
	include("footer.php");
?>