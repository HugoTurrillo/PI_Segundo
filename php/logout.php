<?php
/**
 * Cierro la sesión del usuario y redirijo siempre al home.
 */

session_start();
session_unset();
session_destroy();

header("Location: home.php");
exit;
