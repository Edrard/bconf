<?php

function copyDir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0700, true);

    while(false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $srcPath = "$src/$file";
        $dstPath = "$dst/$file";

        if (is_dir($srcPath)) {
            copyDir($srcPath, $dstPath);
        } else {
            // Якщо файл існує і в цільовій папці, і він новіший — пропускаємо
            if (file_exists($dstPath) && filemtime($dstPath) >= filemtime($srcPath)) {
                echo "Skipped (newer exists): $dstPath\n";
                continue;
            }

            if (copy($srcPath, $dstPath)) {
                // Встановлюємо оригінальний timestamp
                touch($dstPath, filemtime($srcPath));
                echo "Copied: $srcPath -> $dstPath\n";
            } else {
                echo "Failed to copy: $srcPath\n";
            }
        }
    }

    closedir($dir);
}

function deleteDir($dirPath) {
    if (!is_dir($dirPath)) return;

    $items = scandir($dirPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = "$dirPath/$item";

        if (is_dir($path)) {
            deleteDir($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dirPath);
}
$baseDir = __DIR__.'/'.$argv[1]; // Або вкажи абсолютний шлях, якщо запускаєш не з цієї директорії
$folders = scandir($baseDir);

foreach ($folders as $folder) {
    if ($folder === '.' || $folder === '..') {
        continue;
    }

    $fullPath = $baseDir . '/' . $folder;

    if (is_dir($fullPath)) {
        $mikrotikPath = $fullPath . '/'.$argv[2];
        $mikrotikKeysPath = $fullPath . '/'.$argv[3];

        if (is_dir($mikrotikPath) && is_dir($mikrotikKeysPath)) {
            copyDir($mikrotikPath, $mikrotikKeysPath);
            deleteDir($mikrotikPath);
            echo "Copied from $mikrotikPath to $mikrotikKeysPath\n";
        }
    }
}