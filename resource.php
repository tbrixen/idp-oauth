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
        case "ab":
            handleAb();
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

function handleDefault()
{
    echo "<p>Action not defined</p>";
}

?>
