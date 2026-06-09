<?php
/**
 * Sustituye headers hardcodeados por navbar.js en todas las páginas HTML.
 */
$htmlDir = dirname(__DIR__, 2) . "/HTML";
$navBlock = "  <div id=\"navbar-container\"></div>\n  <script src=\"../JS/navbar.js\"></script>\n\n";

foreach (glob($htmlDir . "/*.html") as $path) {
    $content = file_get_contents($path);
    $original = $content;

    $content = preg_replace(
        '/<div id="navbar-container">.*?<\/div>\s*<script>\s*\(function\s*\(\)\s*\{[\s\S]*?header-html\.php[\s\S]*?<\/script>\s*/',
        $navBlock,
        $content,
        1
    );

    $content = preg_replace(
        '/<header class="navbar">[\s\S]*?<\/header>\s*/',
        $navBlock,
        $content,
        1
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo basename($path) . PHP_EOL;
    }
}

echo "Listo.\n";
