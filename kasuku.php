<?php
//connections required
require_once ('AfricasTalkingGateway.php');
require_once ('config.php');
require_once ('connection.php');

// Get the parameters provided by Africa's Talking USSD gateway
/*$phoneNumber = $_GET['phoneNumber'];
$sessionId = $_GET['sessionId'];
$serviceCode = $_GET['serviceCode'];
$text= $_GET['text'];
*/
/*Connection Credentials
$servername = 'localhost';
$username = 'root';
$password = "";
$database = "kasuku";
$dbport = 3306;


// Create connection
$conn = new mysqli($servername, $username, $password, $database, $dbport);

// Check connection
if ($conn->connect_error) {
    header('Content-type: text/plain');
    //log error to file/db $e-getMessage()
    die("END An error was encountered. Please try again later");
}
*/
//receive the POSTs from AfricaStalking
$sessionId=$_POST['sessionId'];
$serviceCode=$_POST['serviceCode'];
$phoneNumber=$_POST['phoneNumber'];
$text=$_POST['text'];

//explored text to get value of the latest interaction using textExplored function
$textArray=explode('*', $text);
$userResponse=trim(end($textArray));

//Set user level to zero(default level of user)
$level=0;

//check the level of user from the db and retain to zero if none is found
$sqlLv = "SELECT level FROM session_levels WHERE session_id ='".$sessionId." '";
$levelQuery = mysqli_query( $conn, $sqlLv);
if($resultLv = mysqli_fetch_assoc($levelQuery)) {
    $level = $resultLv['level'];
}


//=======================check if user is in the database====================================================

$sqlCheckUser="SELECT * FROM kasuku WHERE phoneNumber LIKE '%".$phoneNumber."%' LIMIT 1";
//$resultsCheckUser=mysqli_query($conn,$sqlCheckUser);
//$userAvailability=mysqli_fetch_assoc($resultsCheckUser);
$userQuery = mysqli_query($conn, $sqlCheckUser);
$userAvailability = mysqli_fetch_assoc( $userQuery);


//if the user is available, serve the menu,, else prompt for registration

