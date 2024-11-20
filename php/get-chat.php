<?php 
    session_start();
    function profile_photo($profile_photo,$gender)
    {
        if (empty($profile_photo) || !is_file($profile_photo)) {
            if ($gender == "M") {
                $profile_photo  = "./public/assets/dummy-profile-image-male.jpg";
            }else{
                $profile_photo  = "./public/assets/dummy-profile-image-female.jpg";
            }
        }

        return $profile_photo;
    }
    if(isset($_SESSION['unique_id'])){
        include_once "../connection.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($con, $_POST['incoming_id']);
        $output = "";
        $sql = "SELECT * FROM messages LEFT JOIN usertable ON usertable.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id asc";

        $query = mysqli_query($con, $sql);
        
        $sql1 = "SELECT gender,profile_photo from usertable, userprofile where usertable.uid=userprofile.uid and usertable.unique_id = {$incoming_id}";
        $query1 = mysqli_query($con, $sql1);
        while($row1 = mysqli_fetch_assoc($query1)){

            $prof_pic = profile_photo($row1['profile_photo'],$row1['gender']);
        }

        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'. $row['msg'] .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="'.$prof_pic.'" alt="">
                                <div class="details">
                                    <p>'. $row['msg'] .'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages yet.</br>Maybe you should make the first move.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }

?>