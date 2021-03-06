<?php
session_start();
class db_functions{
    // variable declaration and initialization
    public $db_conn;

   public function connect_db(){
        $host="127.0.0.1";
        $port=3306;
        $socket="";
        $user="root";
        $password="";
        $dbname="multiuserauth";

        $conn = new mysqli($host, $user, $password, $dbname, $port, $socket);
        
        // Check connection
        if ($conn->connect_error) {
            return ("Could not connect to the database server: " . $conn->connect_error);
        }else{
            return $conn;
        }
    
   }
        
    public function login_user($con, $user_name, $user_pass){
        // echo ($user_name.' '. $user_pass).'<BR>';

        $sql = "SELECT usr_id, usr_status, roles_role_id, usr_name FROM users_view WHERE usr_name='$user_name' AND usr_pass='$user_pass'";
        $result = $con->query($sql);

        // confirm the user 
        if ($row = $result->fetch_assoc()) { 
            // update the logs
                // check if the user is authenticated
                if($row['usr_status'] == 1 && $row['roles_role_id'] == 1){
                    // authenticated got to landing page
                      // set auth session to the role id and user session to user id

                    $_SESSION['logon_user'] = [
                                                'user_id'=>$row['usr_id'],
                                                'user_name'=>$row['usr_name'], 
                                                'role_id'=> $row['roles_role_id'] 
                                            ];
                    // fetch rights 
                    $id =$row['usr_id'];
                            $sql2 = "SELECT * FROM rights_view WHERE user_id='$id'";
                            $right_result = $con->query( $sql2);
                            if($row_r = $right_result->fetch_assoc()){
                                // set user right session
                             print_r( $_SESSION['user_rights'] = $row_r);
                              // redirect user to the landing page
                                header('location: home/landing.php');
                            }else{
                                // back to home page 
                                // ask user to contact admin for rights aproval 
                                $_SESSION['alert'] = [
                                        'info', 
                                        'Sorry you have no rights to access these page<br><i><b>Kindly contact the 
                                        <a href="#" class="alert-link">ADMIN</a> for assistance</b></i>'
                                    ];
                             header('location: ../index.php'); 
                            }

                   
                }else{
                    // blocked by admin
                    // redirect back to the login page with an admin message 
                    $_SESSION['alert'] = [
                                        'danger', 
                                        'Sorry your account has been locked for now<br><i><b>Kindly contact the 
                                        <a href="#" class="alert-link">ADMIN</a> for assistance</b></i>'
                                    ];
                    header('location: ../index.php');
                }           
          
        }else{

           $_SESSION['alert'] = ['warning', 'username or password is incorrect'];

            header('location: ../index.php');
            
               
              
        }
        
    }
}
?>
