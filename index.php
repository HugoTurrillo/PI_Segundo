<?php
$marcaBD = __DIR__ . "/php/.bd_creada";

if (!file_exists($marcaBD)) {
    require __DIR__ . "/php/crear_bd.php";
    file_put_contents($marcaBD, "ok");
}

header("Location: HTML/home.html");
exit;
