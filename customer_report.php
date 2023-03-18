<?php
include("header.php");
error_reporting(E_ALL);


/*$stmt_slist = $obj->con1->prepare("select * from state where status='enable'");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();

$stmt_batch = $obj->con1->prepare("select * from batch");
$stmt_batch->execute();
$res_batch = $stmt_batch->get_result();
$stmt_batch->close();

$stmt_course = $obj->con1->prepare("select * from course");
$stmt_course->execute();
$res_course = $stmt_course->get_result();
$stmt_course->close();*/


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  $dispatch_dt=isset($_REQUEST['dispatch_dt'])?$_REQUEST['dispatch_dt']:"";
  

  
  $name_str=($name!="")?"and c1.name like '%".$name."%'":"";
  $contact_str=($contact!="")?"and c1.contact like '%".$contact."%'":"";
 
  $dispatch_dt_str=($dispatch_dt!="")?"and p1.dispatch_date='".$dispatch_dt."'":"";
  
  $stmt_list = $obj->con1->prepare("SELECT p1.*,c1.*,m1.mail_type as mail_type_name from customer_reg c1,post p1,mail_type m1 where p1.sender_id=c1.id and p1.mail_type=m1.id ".$name_str.$contact_str.$dispatch_dt_str);
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
              <label class="form-label" for="basic-default-fullname">Dispatch Date</label>
              <input type="date" class="form-control" name="dispatch_dt" id="dispatch_dt"  value=""/>
              
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
                     if(isset($_REQUEST['btnsubmit']) )
                      {
                      
                        $i=1;
                        while($row=mysqli_fetch_array($result))
                        {
                          
                          
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        
                        <td><?php echo $row["name"]?></td>
                        
                        <td><?php echo $row["contact"]?></td>
                        <td><?php echo $row["receiver_name"]?></td>
                        <td><?php echo $row["house_no"].",".$row["street_1"].",".$row["area"]?></td>
                        
                        <td><?php echo $row["mail_type_name"]?></td>
                        <td><?php echo $row["weight"]?></td>
                        <td><?php echo $row["acknowledgement"]?></td>
                        <td><?php echo $row["priority"]?></td>
                        <td><?php echo $row["dispatch_date"]?></td>

                        <td ><a href="#">View</a></td>
                        
                    
                          
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


<?php 
	include("footer.php");
?>