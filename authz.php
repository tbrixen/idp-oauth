<html>
<head>
<title>Auth server</title>
</head>

<body>
<h1>Hi and welcome to the authz server.</h1>
<?php


if (isset($_GET['action'])){
    $action = $_GET['action'];

    switch($action){
        case "redirect":
            handleRedirect();
            break;
        case "authzclient":
            handleAuthzClient(
                $_GET['clientid'], 
                urldecode($_GET['code']), 
                $_GET['tosign'], 
                urldecode($_GET['signed']));
            break;
        default:
            handleDefault();
    }
} else if (isset($_POST['commit'])){
    // Check of user credentials are corret
    $username = $_POST['username'];
    $password = $_POST['password'];
    $correctLogin = $username == "ole" && $password == "bole";

    if(!$correctLogin) {
        echo "Not correct credentials.";
    } else {
        $clientId = $_POST["clientid"];
        $responsetype = $_POST["responsetype"];
        $scope = $_POST["scope"];

        handleCorrectUserLogin($clientId, $responsetype, $scope, $username);
    }
} else {
    // An actions hasn't been set, and there hasn't been amy attempt to log on


    echo '<p>Here you can login to authorize yoursef.</p>';

}


?>
</body>
</html>
<?php

function handleRedirect()
{
    $clientId = $_GET['clientid'];
    $responseType = $_GET['responsetype'];
    $scope = $_GET['scope'];


    $clientName = "undefined";
    switch($clientId){
        case "23":
            $clientName = "BookFace";
    }

    $scopeText = "undefined";
    switch($scope){
        case "1":
            $scopeText = "<li>First name</li>";
            break;
        case "2":
            $scopeText = "<li>First name</li><li>Last name</li>";
            break;
        case "3":
            $scopeText = "<li>First name</li><li>Last 
                name</li><li>Birthday</li>";
            break;
    }

    // The user should now get to authorize 
    echo "Would you like to give <b>" . $clientName . "</b> access to: 
        <ul> " . $scopeText . "</li><br />";

    echo "Then please login using the following form:";
    echo '
        <form method="post" action="authz.php">
        <input name="username" type="text" value="" placeholder="Username">
        <input name="password" type="password" value="" placeholder="Password">
        <input name="clientid" type="hidden" value="' . $clientId . '">
        <input name="responsetype" type="hidden" value="' . $responseType . '">
        <input name="scope" type="hidden" value="' . $scope . '">
        <input name="commit" type="submit" value="Accept by login">
        </form>
        ';
    

    
}

/**
 * If the user has provided the correct log in details, we generate a
 * authz code that the client can use
 */
function handleCorrectUserLogin($clientId, $responseType, $scope, $username)
{
    echo "handleCorrectUserLogin <br />";
    echo $clientId;
    echo $responseType;
    echo $scope;
    echo $username . "<br />";

    // Generate authz token
    // Properties of the authztoken:
    // Its a token to see that the user has authorized the client on some scope
    // hence, wee need to have:
    // * The user
    // * The client
    // * The scope

    // We prepend some known string, so that we can check for a valid 
    // decryption result later on
    $knownString = "oauth";
    $key = '73f8d4969098400c44dcb50111eb4193';
    $iv =  '1234567890123456';
    $cc = $knownString . " " .  $clientId . " " . $scope . " " . $username;

    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');

    $encryptedData = encrypt($cipher, $key, $iv, $cc);
    
    echo "data to encrypt: " . $cc . "<br />";
    echo "encrypted data data: " . $encryptedData . "<br />";


    $dev = decrypt($cipher, $key, $iv, $encryptedData);
    echo "decrypted encrypted data: " . $dev . "<br />";
    // Redirect the user
    echo '<a href=' .
        '"client.php' . 
         '?action=authzcode' .
         '&code=' . urlencode($encryptedData) . 
         '&scope=' . $scope . 
         '&tosign=' . substr( md5(rand() ), 0, 32 ) . 
        '">
        Go to client with code, scope and tosign
      </a>';
}


/** We have gotten a request from a client that wants to autheticate itself
 * and get an access token to the resource server
 */
function handleAuthzClient($clientId, $code, $toSign, $signed)
{
    printGet();

    // Look up the client secret in our database
    // SELECT secret FROM client WHERE clientId = $clientId
    $clientSecret = "myveryownsecret";
    $authSuccess = false;
    $codeSuccess = false;


    // Authenticate the client by decrypting the 'signed' in order to check
    // that the client actually has the secret key.
    
    $key = $clientSecret;
    $iv =  '1234567890123456';
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');

    $h = decrypt($cipher, $key, $iv, $signed);
    echo "Decrypted signed message: " . $h . "<br/>";
    if ($h == $toSign){
        $authSuccess = true;
    }

    // Check that the code is correct

    $key = '73f8d4969098400c44dcb50111eb4193';

    $m = decrypt($cipher, $key, $iv, $code);
    echo "Decrypted code: " . $m . "<br/>";
    $parts = explode(" ", $m);
    if ($parts[0] == "oauth" && $parts[1] == $clientId){
        $codeSuccess = true;
    }


    if ( $authSuccess && $codeSuccess ){
        // Make an access token
        $token = encrypt($cipher, $key, $iv, $code);

        // We redirect back to the client
        echo '<a href=' .
            '"client.php' . 
             '?action=accesstoken' .
             '&token=' . $token . 
            '">
            Go to client with the token
          </a>';
    } else {
        echo "There was an error in either the code or the signing process";
    }
}

function handleDefault()
{
    echo "<p>Action not defined</p><br />Get parameters: <br />";
    printGet();
}

function printGet()
{
    foreach($_GET as $key => $value){
        echo $key . " : " . $value . "<br />\r\n";
    }
    echo "<br /><br />";
}
function encrypt($cipher, $key, $iv , $data) {

            mcrypt_generic_init($cipher, $key, $iv);
            $encrypted = base64_url_encode(mcrypt_generic($cipher,$data));
            mcrypt_generic_deinit($cipher);

            return $encrypted;
}

function decrypt($cipher, $key, $iv , $data) {

            mcrypt_generic_init($cipher, $key, $iv);
            $decrypted = mdecrypt_generic($cipher,base64_url_decode($data));
            mcrypt_generic_deinit($cipher);

            return $decrypted;
}
function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_url_decode($input) {
 return base64_decode(strtr($input, '-_,', '+/='));
}
?>
