<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
//including the required files
require_once '../include/DbOperation.php';
require '../libs/Slim/Slim.php';

date_default_timezone_set("Asia/Kolkata");
\Slim\Slim::registerAutoloader();

//require_once('../../../Mail/PHPMailer_v5.1/class.phpmailer.php');

$app = new \Slim\Slim();


/*function smtpmailer($to, $from, $from_name, $subject, $body,$username,$password)
{


    global $error;
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPKeepAlive = true;
    $mail->Mailer = "smtp";
    $mail->Host = 'mail.saiaid.org';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->IsHTML(true);
    $mail->SMTPDebug = 1;
    $mail->From = $from;
    $mail->FromName = $from_name;
    $mail->Sender = $from; // indicates ReturnPath header
    $mail->AddReplyTo($from, $from_name); // indicates ReplyTo headers
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    $mail->Timeout = 60;


    if (!$mail->Send()) {
        $error = 'Mail error: ' . $mail->ErrorInfo;
         // echo $error;
        return 0;


    } else {
        $error = 'Message sent!';
        // echo $error;
        return 1;
    }
}*/



/* *
 * user login
 * Parameters: username, password,device_token,type
 * Method: POST
 * 
 */

$app->post('/login', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $userid = $data->userid;
    $password = $data->password;
    $device_token = $data->device_token;
    $device_type = $data->device_type;
    $user_type = $data->user_type;
    $db = new DbOperation();
    $data = array();
    //$data["response"] = array();

    //student login
    if($user_type=="student")
    {
        $db->studentLogin($userid, $password);
        if ($db->studentLogin($userid, $password)>0) 
        {
            $student = $db->getStudent($userid);
            
            $response = new stdClass();

           $response = new stdClass();
    
            foreach ($student as $key => $value) {
                $response->$key= $value;
            }
            $response->img_url="https://englishexpress.co.in/roots555/studentProfilePic/".$student["pic"];

            $insert_device = $db->insert_student_device($student['sid'], $device_token, $device_type);
            $data["data"]=$response;
            $data['success'] = true;
            $data["user_type"]=$user_type;
            

        }
        else
        {
            $data['success'] = false;

                $data['message'] = "Invalid username or password";

        }
    }
    else // faculty login
    {
        if ($db->facultyLogin($userid, $password)) 
        {
            $faculty = $db->getFaculty($userid);
       
            $response = new stdClass();

            
                
            foreach ($faculty as $key => $value) {
                $response->$key= $value;
                
            }
               
            
            $data["user_type"]=$user_type;
            $data['message'] = "";
            $data['success'] = true;
            
            $response->img_url="https://englishexpress.co.in/roots555/faculty_pic/".$faculty["profilepic"];

            $insert_device = $db->insert_faculty_device($faculty['id'], $device_token, $device_type);
            $data["data"]=$response;

        }
        else
            {

                $data['success'] = false;

                $data['message'] = "Invalid username or password";

            }

    }

    echoResponse(200, $data);
});


/* *
 * faculty list
 * Parameters: 
 * Method: POST
 * 
 */

