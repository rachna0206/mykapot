<?php
include "db_connect.php";
$obj=new DB_connect();

$name = isset($_REQUEST["name"])?$_REQUEST["name"]:"";
$email = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
$contact = isset($_REQUEST["contact"])?$_REQUEST["contact"]:"";



$name_str=(!empty($name))?" name like '%".$name."%'":"";
$contact_str=(!empty($contact))?" and contact like '%".$contact."%'":"";
$email_str=(!empty($email))?" and email like '%".$email."%'":"";



$stmt_list = $obj->con1->prepare("SELECT * from customer_reg c1 where ".$name_str.$contact_str.$email_str." order by c1.id desc");
$stmt_list->execute();
$result = $stmt_list->get_result();

$stmt_list->close();


$i=1;

while($row0 = mysqli_fetch_array($result))
{
							
$array[]=array("ID"=>$i,"Name"=>$row0["name"],"Contact"=>$row0["contact"],"Email"=>$row0["email"]);
		
	
$i++;

}


function cleanData(&$str)
  {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // file name for download
  $filename = "Customer Report for " . $name.$contact.$email.".xls";

 header("Content-Disposition: attachment; filename=\"$filename\"");
 header("Content-Type: application/vnd.ms-excel");
   $flag = false;
  foreach($array as $row) {
    if(!$flag) {
      // display field/column names as first row
      echo implode("\t", array_keys($row)) . "\n";
      $flag = true;
    }
    array_walk($row, 'cleanData');
    echo implode("\t", array_values($row)) . "\n";
  }

$flag = false;
exit();
?>