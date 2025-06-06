<?php
session_start();
session_unset();
session_destroy();

// Desactiva caché para que no pueda regresar
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Location: login.php"); // redirige al login
exit();
?>