if($userAvailability && $userAvailability['name'] !=NULL && $userAvailability['phoneNumber'] !=NULL && $userAvailability['acc_num'] !=NULL){
//    set level to zero
    if($level==0 || $level==1){
        switch ($userResponse) {
            case "":
                if ($level == 0) {
                    //             update user level and set to 1 and display the menu
                    $sqluserLvl = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('" . $sessionId . "','" . $phoneNumber . "',1)";
                    $resultsUserLvl = mysqli_query($conn, $sqluserLvl);
                    //   serve the menu
                    $response = "CON Welcome to CASH IN HAND " . $userAvailability['name'] . ". Choose a service.\n";
                    $response .= " 1. Register.\n";
                    $response .= " 2. account details\n";
                    $response .= " 3. account balance\n";
                    $response .= " 4. inter banking\n";
                    $response .= " 5. Subscribe for updates\n";


                    header('Content-type: text/plain');
                    echo $response;
                }
                break;

            case "0":
                if ($level == 0) {
//                    update user to the next level
                    $sqluserLvl1 = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('" . $sessionId . "','" . $phoneNumber . "',1)";
                    $resultsUserLvl1 = mysqli_query($conn, $sqluserLvl1);
                    //   serve/ display the menu
                    $response = "CON Welcome to CASH IN HAND " . $userAvailability['name'] . ". Choose a service.\n";
                    $response .= " 1. Register.\n";
                    $response .= " 2. account details\n";
                    $response .= " 3. account balance\n";
                    $response .= " 4. inter banking\n";
                    $response .= " 5. Subscribe for updates\n";

                    header('Content-type: text/plain');
                    echo $response;

                }
                break;

            case "1":
                if ($level == 1) {
                    // Check if user is registered
                    $sqlReg = "SELECT * FROM kasuku WHERE phoneNumber LIKE '%" . $phoneNumber . "%' LIMIT 1 ";
                    $resultsReg = mysqli_query($conn, $sqlReg);
                    $checkReg = mysqli_num_rows($resultsReg);
                    if ($checkReg > 0) {
                        $response = "END You're already registered.\nPress 0 to main menu.";

                        $sqlLevelDemote = "UPDATE `session_levels` SET `level`=0 where `session_id`='" . $sessionId . "'";
                        $conn->query($sqlLevelDemote);

                    } else {
                        $response = "END Your data not matching or does not exist\n";
                    }

                    // Print the response onto the page so that our gateway can read it
                    header('Content-type: text/plain');
                    echo $response;
                }
                break;

            case "2":
                if ($level == 1) {
//                    select user data from database and print out
                    $response = " USER INFORMATION:\n";
                    $sql3 = "SELECT * FROM kasuku WHERE phoneNumber='$phoneNumber'";
                    $result3 = mysqli_query($conn, $sql3);
                    $checkData = mysqli_num_rows($result3);

                    while ($row3 = mysqli_fetch_array($result3)) {
                        $name3 = $row3['name'];
                        $acc_num3 = $row3['acc_num'];


                    }
                    if ($checkData == 0) {
                        $response = "END user information for provided phone number does not exist.Please visit the bank to update data.\n";
                    } elseif ($checkData == 1) {
                        //display user information
                        $response .= "END CASH AT HAND.\n";
                        $response .= "Name: " . $name3 . "\n";
                        $response .= "Account number: " . $acc_num3 . "\n";


                    }
                    // Print the response onto the page so that our gateway can read it
                    header('Content-type: text/plain');
                    echo $response;
                }
                break;

            case "3":
                if ($level == 1) {
                    // select user data from database and print out
                    $response = " USER BALANCE:\n";
                    $sqlbal = "SELECT * FROM kasuku WHERE phoneNumber='$phoneNumber'";
                    $resultbal = mysqli_query($conn, $sqlbal);
                    $checkbal = mysqli_num_rows($resultbal);

                    while ($rowbal = mysqli_fetch_array($resultbal)) {
                        $namebal = $rowbal['name'];
                        $acc_numbal = $rowbal['acc_num'];
                        $acc_balbal = $rowbal['acc_bal'];


                    }
                    if ($checkbal == 0) {
                        $response = "END user information for provided phone number does not exist.Please visit the bank to update data.\n";
                    } elseif ($checkbal == 1) {
                        //display user information
                        $response .= "END CASH AT HAND.\n";
                        $response .= "ACCOUNT BALANCE: " . $acc_balbal . "\n";


                    }
                    // Print the response onto the page so that our gateway can read it
                    header('Content-type: text/plain');
                    echo $response;

                }
                break;

            case "4":
                if ($level == 1) {
                    $response .= "END this is the interbank service.\n";
                    header('Content-type: text/plain');
                    echo $response;
                }

                break;

            case "5":
                if ($level == 1) {
                    $response = "END Thank you for banking with us. we have no updates for now.\n";

                    // Print the response onto the page so that ussd gateway can read it
                    header('Content-type: text/plain');
                    echo $response;
                }
                break;

            default:
                if ($level == 1) {
                    // Return user to Main Menu & Demote user's level
                    $response = "CON You have to choose a service.\n";
                    $response .= "Press 0 to go back.\n";
                    //demote
                    $sqlLevelDemote = "UPDATE `session_levels` SET `level`=0 where `session_id`='" . $sessionId . "'";
                    $conn->query($sqlLevelDemote);

                    // Print the response onto the page so that our gateway can read it
                    header('Content-type: text/plain');
                    echo $response;
                }
        }

        }

    } else{
//    register user
//    check user response is not empty
    if($userResponse==""){
        switch ($level){
            case 0:
                //            update user to the next level so you dont serve them the same menu
                $sql10b = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."', 1)";
                $conn->query($sql10b);

                //Insert the phoneNumber, since it comes with the first POST
                $sql10c = "INSERT INTO kasuku(`phoneNumber`) VALUES ('".$phoneNumber."')";
                $conn->query($sql10c);

                //Serve the menu request for name
                $response = "CON Please enter your Name";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;

            case 1:
                // Request again for name - level has not changed...
                $response = "CON Name not supposed to be empty. Please enter your name \n";

                // Print the response onto the page so that gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;

            case 2:
                //10f. Request for account number again --- level has not changed...
                $response = "CON Account not supposed to be empty. Please reply with  Account number\n";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;

            default:
                //10g. End the session
                $response = "END Apologies, something went wrong. \n";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;

        }
    }else{
//        if not empty, update user details
        switch ($level) {
            case 0:
                //Graduate the user to the next level, so you dont serve them the same menu
                $sqlb = "INSERT INTO `session_levels`(`session_id`, `phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."', 1)";
                $conn->query($sqlb);

                // Insert the phoneNumber, since it comes with the first POST
                $sqlc = "INSERT INTO kasuku (`phoneNumber`) VALUES ('".$phoneNumber."')";
                $conn->query($sqlc);

                //10d. Serve the menu request for name
                $response = "CON Please enter your name";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;
            case 1:
                //Update Name, Request for city
                $sql11b = "UPDATE kasuku SET `name`='".$userResponse."' WHERE `phoneNumber` LIKE '%". $phoneNumber ."%'";
                $conn->query($sql11b);

                //graduate the user to the admission level
                $sql11c = "UPDATE `session_levels` SET `level`=2 WHERE `session_id`='".$sessionId."'";
                $conn->query($sql11c);

                //request for the admission
                $response = "CON Please enter your account number";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;
            case 2:
                //11d. Update city
                $sql11d = "UPDATE kasuku SET `acc_num`='".$userResponse."' WHERE `phoneNumber` = '". $phoneNumber ."'";
                $conn->query($sql11d);

                //11e. Change level to 0
                $sql11e = "INSERT INTO `session_levels`(`session_id`,`phoneNumber`,`level`) VALUES('".$sessionId."','".$phoneNumber."',1)";
                $conn->query($sql11e);

                //11f. Serve the menu request for name
                $response = "END You have been successfully registered to CASH IN HAND. Dial *384*303030 # to choose a service.";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;
            default:
                //11g. Request for city again
                $response = "END Apologies, something went wrong... \n";

                // Print the response onto the page so that our gateway can read it
                header('Content-type: text/plain');
                echo $response;
                break;
        }
    }

}
?>