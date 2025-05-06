<?php 

    ob_start();
    echo "Testing2";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
     <h1> 
        <?php 
            echo ob_get_clean();
        ?>
     </h1>
</body>
</html>