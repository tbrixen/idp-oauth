<html>
<head>
<title>Client website</title>
</head>

<body>

<h1>Hi and welcome to BookFace (the client site).</h1>

<?php

$clientId = "23";
$clientSecret = "myveryownsecret";

// Setup action handler
if (isset($_GET['action'])){
    $action = $_GET['action'];
    switch($action){
        case "authzcode":
            handleAuthzCode($clientId, $clientSecret);
            break;
        case "accesstoken":
            handleAccessToken();
            break;
        case "returninfo":
            handleReturnInfo();
            break;
        default:
            handleDefault();
    }
} else {

    echo '<p>So you want to use our sevices? Then we need data from the authz 
        controlled resource server. The more data you\'ll give us, the more 
        points you\'ll get. Choose between:

        <ul>
        <li><a href="authz.php?action=redirect&clientid=23&responsetype=acg&scope=1">1 point</a>: First name</li>
        <li><a href="authz.php?action=redirect&clientid=23&responsetype=acg&scope=2">2 points</a>: First and last name</li>
        <li><a href="authz.php?action=redirect&clientid=23&responsetype=acg&scope=3">3 points</a>: First and last name + birthday</li>
        </ul>
        
        ';

}


?>



</body>
</html>

<?php

/**
 * Ok, know the client has gotten an code that it can use to access a specific
 * users information. Now to talk to the resource server, we need a access token
 * from the Authz server
 */
function handleAuthzCode($clientId, $clientSecret)
{
    // We have now gotten the user to accept our scope, and he has authenticaed himself
    $code = $_GET['code'];
    $scope = $_GET['scope'];
    $toSign = $_GET['tosign'];

    // We sign the message by encrypting it
    $key = $clientSecret;
    $iv =  '1234567890123456';
    $cc = $toSign;
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
    $signed = encrypt($cipher, $key, $iv, $cc);


    // We then need to get the access token so that us, the client, may get the data
    echo '<a href=' .
        '"authz.php' . 
         '?action=authzclient' .
         '&clientid=' . $clientId . 
         '&code=' . urlencode($code) . 
         '&tosign=' . $toSign . 
         '&signed=' . urlencode($signed) . 
        '">
        go to authz with clientId, code, tosign, and signed
      </a>';

}

/**
 * We have successfully authenticated ourselvs to the authz server, and have
 * gotten ourselvs an access token. Now we just need to use this to get the info
 */
function handleAccessToken()
{
    $token = $_GET['token'];


    // Call the resource server to get the info
    echo '<a href=' .
        '"resource.php' . 
         '?action=retreiveinfo' .
         '&token=' . $token . 
         '">
         goto resource with token
      </a>';

}

function handleReturnInfo()
{
    $info = $_GET['info'];

    echo "Thank you for your information. You'll get your points to your
        account as soon as we have sold your information.<br/>";
    echo "As a service, you are hereby presented with the information we are 
        trying to sell:<br /><br />";
    echo $info;
    echo "<br /><br />Get <a href=index.php>back</a>";
}


function handleDefault()
{
    echo "<p>Action not defined</p>";
}

function encrypt($cipher, $key, $iv , $data) {

            mcrypt_generic_init($cipher, $key, $iv);
            $encrypted = base64_url_encode(mcrypt_generic($cipher,$data));
            mcrypt_generic_deinit($cipher);

            return $encrypted;
}
function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_url_decode($input) {
 return base64_decode(strtr($input, '-_,', '+/='));
}

?>
