<?php 
include('arbpg.php');

$trandata= $_POST['trandata'];
var_dump($trandata);
$arbPg = new ArbPg();

$result  = $arbPg->getresult($trandata);


echo $result;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>

    <div>
        <a href="index.php">HOME</a>
    </div>
</body>
</html>