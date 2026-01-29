<?php
session_start();
session_unset();
session_destroy();

// Volver SIEMPRE al home dinámico
header("Location: home.php");
exit;