$app->post('/faculty_list', function () use ($app) {
    //verifyRequiredParams(array('data'));
    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();

    
    $result=$db->faculty_list();
    
     
    
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * faculty list
 * Parameters: 
 * Method: POST
 * 
 */

$app->post('/batch_list', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $faculty_id = $data->faculty_id;
    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->batch_list($faculty_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});

/* *
 * student list
 * Parameters: 
 * Method: POST
 * 
 */

$app->post('/student_list', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $batch_id = $data->batch_id;
    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->student_list($batch_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * attendence from faculty side
 * Parameters: stu_id,batch_id,attendence
 * Method: POST
 * 
 */

$app->post('/faculty_attendence', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $batch_id = $data->batch_id;
    $stu_id = $data->stu_id;
    $attendence = $data->attendence;
    $remark = $data->remark;
    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    if($db->faculty_attendence($batch_id,$stu_id,$attendence,$remark))
    {

        $data['message'] = "Attendence added successfully";
        $data['success'] = true;
    }
    else
    {
        $data['message'] = "An error occurred";
        $data['success'] = true;
    }
       
    echoResponse(200, $data);
});


/* *
 * attendence from student side
 * Parameters: stu_id,batch_id,attendence
 * Method: POST
 * 
 */

$app->post('/stu_attendence', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $batch_id = $data->batch_id;
    $stu_id = $data->stu_id;
    $attendence = $data->attendence;
   
    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    if($db->stu_attendence($batch_id,$stu_id,$attendence))
    {

        $data['message'] = "Attendence added successfully";
        $data['success'] = true;
    }
    else
    {
        $data['message'] = "An error occurred";
        $data['success'] = true;
    }
       
    echoResponse(200, $data);
});


/* *
 * skill list
 * Parameters: 
 * Method: POST
 * 
 */

$app->post('/skill_list', function () use ($app) {
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->skill_list();
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * course list
 * Parameters: 
 * Method: POST
 * 
 */

$app->post('/course_list', function () use ($app) {
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->course_list();
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * student registration 
 * Parameters: name,address,education,stu_type(level),enrollment_dt,skill_id,course_id,
 * Method: POST
 * 
 */

$app->post('/stu_reg', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $name = $data->name;
    $address = $data->address;
    $education = $data->education;
    $stu_type = $data->stu_type;
    $enrollment_dt=$data->enrollment_dt;
    $skill_id=$data->skill_id;
    $course_id=$data->course_id;

    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    if($db->stu_reg($name,$address,$education,$stu_type,$enrollment_dt,$skill_id,$course_id))
    {

        $data['message'] = "Student added successfully";
        $data['success'] = true;
    }
    else
    {
        $data['message'] = "An error occurred";
        $data['success'] = true;
    }
       
    echoResponse(200, $data);
});



/* *
 * book list
 * Parameters: course_id
 * Method: POST
 * 
 */

$app->post('/book_list', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $course_id = $data->course_id;
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->book_list($course_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * chapter list
 * Parameters: book_id
 * Method: POST
 * 
 */

$app->post('/chapter_list', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $book_id = $data->book_id;
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->chapter_list($book_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * exercise list
 * Parameters: book_id,chapter_id
 * Method: POST
 * 
 */

$app->post('/exercise_list', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $book_id = $data->book_id;
    $chapter_id = $data->chapter_id;
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->exercise_list($book_id,$chapter_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * skills progress
 * Parameters: stu_id
 * Method: POST
 * 
 */

$app->post('/skill_status', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $stu_id = $data->stu_id;
    
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->skill_status($stu_id);
    
     
    
    while ($row = $result->fetch_assoc()) {
        $temp = new stdClass();
        foreach ($row as $key => $value) {
            $temp->$key = $value;
        }
        
        
    }
    $data['data']=$temp;
    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * roadmap
 * Parameters: stu_id
 * Method: POST
 * 
 */

$app->post('/roadmap', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $stu_id = $data->stu_id;
    
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->roadmap($stu_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        // get exercise count
        $result_exercise=$db->roadmap_exercise_count($stu_id,$row["bid"]);
        $temp = array();
        foreach ($row as $key => $value) {
            $temp["total_skill"]=$result_exercise["total_skill"];
            $temp["completed_skill"]=$result_exercise["completed_skill"];
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * chapter_list
 * Parameters: stu_id,book_id
 * Method: POST
 * 
 */

$app->post('/stu_chapter_list', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $stu_id = $data->stu_id;
    $book_id = $data->book_id;
    
        
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->stu_chapter_list($stu_id,$book_id);
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * banner
 * Parameters:
 * Method: POST
 * 
 */

$app->post('/banner', function () use ($app) {

    
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    $result=$db->banner();
    
     
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
           
            $temp["image_path"]="http://englishexpress.co.in/roots555/banner/";
            
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['message'] = "";
    $data['success'] = true;
    
    echoResponse(200, $data);
});


/* *
 * edit_profile
 * Parameters:uid,type
 * Method: POST
 * 
 */

$app->post('/edit_profile', function () use ($app) {

    verifyRequiredParams(array('data'));
    $data = json_decode($app->request->post('data'));
    $uid = $data->uid;
    $type=$data->type;
    $email=$data->email;
    $pic=(isset($_FILES["pic"]["name"]))?$_FILES["pic"]["name"]:"";
    $db = new DbOperation();
    $data = array();
    $data["data"] = array();  
    if($type=="student")
    {
        $result=$db->edit_stu_profile($uid,$email,$pic);
    }
    else
    {
        $result=$db->edit_faculty_profile($uid,$email,$pic);
    }
    
    if($result==1)
    {
        // upload pic
        if (isset($_FILES["pic"]["name"]) && $_FILES["pic"]["name"] != "" && $type=="student")
        {
                            
            $i = 1;
            $MainFileName = $_FILES["pic"]["name"];
           
            if(move_uploaded_file($_FILES["pic"]["tmp_name"], "../../../roots555/studentProfilePic/" . $MainFileName))
            {
                $data["image_upload"] = true;
            }
            else
            {
                $data["image_upload"] =false;
            }
                
            
        }
        // upload faculty pic
        if (isset($_FILES["pic"]["name"]) && $_FILES["pic"]["name"] != "" && $type=="faculty")
        {
                            
            $i = 1;
            $MainFileName = $_FILES["pic"]["name"];
           
            if(move_uploaded_file($_FILES["pic"]["tmp_name"], "../../../roots555/faculty_pic/" . $MainFileName))
            {
                $data["image_upload"] = true;
            }
            else
            {
                $data["image_upload"] =false;
            }
                
            
        }
       
        $data['message'] = "Profile updated successfully";
        $data['success'] = true;
    }
    else
    {

        $data['message'] = "An error occurred! Try again";
        $data['success'] = false;
    }


    
    
    echoResponse(200, $data);
});


// change password


$app->post('/change_password', function () use ($app) {
    verifyRequiredParams(array('data'));

    $data = json_decode($app->request->post('data'));
    
    $user_type=$data->user_type;
    $password=$data->password;
    $uid=$data->uid;
    $data = array();
    $data["data"] = array();  

   //device_token

    $db = new DbOperation();
    if($user_type=="student")
    {
        $res = $db->stu_password_update($uid,$password);
    }
    else
    {
        $res = $db->faculty_password_update($uid,$password);
    }
    

    if ($res == 1) {


        $data['success'] = true;
        
        $data['message'] = "Password updated successfully.";
        
    } else {

        $data['success'] = false;
       
        $data['message'] = "Please try again";
        
    }
    echoResponse(201, $data);

});

// logout
$app->post('/logout', function () use ($app) {
    verifyRequiredParams(array('data'));

    $data = json_decode($app->request->post('data'));
    $device_token = $data->device_token;
    $user_type=$data->user_type;
    $device_type=$data->device_type;
    $uid=$data->uid;
    $data = array();
    $data["data"] = array();  

   //device_token

    $db = new DbOperation();
    if($user_type=="student")
    {
        $res = $db->stu_logout($uid, $device_token,$device_type);
    }
    else
    {
        $res = $db->faculty_logout($uid, $device_token,$device_type);
    }
    

    if ($res == 1) {


        $data['success'] = true;
        
        $data['message'] = "Logged out";
        
    } else {

        $data['success'] = false;
       
        $data['message'] = "Please try again";
        
    }
    echoResponse(201, $data);

});


//---------------------------------------//


/*

* register new user
* method:post
* param:email,name,contact,password
*/

$app->post('/customer_registeration', function () use ($app) {
    verifyRequiredParams(array('email', 'name', 'contact', 'password'));
    $response = array();
    // $username = $app->request->post('username');
    $email = $app->request->post('email');
    $name = $app->request->post('name');
    $gender = $app->request->post('gender');
    $contact = $app->request->post('contact');
    $profile_for = $app->request->post('profile_for');
    $password = $app->request->post('password');

    $db = new DbOperation();
    $res = $db->do_reg_customer($email, $name, $contact, $password,$gender,$profile_for);


    if ($res == 0) {
        $user = $db->getUser($email);
        $data= new stdClass();
        $response['success'] = true;
        
        $data->id=$user['id'];
        $response["data"]=$data;
        $response["message"] = "You are successfully registered";
      
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response["message"] = "Oops! An error occurred while registereing";
        $response['success'] = false;
        echoResponse(200, $response);
    } else if ($res == 2) {
        //$res_user = $db->check_user($email, $contact);
        //$user = $res_user->fetch_assoc();
        $response["message"] = "Sorry, this user email already exist";
        $response['success'] = false;
        
        
        echoResponse(200, $response);
    } else if ($res == 3) {
        //$res_user = $db->check_user($email, $contact);
        //$user = $res_user->fetch_assoc();
        $response["message"] = "Sorry, this user contact already exist";
        $response['success'] = false;
        
        echoResponse(200, $response);
    }


});







/*
*add address
*param: ssid,address
*method:post
*/

$app->post('/add_address', function () use ($app) {
    verifyRequiredParams(array('address','pincode','name','phone'));
    $data = array();
    
    $address = $app->request->post('address');
    $pincode = $app->request->post('pincode');
    $name = $app->request->post('name');
    $phone = $app->request->post('phone');
    $email = $app->request->post('email');
    $country = $app->request->post('country');
    $qty = $app->request->post('qty');
    $feedback = "";
    $product="vibhuti";
    $order_from="android";
    $reg_from="android";
    $date=Date("Y-m-d");
    $phone_code = ($app->request->post('phone_code')!="")?$app->request->post('phone_code'):"+91";


    $db = new DbOperation();

    $res = $db->check_phone_number($phone,$phone_code);
    $num_rows = $res->num_rows;
    $phone_data=mysqli_fetch_array($res);
    
    if ($num_rows > 0) {
       // update address
        $check_user=$db->get_user_order($phone_data["ssno"]);
        $user_data=mysqli_fetch_array($check_user);
        $res_add=$db->update_address($name,$phone,$address,$pincode,$qty,$email,$country,$phone_code,$reg_from);
        $user_id=$phone_data["ssno"];

        $delivery_date=explode(" ", $user_data["delivery_date"]);
        $now = strtotime(Date('Y-m-d')); // or your date as well
        $your_date = strtotime($delivery_date[0]);
        $datediff = $now - $your_date;

        $diff= round($datediff / (60 * 60 * 24));
            /*if($diff>=10)
            {*/
                $date=Date('Y-m-d', strtotime($delivery_date[0]. ' + 10 days'));
                $current_date=Date("Y-m-d");
                  if($date<$current_date)
                  {
                     $date=Date("Y-m-d",strtotime("+3 days"));
                  }
                $user_order=$db->add_order($phone_data["ssno"],$product,$feedback,$date,$order_from);
                if ($user_order > 0) {
                $data['success'] = true;        
               $data['message'] = "Sai Ram, we have received your
request for Divya Udi Prasadam.
It will be dispatched by ".Date("d-m-Y",strtotime($date)).".
Thanks.";        
            }
           
       /* }
        else
        {
            $data['success'] = false;        
            $data['message'] = "You can not repeat order within 10 days";
        }*/
        /*$data['success'] = true;        
        $data['message'] = "Address added successfully";*/

        
    }
    else
    {
        
         // add address
        $res_add=$db->add_address($name,$phone,$address,$pincode,$qty,$email,$country,$phone_code,$reg_from);
        $user_id=$res_add;

        $date=date('Y-m-d', strtotime( ' + 3 days'));
        $user_order=$db->add_order($user_id,$product,$feedback,$date,$order_from);
        if ($user_order > 0) {
            $data['success'] = true;        
            $data['message'] = "Address added successfully ";
        
        }
        else
        {
            $data['success'] = false;        
            $data['message'] = "An error occurred!";
        }
    }
    
        
        /*$data['success'] = true;        
        $data['message'] = "Address added successfully";*/
        
        
    
    echoResponse(200, $data);

});



/*
*add/update user
*param: ssid,address
*method:post
*/

$app->post('/add_user', function () use ($app) {
    verifyRequiredParams(array('address','pincode','name','phone'));
    $data = array();
    $address = $app->request->post('address');
    $pincode = $app->request->post('pincode');
    $name = $app->request->post('name');
    $phone = $app->request->post('phone');
    $email = $app->request->post('email');
    $country = $app->request->post('country');
    $qty = $app->request->post('qty');
    $phone_code = ($app->request->post('phone_code')!="")?$app->request->post('phone_code'):"+91";
    $reg_from = ($app->request->post('reg_from')!="")?$app->request->post('reg_from'):"android";
    $fcm_token = ($app->request->post('fcm_token')!="")?$app->request->post('fcm_token'):"";
    $user_type="external";


    $db = new DbOperation();

    $res = $db->check_phone_number($phone,$phone_code);
    $num_rows = $res->num_rows;

   
    if ($num_rows > 0) {
       // update address
         $user_data=mysqli_fetch_array($res);
        $res_add=$db->update_address($name,$phone,$address,$pincode,$qty,$email,$country,$phone_code,$reg_from);
        $user_id=$user_data["ssno"];
    }
    else
    {
        
         // add address
        $res_add=$db->add_address($name,$phone,$address,$pincode,$qty,$email,$country,$phone_code,$reg_from);
        $user_id=$res_add;
    }
    
    $res = $db->insert_device_type($reg_from, $fcm_token, $user_id, $user_type);
        $obj= new stdClass();
        $obj->user_id=$user_id;
        $data['success'] = true;        
        $data['message'] = "Address added successfully";
        $data['data']=$obj;

        echoResponse(200, $data);

});



/*
*add order
*param: cid
*method:post
*/

$app->post('/add_order', function () use ($app) {
    verifyRequiredParams(array('cid'));
    $data = array();
    $cid = $app->request->post('cid');
    //$user_type = $app->request->post('user_type');
    $feedback = $app->request->post('feedback');
    $product="vibhuti";
    $date=Date("Y-m-d");
    $order_from = ($app->request->post('order_from')!="")?$app->request->post('order_from'):"android";
    $flag=1;
    $db = new DbOperation();    

    $check_user=$db->get_user_order($cid);
    $user_data=mysqli_fetch_array($check_user);
    if($check_user->num_rows==0)
    {
        // add order
        $date=date('Y-m-d', strtotime( ' + 3 days'));

        $user_order=$db->add_order($cid,$product,$feedback,$date,$order_from);
        if ($user_order > 0) {
           // $add_noti=$db->add_notification();
            $data['success'] = true;        
            $data['message'] = "Sai Ram, we have received your
request for Divya Udi Prasadam.
It will be dispatched by ".Date("d-m-Y",strtotime($date)).".
Thanks.";  
        
        }
        else
        {
            $data['success'] = false;        
            $data['message'] = "An error occurred!";
        }
    }
    else
    {
        $delivery_date=explode(" ", $user_data["delivery_date"]);
        $now = strtotime(Date('Y-m-d')); // or your date as well
        $your_date = strtotime($delivery_date[0]);
        $datediff = $now - $your_date;

        $diff= round($datediff / (60 * 60 * 24));
                
                $date=date('Y-m-d', strtotime($delivery_date[0]. ' + 30 days'));
                $current_date=Date("Y-m-d");
                  if($date<$current_date)
                  {
                     $date=Date("Y-m-d",strtotime("+3 days"));
                     $flag=0;
                  }
                  
                  if($flag==0)
                {
                $user_order=$db->add_order($cid,$product,$feedback,$date,$order_from);
                if ($user_order > 0) {
                $data['success'] = true;        
                $data['message'] = "Sai Ram, we have received your
request for Divya Udi Prasadam.
It will be dispatched by ".Date("d-m-Y",strtotime($date)).".
Thanks.";  
            
            }
            else
            {
                $data['success'] = false;        
                $data['message'] = "An error occurred!";
            }
        }
        else
        {
           $data['success'] = true;        
                $data['message'] = "Since your Divya Udi request is already placed, you can not place another request before ".Date("d-m-Y",strtotime($date)).".
Om Sai Ram .";  
        }
    }
  
  
    echoResponse(200, $data);

});




/*
*food request for needy
*param: cid
*method:post
*/

$app->post('/food_request', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = array();
    $request_data = json_decode($app->request->post('data'));
    $reference_contact=$request_data->reference_contact;
    $child_below_six=$request_data->child_below_six;
    $children=$request_data->children;
    $adults=$request_data->adults;
    $senior_citizen=$request_data->senior_citizen;
    $needy_contact=$request_data->needy_contact;
    $order_type=$request_data->order_type;
    $lat=$request_data->lat;
    $long=$request_data->long;
    $address=$request_data->address;
    $distance=$request_data->distance;
    $kitchen_id=$request_data->kitchen_id;
    $device_token = isset($request_data->device_token)?$request_data->device_token:"";
    $device_type = isset($request_data->type)?$request_data->type:"";
    $date = Date('d/m/Y');
    $time = Date('h:i a');
    $image=$app->request->post('image');
    $image_resp=array();

    $db = new DbOperation();    
    if (isset($_FILES["image"]["name"]) && $_FILES["image"]["name"] != "")
    {
                        
        $i = 1;
        $MainFileName = $_FILES["image"]["name"];
        $milliseconds = round(microtime(true) * 1000);
        $Arr = explode('.', $MainFileName);
        $MainFileName = date("Ymd")  . $milliseconds. ".".$Arr[1];              
   
        $type="image";
        
        if($MainFileName!=""){
            if(move_uploaded_file($_FILES["image"]["tmp_name"], "../../../saiUploads/" . $MainFileName))
            {
                      $image_resp["image_upload"] = true;
            }
            else
            {
                      $image_resp["image_upload"] =false;
            }
            
        }
    }
    else
    {
        $MainFileName="";
    }
    // 2nd image
    if (isset($_FILES["image2"]["name"]) && $_FILES["image2"]["name"] != "")
    {
                        
        $i = 1;
        $MainFileName2 = $_FILES["image2"]["name"];
        $milliseconds2 = round(microtime(true) * 1000);
        $Arr = explode('.', $MainFileName2);
        $MainFileName2 = date("Ymd")  . $milliseconds2. ".".$Arr[1];              
   
        $type="image";
        
        if($MainFileName2!=""){
            if(move_uploaded_file($_FILES["image2"]["tmp_name"], "../../../saiUploads/" . $MainFileName2))
            {
                      $image_resp["image_upload2"] = true;
            }
            else
            {
                      $image_resp["image_upload2"] =false;
            }
            
        }

    }
    else
    {
       $MainFileName2=""; 
    }


    $delTime = strtotime("+45 minutes", strtotime($time));
    $del_time=date('h:i a', $delTime);
      
    $user_order=$db->add_food_request($reference_contact,$child_below_six,$children,$adults,$senior_citizen,$needy_contact,$order_type,$lat,$long,$address,$distance,$MainFileName,$MainFileName2,$date,$del_time,$kitchen_id); 
    if ($user_order > 0) {
    $data['success'] = true;        
    $data['message'] = "Request added successfully";  
    $data['data']=$image_resp;

    // insert notification
     $notification=$db->add_aid_notification($user_order);
         if($device_token!="")
         {

         
            $insert_device = $db->insert_aid_user_device($user_order, $device_token, $device_type);
        }
    
    }
    else
    {
        $data['success'] = false;        
        $data['message'] = "An error occurred!";
        $data['data']=$image_resp;
    }
    
    echoResponse(200, $data);

});




/*
*food request for needy
*param: cid
*method:post
*/

$app->post('/advance_seva', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = array();
    $request_data = json_decode($app->request->post('data'));
    $reference_contact=$request_data->reference_contact;
    
    $device_token = isset($request_data->device_token)?$request_data->device_token:"";
    $device_type = isset($request_data->type)?$request_data->type:"";
    $date = $request_data->date;
    $time = $request_data->time;
    

    $db = new DbOperation();
     $user_order=$db->add_advance_seva($reference_contact,$date,$time);
    if ($user_order > 0) {
    $data['success'] = true;        
    $data['message'] = "Request added successfully";  
    

   
         if($device_token!="")
         {

         
            $insert_device = $db->insert_aid_user_device($user_order, $device_token, $device_type);
        }
    
    }
    else
    {
        $data['success'] = false;        
        $data['message'] = "An error occurred!";
        $data['data']=$image_resp;
    }
    
    echoResponse(200, $data);

});
/*
* get counters value
* param: none
* method:POST
*/

$app->get('/get_counters', function () use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_counters();
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    $response['message'] = "";  
    
    $kitchen=$db->get_kitchen_count();
    $kitchen_count=$kitchen->fetch_assoc();
    $meals_db=$db->get_meal_count();
    $meals=$meals_db->fetch_assoc();
    $volunteers_db=$db->get_volunteer_count();
    $volunteers=$volunteers_db->fetch_assoc();
    
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        $temp["volunteers"]=$volunteers["volunteers"];
        $temp["location"]=$kitchen_count["kitchen"];
        $temp["meals"]=$meals["meals"]+145000;
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});


/*
* insert interested in seva 
* param: phone
* method:POST
*/

$app->post('/add_insterested_seva', function () use ($app) {
    verifyRequiredParams(array('phone'));
   
    $phone = $app->request->post('phone');
    $db = new DbOperation();
    $result = $db->add_insterested_seva($phone);
    $response = array();
    $data['success'] = true;
    $data['data'] = array();
   
    if ($result==1) {
    $data['success'] = true;        
    $data['message'] = "Request added successfully";  
   
    }
    else if($result==2)
    {
        $data['success'] = true;        
        $data['message'] = "Mobile No. already exists!";  
    }
    else
    {
        $data['success'] = false;        
        $data['message'] = "An error occurred!";
      
    }

    echoResponse(200, $data);
});


/*
* get banners
* param: none
* method: GET

*/
$app->get('/get_banner', function () use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_banner();
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});




/*
* find nearby kitchen
*param: pincode
*method:post
*/

$app->post('/find_kitchen', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = array();
    $request_data = json_decode($app->request->post('data'));
    $pincode=$request_data->pincode;
    $data['data'] = array();
    $db = new DbOperation();
     $kitchen=$db->find_kitchen($pincode);
    
    if (isset($kitchen->num_rows)) {
         
        while ($row = $kitchen->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['success'] = true;        
    $data['message'] = "";  
    

    
    }
    else
    {
        $data['success'] = false;        
        $data['message'] = "No kithcen found ";

    }
    
    echoResponse(200, $data);

});


/*
* find  kitchen from city
*param: pincode
*method:post
*/

$app->post('/find_kitchen_from_city', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = array();
    $request_data = json_decode($app->request->post('data'));
    $city_name=$request_data->city_name;
    $pincode=$request_data->pincode;
    $data['data'] = array();
    $db = new DbOperation();
    $kitchen=$db->find_kitchen_from_city($city_name,$pincode);
    
    if ($kitchen) {
         
        while ($row = $kitchen->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['data'], $temp);
    }

    $data['success'] = true;        
    $data['message'] = "";  
    

    
    }
    else
    {
        $data['success'] = false;        
        $data['message'] = "No kithcen found ";

    }
    
    echoResponse(200, $data);

});


/*
*food request for needy
*param: cid
*method:post
*/

$app->post('/contact_us', function () use ($app) {
    verifyRequiredParams(array('data'));
    $data = array();
    $request_data = json_decode($app->request->post('data'));
    $name=$request_data->name;
    $mobile=$request_data->mobile;
    $email=$request_data->email;
    $pincode=$request_data->pincode;
    $address=$request_data->address;
    $message=$request_data->message;

    
    $db = new DbOperation();
   $contact_mail='<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            color: #000000;

        }

        li {
            float: left;
        }

        li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        li a:hover {
        }
        [class*="col-"] {
            float: left;
            padding: 5px;
        }
        html {
            font-family: "Lucida Sans", sans-serif;
        }

        .logoRes img{
            height: auto;
            width: 50%;

        }


        @media only screen and (min-width: 150px) and (max-width: 600px){
            .logoRes img{
                height: auto;
                width: 70%;

            }

        }
        th {



            padding: 2%;
            font-size: 20px;

        }
        tr {

            font-size: 18px;
        }
        td{
            padding: 2%;

            border: 2px solid #f4f4f4;
        }

        @media only screen and (max-width: 1250px) {


            tr {
                font-size: 14px;
            }

        }

        @media only screen and (max-width: 800px) {
            tr {
                font-size: 12px;
            }
            .logoRes img{
                height: auto;
                width: 90%;

            }

        }
        @media only screen and (max-width: 300px) {
            tr {
                font-size: 10px;
            }
            @media only screen and (max-width: 800px) {
                tr {
                    font-size: 12px;
                }
                .logoRes img{
                    height: auto;
                    width: 90%;

                }
            }
        }

    </style>
</head>
<body style=" background-color: #eaebec;margin-bottom: 5%">
<div class="logoRes" >
   </div>
<div style="margin:0;padding:0" style=" background-color: #ffffff;">
    <div style="width:100%;  ">
        <table style="width:100%;empty-cells:show;margin:10px 0;background-color: white;" >
            <thead>
            <tr>
                <th colspan="2" style="text-align:left;padding:10px;background-color: #005370;color: white">
                    <h3 style="margin:0;padding:0">Details of Enquiry</h3>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width:50%;padding:10px;background-color: #ebebf2">Name </td>


                <td style="width:50%;padding:10px;color: #3B5998;"> '.$name.'</td>
            </tr>

            <tr>
                <td style="width:50%;padding:10px;background-color: #ebebf2"> Mobile  </td>
                <td style="width:50%;padding:10px;color: #3B5998"> '.$mobile.'</td>
            </tr>
            <tr>
                <td style="width:50%;padding:10px;background-color: #ebebf2"> Email-Id </td>
                <td style="width:50%;padding:10px;color: #3B5998"> '.$email.'</td>
            </tr>
            <tr>
                <td style="width:50%;padding:10px;background-color: #ebebf2"> Address  </td>
                <td style="width:50%;padding:10px;color: #3B5998"> '.$address.'</td>
            </tr>
            <tr>
                <td style="width:50%;padding:10px;background-color: #ebebf2"> Pincode  </td>
                <td style="width:50%;padding:10px;color: #3B5998"> '.$pincode.'</td>
            </tr>
            <tr>
                <td style="width:50%;padding:10px;background-color: #ebebf2"> Message  </td>
                <td style="width:50%;padding:10px;color: #3B5998"> '.$message.'</td>
            </tr>

            </tbody>
        </table>
</body>
</html>
';
            
    
    $mail_resp = smtpmailer("info@annaseva.in", "info@annaseva.in", $name, "Contact Enquiry", $contact_mail, "info@annaseva.in", "m@2MhGTe*z&M");
     $res_contact=$db->add_contact($name,$email,$mobile,$message,$address,$pincode);
    if ($res_contact > 0) {
    $data['success'] = true;        
    $data['message'] = "Enquiry sent successfully";  
    
    
    }
    else
    {
        $data['success'] = false;        
        $data['message'] = "An error occurred!";
        
    }
    
    echoResponse(200, $data);

});


/*
* get all country list
* param: none
* method: GET

*/
$app->get('/get_allCountry', function () use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_allCountry();
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});


/*
* get all state list
* param: none
* method: GET

*/
$app->get('/get_allState', function () use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_allState();
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});



/*
* get country wise state list
* param: none
* method: GET

*/
$app->get('/get_country_state/:country_id', function ($country_id) use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_country_state($country_id);
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});



/*
* get state wise city list
* param: none
* method: GET

*/
$app->get('/get_state_city/:state_id', function ($state_id) use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_state_city($state_id);
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});


/*
* get all languages list
* param: none
* method: GET

*/
$app->get('/get_allLanguages', function () use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_allLanguages();
    $response = array();
    $response['success'] = true;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});


/* *
 * add user payment
 * Parameters: user_id,payment_type,amount,start_date,end_date,payment
 * Authorization: Put API Key in Request Header
 * Method: post
 * */
$app->post('/user_payment', 'authenticateUser', function() use ($app){
    verifyRequiredParams(array('user_id','start_date','end_date','payment'));
    $user_id = $app->request->post('user_id');
    $payment_type = $app->request->post('payment_type');
    $amount = $app->request->post('amount');
    $start_date = $app->request->post('start_date');
    $end_date = $app->request->post('end_date');
    $payment = $app->request->post('payment');
    $db = new DbOperation();
    $response = array();
    $data = array();
    $user = $db->getUserData($user_id);
    
    if($payment=="trial")
    {
        $result = $db->user_trial($user_id,$payment_type,$amount,$start_date,$end_date);
    }
    else
    {
       $result = $db->user_payment($user_id,$payment_type,$amount,$start_date,$end_date);  
    }
   
    if($result==1)
    {
        $response['success'] = true;
        if($payment=="trial")
        $response['message']="Trial Added successfully";
    else
        $response['message']="Payment Added successfully";
    }
    else
    {
        $response['success'] = false;
        $response['message']="Error in payment";
    }
    if($user["otp_verification"]=="verified")
    {
        $data["otp_verification"]=true;
    }
    else
    {
        $data["otp_verification"]=false;   
    }

    // check if user has paid or using trial version

    $user_payment=$db->check_user_payment($user_id);
    $user_trial=$db->check_user_trial($user_id);
    if($user_payment->num_rows>0)
    {
        $payment_data=$user_payment->fetch_assoc();
        $data["user_payment"]="paid";
        $data["start_date"]=$payment_data["start_date"];
    }
    else if ($user_trial->num_rows>0)
    {
        $trial_data=$user_trial->fetch_assoc();
        $data["user_payment"]="trial";
        $data["start_date"]=$trial_data["start_date"];
    }
    else
    {
        $data["user_payment"]="not available";
        $data["start_date"]="";
    }
    $response["data"]=$data;
    
   
    echoResponse(200,$response);
});



/* *
 * get user profile
 * Parameters: user_id,payment_type,amount,start_date,end_date,payment
 * Authorization: Put API Key in Request Header
 * Method: post
 * */
$app->get('/get_user_profile/:user_id', 'authenticateUser', function($user_id) use ($app){
    
    
    $db = new DbOperation();
    $data = array();
    $temp=new stdClass();
    $response = new stdClass();
    $user = $db->getUserData($user_id);
    if(!empty($user))
    {
        $response->id = $user['id'];
        $response->fname = $user['fname'];  
        $response->lname = $user['lname'];               
        $response->email= $user['email'];   
        $response->contact = $user['contact'];  
        $response->password = $user['password'];  
        $response->gender = $user['gender'];  
        $response->profile_for = $user['profile_for'];  
        $response->status = $user['status'];  
        $response->authentication = $user['authentication'];  
        $response->hide_photo  = $user['hide_photo']; 
        if($user["otp_verification"]=="verified")
        {
            $response->otp_verification=true;
        }
        else
        {
            $response->otp_verification=false;   
        }
       
    }
    
    
     
    
    // get personal details
    $personal=$db->get_personal_data($user_id);
    $personaldata = new stdClass();
    while ($row = $personal->fetch_assoc()) {
        
        foreach ($row as $key => $value) {
            $personaldata->$key = $value;
        }
        //$personaldata = array_map('utf8_encode', $personaldata);
        
    }
    $temp->personal_details=$personaldata;
    //get social details
    $social=$db->get_social_data($user_id);
    $socialdata = new stdClass();
    while ($row = $social->fetch_assoc()) {
        
        foreach ($row as $key => $value) {
            $socialdata->$key = $value;
        }
       // $socialdata = array_map('utf8_encode', $socialdata);
        
    }
    $temp->social_details=$socialdata;

    //get career details
    $career=$db->get_career_data($user_id);
    $careerdata = new stdClass();
    while ($row = $career->fetch_assoc()) {
        
        foreach ($row as $key => $value) {
            $careerdata->$key = $value;
        }
       // $careerdata = array_map('utf8_encode', $careerdata);
        
    }
    $temp->career_details=$careerdata;
    $temp->register=$response;
    $data["data"]=$temp;
    $data["success"]=true;
    $data["message"]="";

    
    
   
    echoResponse(200,$data);
});




/* *
 * add user profile
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: post
 * */
$app->post('/add_profile', 'authenticateUser', function() use ($app){
    verifyRequiredParams(array('profile_data'));
    $data = json_decode($app->request->post('profile_data','user_id'));
    $user_id = $app->request->post('user_id');
    $personal_details=$data->personal_details;
    $education=$data->education;
    $social_details=$data->social_details;
    $db = new DbOperation();
    $response = array();
    $data=array();
    $MainFileName="";
        //$personal_details->gender,$personal_details->hide_photo

    // update registration
    $register=$db->update_register($user_id,$personal_details->first_name,$personal_details->last_name,$personal_details->contact,$personal_details->gender,$personal_details->profile_for,$personal_details->hide_photo);

    //check for personal/social and career profile

    $check_personal_profile=$db->check_personal_profile($user_id);
    if($check_personal_profile->num_rows>0)
    {
        
        $personal_res=$db->update_personal_profile($user_id,$personal_details->dob,$personal_details->height,$personal_details->weight,$personal_details->complexion,$personal_details->country_id,$personal_details->state_id,$personal_details->city_id,$personal_details->smoking_habit,$personal_details->drinking_habit,$personal_details->diet_preference,$personal_details->about,$personal_details->thalassemia);
    }
    else
    {
        $personal_res=$db->add_personal_profile($user_id,$personal_details->dob,$personal_details->height,$personal_details->weight,$personal_details->complexion,$personal_details->country_id,$personal_details->state_id,$personal_details->city_id,$personal_details->smoking_habit,$personal_details->drinking_habit,$personal_details->diet_preference,$personal_details->about,$personal_details->thalassemia);
    }

    
    $check_social_profile=$db->check_social_profile($user_id);
    if($check_social_profile->num_rows>0)
    {
       $social_res=$db->update_social_profile($user_id,$social_details->marital_status,$social_details->mother_tongue,$social_details->languages_known,$social_details->religion_id ,$social_details->caste_id,$social_details->sub_caste_id,$social_details->manglik,$social_details->sai_devotee);
    }
    else
    {
        $social_res=$db->add_social_profile($user_id,$social_details->marital_status,$social_details->mother_tongue,$social_details->languages_known,$social_details->religion_id ,$social_details->caste_id,$social_details->sub_caste_id,$social_details->manglik,$social_details->sai_devotee);
    }
   
   $check_career_profile=$db->check_career_profile($user_id);
   if($check_career_profile->num_rows>0)
   {
        $education_res=$db->update_education($user_id,$education->higher_education,$education->occupation,$education->employed_in,$education->income);
   }
   else
   {
        $education_res=$db->add_education($user_id,$education->higher_education,$education->occupation,$education->employed_in,$education->income);
   }

   
   
    if($personal_res==1)
    {
        
        $data['personal_message']="Personal profile added successfully";
        if (isset($_FILES["id_proof"]["name"]) && $_FILES["id_proof"]["name"] != "")
        {
            if (file_exists("../../../vivaahRoots777/uploads/" . $_FILES["id_proof"]["name"] )) {
                $i = 1;
                $MainFileName = $_FILES["id_proof"]["name"];
                $Arr = explode('.', $MainFileName);
                $MainFileName = $Arr[0] . $i . "." . $Arr[1];
                while (file_exists("../../../vivaahRoots777/uploads/" . $MainFileName)) {
                    $i++;
                    $MainFileName = $Arr[0] . $i . "." . $Arr[1];
                }

            } else {
                $MainFileName = $_FILES["id_proof"]["name"];;
            }
                   
                $add_id=$db->add_id_proof($user_id,$MainFileName);
        }
        
        if($MainFileName!=""){
            if(move_uploaded_file($_FILES["id_proof"]["tmp_name"], "../../../vivaahRoots777/uploads/" . $MainFileName))
            {
                      $data["image_upload"] = "success";
            }
            else
            {
                      $data["image_upload"] = "fail";
            }
            
        }
    
    }
    else
    {
        
        $data['personal_message']="Error in personal profile";
    }
    if($social_res==1)
    {
               
        $data['social_message']="Social profile added successfully";
    
    }
    else
    {
        
        $data['social_message']="Error in social profile";
    }
    if($education_res==1)
    {
        
        $data['education_message']="Career profile added successfully";
    
    }
    else
    {
        
        $data['education_message']="Error in career profile";
    }
    if($personal_res==1 || $social_res==1 || $education_res==1)
    {
        $response['success'] = true; 
        $response['message'] = "Profile added successfully";
    }
    else
    {
        $response['success'] = false;
    }
    $response['data']=$data;
    echoResponse(200,$response);
});


// upload files
$app->post('/uploadFiles', 'authenticateUser',function () use ($app) {
    verifyRequiredParams(array('reg_id'));
    $response = array();
    $reg_id = $app->request->post('reg_id');
    $db = new DbOperation();


   // upload images
   
    
       
    
    if (isset($_FILES["images"]["name"]) && $_FILES["images"]["name"] != "")
        {
            print_r($_FILES["images"]["name"]);
            //if (file_exists("../../../vivaahRoots777/uploads/" . $_FILES["images"]["name"] )) {
            for($j=0;$j<count($_FILES["images"]["name"]);$j++)
            {
                $i = 1;
                $MainFileName = $_FILES["images"]["name"][$j];
                $milliseconds = round(microtime(true) * 1000);
                $Arr = explode('.', $MainFileName);
                $MainFileName = $reg_id .date("Ymd")  . $milliseconds. ".".$Arr[1];              
           
                $type="image";
                $res = $db->uploadPhotos($MainFileName, $reg_id,$type);
                if($MainFileName!=""){
                    if(move_uploaded_file($_FILES["images"]["tmp_name"][$j], "../../../vivaahRoots777/uploads/" . $MainFileName))
                    {
                              $data["image_upload"] = "success";
                    }
                    else
                    {
                              $data["image_upload"] = "fail";
                    }
                    
                }
        }
        
        
    }

    // upload video

    if (isset($_FILES["video"]["name"]) && $_FILES["video"]["name"] != "")
        {
            //if (file_exists("../../../vivaahRoots777/uploads/" . $_FILES["images"]["name"] )) {
             
                $i = 1;
                $videoName = $_FILES["video"]["name"];
                $milliseconds = round(microtime(true) * 1000);
                $Arr = explode('.', $videoName);
                $videoName = $reg_id .date("Ymd")  . $milliseconds. ".".$Arr[1];              
           
                $type="video";
                $res = $db->uploadPhotos($videoName, $reg_id,$type);
        
        
        if($videoName!=""){
            if(move_uploaded_file($_FILES["video"]["tmp_name"], "../../../vivaahRoots777/uploads/" . $videoName))
            {
                      $data["image_upload"] = "success";
            }
            else
            {
                      $data["image_upload"] = "fail";
            }
            
        }
    }
    $db = new DbOperation();
    
       

    
    if ($res == 0) {


        $response["success"] = false;

        $response["message"] = "Oops! An error occurred while uploading";
        echoResponse(200, $response);
    } else {

        $response["success"] = true;

        $response["message"] = "Images uploaded";
        echoResponse(201, $response);

    }
});



/*
URL: https://pragmanxt.com/pragma_demo_multivendor/Mobile_Services/delivery/v1/logout
 * logout
 * Parameters:id,tokenid
 * Method: post
*/
$app->post('/logout', function () use ($app) {
    verifyRequiredParams(array('id', 'tokenid'));

    $response = array();

    $id = $app->request->post('id');
    $tokenid = $app->request->post('tokenid');

    $db = new DbOperation();
    $res = $db->logout($id, $tokenid);

    if ($res == 1) {


        $response['error'] = false;
        $response['value'] = "valid";
        $response['message'] = "Logged out";
        echoResponse(201, $response);
    } else {

        $response['error'] = true;
        $response['error_code'] = 1;
        $response['value'] = "invalid";
        $response['message'] = "Please try again";
        echoResponse(201, $response);
    }

});


/*
URL: https://pragmanxt.com/pragma_demo_multivendor/Mobile_Services/delivery/v1//get_privacy
 * privacy policy
 * Parameters:
 * Method: get
*/


// get privacy policy
$app->post('/get_privacy', function () use ($app) {
    
    $typ = $app->request->post('typ');
    $db = new DbOperation();
    $result = $db->get_privacy($typ);
    $response = array();
    $response['error'] = false;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});





$app->post('/get_terms', function () use ($app) {
    

    $typ = $app->request->post('typ');
    $db = new DbOperation();
    $result = $db->get_terms($typ);
    $response = array();
    $response['error'] = false;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});


/*

 * view profile
 * Parameters:
 * Method: post
 * dev : jay
*/


$app->post('/view_profile', function () use ($app) {
    verifyRequiredParams(array('userid'));
    $userid = $app->request->post('userid');
    $db = new DbOperation();
    $result = $db->view_profile($userid);
    $response = array();
    $response['error'] = false;
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        if($row["owned_by"]=="vendor")
        {
            $owner_res=$db->get_myvendor($row["id"]);
            $owner=$owner_res["business_name"];
        }
        else
        {
            $owner="Sai Store";
        }
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp["owner"]=$owner;
        $temp = array_map('utf8_encode', $temp);
        array_push($response['data'], $temp);
    }
    echoResponse(200, $response);
});

/*
 * call notification
 * Parameters:
 * Method: post
 * dev : jay
*/





function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}


function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["error_code"] = 99;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

function authenticateUser(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    if (isset($headers['Authorization'])) {
        $db = new DbOperation();
        $api_key = $headers['Authorization'];
        if (!$db->isValidUser($api_key)) {
            $response["success"] = false;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        $response["success"] = false;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}



// fcm notificaton for android
function send_notification_android($data, $reg_ids_android, $title, $body,$send_to)
{

    $url = 'https://fcm.googleapis.com/fcm/send';
    if($send_to=="user")
    {
        $api_key = 'AAAAECnANz8:APA91bGYp0sVe-8WMW7EJt6SHsaHXplVfZb0jniq8kSuw62aruDgcfLkH_-lTSQR2tFu_NSexF7L9tl05c1N1LxcLbrry2q_vE8gv5k4_xXM8GQj32EJDPbJm-FeO532GPO2wp-9sg6K';
    }
    else
    {
        $api_key = 'AAAAECnANz8:APA91bGYp0sVe-8WMW7EJt6SHsaHXplVfZb0jniq8kSuw62aruDgcfLkH_-lTSQR2tFu_NSexF7L9tl05c1N1LxcLbrry2q_vE8gv5k4_xXM8GQj32EJDPbJm-FeO532GPO2wp-9sg6K';
    }
    
    $msg = array(
        'title' => $title,
        'body' => $body,
        'icon' => 'myicon',
        'sound' => 'custom_notification.mp3',
        'data' => $data
    );

    $fields = array(
        'registration_ids' => $reg_ids_android,
        'data' => $data,

    );
//print_r($fields);
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $api_key
    );

    // echo json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        //die('FCM Send Error: ' . curl_error($ch));
        $resp=0;
    }
    else{
        $resp=$result;
    }
    curl_close($ch);

    //  echo $result;
    return $resp;
}

// fcm notification code
function send_notification_ios($data, $reg_ids, $title, $body,$send_to)
{
    //$reg_ids[0]="esR5GsVCeEBljF0hszij-k:APA91bEq7A2QCl6Rrt8-__t7OlUemcQOIy_KRe0Zm6h50b8ffZcciHDdnT8f9poGAiW6gcqywi438TWt_aOLN0yk7YKgbOakkvrmTlvVUEtr98aiz69BsgoACxfHXztRmFx-0HprNxLy";
    $url = 'https://fcm.googleapis.com/fcm/send';
    if($send_to=="user")
    {
        $api_key = 'AAAAECnANz8:APA91bGYp0sVe-8WMW7EJt6SHsaHXplVfZb0jniq8kSuw62aruDgcfLkH_-lTSQR2tFu_NSexF7L9tl05c1N1LxcLbrry2q_vE8gv5k4_xXM8GQj32EJDPbJm-FeO532GPO2wp-9sg6K';
    }
    else
    {
        $api_key = 'AAAAECnANz8:APA91bGYp0sVe-8WMW7EJt6SHsaHXplVfZb0jniq8kSuw62aruDgcfLkH_-lTSQR2tFu_NSexF7L9tl05c1N1LxcLbrry2q_vE8gv5k4_xXM8GQj32EJDPbJm-FeO532GPO2wp-9sg6K';
    }
    $msg = array(
        'title' => $title,
        'body' => $body,
        'icon' => 'myicon',
        'sound' => 'custom_notification.mp3',
        'data' => $data
    );
    $fields = array(
        'registration_ids' => $reg_ids,
        'notification' => $msg
    );
//print_r($fields);
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $api_key
    );

    // echo json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
    //    die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    //  echo $result;
    return $result;
}

$app->run();
