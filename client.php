<html>
<head>
<title>Client website</title>
</head>

<body>

<h1>Hi and welcome to the client site</h1>

<?php

$action = $_GET['action'];

if (isset($action)){
    switch($action){
        case "ab":
            handleAb();
            break;
        default:
            handleDefault();
    }
} else {

    echo '<p>So you want to use our sevices? Then i need data from the authz 
        controlled resource server. Please give us access by <a 
        href="authz.php">logging in</a></p>.';

}


?>



</body>
</html>

<?php

function handleDefault()
{
    echo "<p>Action not defined</p>";
}

?>
