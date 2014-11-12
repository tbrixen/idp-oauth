<html>
<head>
<title>Resource Server</title>
</head>

<body>
<h1>Hi and welcome to the resource server.</h1>
<?php

$action = $_GET['action'];

if (isset($action)){
    switch($action){
        case "retreiveinfo":
            handleRetreiveInfo();
            break;
        default:
            handleDefault();
    }
} else {
    echo '<p>You can only use this if you have a valid access token.</p>';
}


?>
</body>
</html>
<?php
function handleRetreiveInfo()
{
    $token = $_GET['token'];

    // The token is the encryption of the code, hence, to get the info, we
    // need to decrypt twice

    $key = '73f8d4969098400c44dcb50111eb4193';
    $iv =  '1234567890123456';
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');

    $code = decrypt($cipher, $key, $iv, $token);

    $message = decrypt($cipher, $key, $iv, $code);

    echo "token: " . $token;
    echo "<br />code: " . $code;
    echo "<br />message: " . $message;


    // Check that thet we have valid data is correct
    $parts = explode(" ", $message);
    $oauth = $parts[0];
    $clientId = $parts[1];
    $scope = $parts[2];
    $username = $parts[3];

    // If first part is the string oauth, it's decrypted properly.
    if ($oauth == "oauth"){
        echo "Token is ok";
        $info = getInfo($username, $scope);


    // Call the resource server to get the info
    echo '<a href=' .
        '"client.php' . 
         '?action=returninfo' .
         '&info=' . urlencode($info) . 
        '">
        Go to client with info
      </a>';

    }
}


function handleDefault()
{
    echo "<p>Action not defined</p>";
}

function decrypt($cipher, $key, $iv , $data) {

            mcrypt_generic_init($cipher, $key, $iv);
            $decrypted = mdecrypt_generic($cipher,base64_url_decode($data));
            mcrypt_generic_deinit($cipher);

            return $decrypted;
}


function getInfo($username, $scope)
{
    if (trim($username) == "ole"){
        switch($scope){
            case "1":
                return "Firstname: Ole";
                break;
            case "2":
                return "Firstname: Ole, Lastname: Bole";
                break;
            case "3":
                return "Firstname: Ole, Lastname: Bole, Birthday 24/12-1970";
                break;
        }
    }
}

function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_url_decode($input) {
 return base64_decode(strtr($input, '-_,', '+/='));
}?>
