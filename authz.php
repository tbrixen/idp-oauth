<html>
<head>
<title>Auth server</title>
</head>

<body>
<h1>Hi and welcome to the authz server.</h1>
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

    echo '<p>Here you can login to authorize yoursef.</p>';

}


?>
</body>
</html>
