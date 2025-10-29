<?php
    require("../controllers/protect.php");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head> 
<body>
    <h1>Bem vindo ao Dashboard, 
        <?php echo $_SESSION["accountID"]; ?>
        ! 
    </h1>

    <p><a href="logout.php">Sair</a></p>
</body>
</html>