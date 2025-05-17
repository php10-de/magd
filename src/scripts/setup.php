<?php
use Composer\InstalledVersions;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$packageName = 'php10-de/magd'; // Replace with your package name

$version = InstalledVersions::getVersion($packageName);
$prettyVersion = InstalledVersions::getPrettyVersion($packageName);

echo "Version: $version\n";
echo "Pretty Version: $prettyVersion\n";

$phpVersion = PHP_VERSION;
list($major, $minor) = explode('.', $phpVersion);

echo "PHP Version: $phpVersion\n";

$json = file_get_contents(__DIR__ . '/../../../../../composer.json');
$data = json_decode($json, true);

$phpPlatformVersion = isset($data['config']['platform']['php']) ? $data['config']['platform']['php'] : null;

echo "PHP Platform Version: $phpPlatformVersion\n";

$ionPhpVersion = $phpPlatformVersion ? $phpPlatformVersion : $phpVersion;

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

echo "🚀 Copying assets from $sourceDir to $targetDir\n";
recursiveCopy($sourceDir . 'assets/', $targetDir, $omit);

echo "🚀 Copying files from version $sourceDir $version to $targetDir\n";
recursiveCopy($sourceDir . $version . '/', $targetDir, $omit);

echo "🚀 Copying ioncube  files from $sourceDir to $targetDir\n";
recursiveCopy($sourceDir . $ionPhpVersion . '/', $targetDir, $omit);

$dstPath = $targetDir . '/../docker-compose.example.yml';
copy($sourceDir . '../docker-compose.yml', $targetDir . '/../docker-compose.dist.yml');
echo "Copied: $dstPath\n";

$dstPath = $targetDir . '/../Dockerfile';
copy($sourceDir . '../Dockerfile', $targetDir . '/../Dockerfile.dist');
echo "Copied: $dstPath\n";

echo "✅ Done.\n";