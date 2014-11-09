<html>
<head>
<title>Client website</title>
</head>

<body>

<h1>Hi and welcome to BookFace (the client site).</h1>

<?php

$clientId = "42";
$clientSecret = "myveryownsecret";

// Setup action handler
if (isset($_GET['action'])){
    $action = $_GET['action'];
    switch($action){
        case "authzcode":
            handleAuthzCode($clientId, $clientSecret);
            break;
        default:
            handleDefault();
    }
} else {

    echo '<p>So you want to use our sevices? Then we need data from the authz 
        controlled resource server. The more data you\'ll give us, the more 
        points you\'ll get. Choose between:

        <ul>
        <li><a href="authz.php?action=redirect&clientid=42&responsetype=acg&scope=1">1 point</a>: First name</li>
        <li><a href="authz.php?action=redirect&clientid=42&responsetype=acg&scope=2">2 points</a>: First and last name</li>
        <li><a href="authz.php?action=redirect&clientid=42&responsetype=acg&scope=3">3 points</a>: First and last name + birthday</li>
        </ul>

        
        ';

}


?>



</body>
</html>

<?php

function handleAuthzCode($clientId, $clientSecret)
{
    // We have now gotten the user to accept our scope, and he has authenticaed himself
    $code = $_GET['code'];
    $scope = $_GET['scope'];

    echo $scope . " - " . $code;


    // We then need to get the access token so that us, the client, may get the data
    echo '<script type="text/javascript">
        window.location = ' . 
        '"authz.php' . 
         '?action=authzclient' .
         '&clientid=' . $clientId . 
         '&scope=' . $scope . 
         '&clientsecret=' . $clientSecret . 
         '&code=' . $code . 
        '"
      </script>';

}

function handleDefault()
{
    echo "<p>Action not defined</p>";
}

?>
