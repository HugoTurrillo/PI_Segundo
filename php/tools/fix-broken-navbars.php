<?php
$htmlDir = dirname(__DIR__, 2) . "/HTML";

foreach (glob($htmlDir . "/*.html") as $path) {
    $content = file_get_contents($path);
    $original = $content;

    $content = preg_replace(
        '/(<script src="\.\.\/JS\/navbar\.js"><\/script>)\s*<\/div>\s*/',
        "$1\n\n",
        $content
    );

    $content = preg_replace(
        '/<script>\s*\(function\s*\(\)\s*\{[\s\S]*?header-html\.php[\s\S]*?<\/script>\s*/',
        '',
        $content
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo basename($path) . PHP_EOL;
    }
}

echo "OK\n";
