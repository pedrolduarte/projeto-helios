<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION["accountID"])) {
        header("Location: ../view/error.php");
        exit;
    }

    if (!isset($_SESSION["clientID"])) {
        header("Location: ../view/error.php");
        exit;
    }
?>