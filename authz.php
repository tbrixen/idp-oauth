<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
?>
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
        default:
            handleDefault();
    }
} else if (isset($_POST['commit'])){
    // Check of user credentials are corret
    if($_POST['username'] == "ole" && $_POST['password'] == "bole") {
        echo "correct";
    } else {
        echo "Not correct credentials.";
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
        case "42":
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
    echo "Would you like to give " . $clientName . " access to: 
        <ul> " . $scopeText . "</li><br />";

    echo "Then please login using the following form:";
    echo '
        <form method="post" action="authz.php">
        <input name="username" type="text" value="" placeholder="Username">
        <input name="password" type="password" value="" placeholder="Password">
        <input name="clientid" type="hidden" value="' . $clientId . '">
        <input name="responsetype" type="hidden" value="' . $responseType . '">
        <input name="scope" type="hidden" value="' . $scope . '">
        <input name="commit" type="submit" value="Login">
        </form>
        ';
    

    
}


function handleDefault()
{
    echo "<p>Action not defined</p><br />Get parameters: <br />";
    foreach($_GET as $key => $value){
        echo $key . " : " . $value . "<br />\r\n";
    }
}

?>
