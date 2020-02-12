<?php
// Reads the variables sent via POST from our gateway
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];



if ($text == "") {
    // This is the first request. Note how we start the response with CON
    $response  = "CON welcome to cash in hand banking system \n";
    $response .= " 1. Register.\n";
    $response .= " 2. account details\n";
    $response .= " 3. account balance\n";
    $response .= " 4. inter banking\n";
    $response .= " 5. Subscribe for updates\n";


} else if ($text == "1") {
    // Business logic for first level response
    $response = "CON enter your details\n";
    $response .= "1. Account number and bank \n";
    $response .= "2. name";

} else if ($text == "2") {
    // Business logic for first level response
    // This is a terminal request. Note how we start the response with END
    $response = "END Your account number is  30,000";

} else if($text == "1*1") {
    // This is a second level response where the user selected 1 in the first instance
    $accountNumber  = "ACC1001";

    // This is a terminal request. Note how we start the response with END
    $response = "END Your account number is ".$accountNumber;

} else if ( $text == "1*2" ) {
    // This is a second level response where the user selected 1 in the first instance
    $balance  = "KES 10,000";

    // This is a terminal request. Note how we start the response with END
    $response = "END Your balance is ".$balance;
}

// Echo the response back to the API
header('Content-type: text/plain');
echo $response;