<?php

function recursiveCopy(string $src, string $dst, array $omitDirs = []): void {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);

    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $srcPath = "$src/$file";
        $dstPath = "$dst/$file";

        if (is_dir($srcPath) && in_array($file, $omitDirs)) {
            echo "⏭️ Skipping directory: $srcPath\n";
            continue;
        }

        if (is_dir($srcPath)) {
            recursiveCopy($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
            echo "Copied: $dstPath\n";
        }
    }

    closedir($dir);
}

$sourceDir = __DIR__ . '/../';
$targetDir = __DIR__ . '/../../../../../src';
$omit = ['scripts'];

if (!is_dir($sourceDir)) {
    echo "❌ Source directory does not exist: $sourceDir\n";
    exit(1);
}

echo "🚀 Copying files from $sourceDir to $targetDir\n";
recursiveCopy($sourceDir, $targetDir, $omit);

$dstPath = $targetDir . 'docker-compose.example.yml';
copy($sourceDir . '../docker-compose.yml', $targetDir . '/../docker-compose.example.yml');
echo "Copied: $dstPath\n";

echo "✅ Done.\n";