<?php
use Composer\InstalledVersions;

$rootDir = __DIR__ . '/../../../../../';

require_once $rootDir . 'vendor/autoload.php';

$packageName = 'php10-de/magd'; // Replace with your package name

$version = InstalledVersions::getVersion($packageName);
$prettyVersion = InstalledVersions::getPrettyVersion($packageName);

echo "Version: $version\n";
echo "Pretty Version: $prettyVersion\n";

$phpVersion = PHP_VERSION;
list($major, $minor) = explode('.', $phpVersion);

echo "PHP Version: $phpVersion\n";
echo "PHP Major Version: $major\n";
echo "PHP Minor Version: $minor\n";
$phpMajorMinor = $major . '.' . $minor;

$json = file_get_contents($rootDir . 'composer.json');
$data = json_decode($json, true);

$phpPlatformVersion = isset($data['config']['platform']['php']) ? $data['config']['platform']['php'] : null;

echo "PHP Platform Version: $phpPlatformVersion\n";

$ionPhpVersion = $phpPlatformVersion ? : $phpMajorMinor;

if (file_exists( $rootDir . 'src/inc/version.php')) {
    include_once $rootDir . 'src/inc/version.php';
    echo "HROSE Version: " . HROSE_VERSION . "\n";
} else {
    echo "HROSE Version: Not found\n";
}

if (HROSE_VERSION === $prettyVersion) {
    echo "HROSE Version matches the package version.\n";
    echo "✅ No further action. Done.\n";
}
function recursiveCopy(string $src, string $dst, array $omitDirs = []): void {
    if (!is_dir($src)) {
        echo "❌ Source directory does not exist: $src\n";
        return;
    }
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
            // Check if the destination file already exists and md5 hash is the same
            if (file_exists($dstPath) && md5_file($srcPath) === md5_file($dstPath)) {
                // echo "⏭️ Skipping identical file: $dstPath\n";
                continue;
            }
            copy($srcPath, $dstPath);
            echo "Copied: $dstPath\n";
        }
    }

    closedir($dir);
}

$sourceDir = __DIR__ . '/../';
$targetDir = $rootDir . 'src';

if (!is_dir($sourceDir)) {
    echo "❌ Source directory does not exist: $sourceDir\n";
    exit(1);
}

echo "🚀 Copying assets from $sourceDir to $targetDir\n";
recursiveCopy($sourceDir . 'assets/', $targetDir . '/assets');

$ionDirName = 'ionphp' .  str_replace('.', '', $ionPhpVersion);
echo "🚀 Copying files from version $sourceDir $version to $targetDir\n";
$omit = [$ionDirName];
recursiveCopy($sourceDir . $version . '/', $targetDir, $omit);

echo "🚀 Copying ioncube files from $sourceDir . $version . '/' . $ionDirName to $targetDir\n";
recursiveCopy($sourceDir . $version . '/' . $ionDirName . '/', $targetDir, $omit);

$versionFile = $rootDir . 'src/inc/version.php';
if (file_exists($versionFile)) {
    $versionContent = file_get_contents($versionFile);
    $newVersionContent = str_replace('COMPSER_VERSION', $prettyVersion, $versionContent);
    file_put_contents($versionFile, $newVersionContent);
    echo "Updated HROSE version in $versionFile\n";
} else {
    echo "❌ Version file not found: $versionFile\n";
}

$dbConnectSrc = $sourceDir . '../db/db_connect.dist.php';
$dbConnectDst = $rootDir . 'src/db/db_connect.php';
if (!file_exists($dbConnectDst)) {
    if (file_exists($dbConnectSrc)) {
        copy($dbConnectSrc, $dbConnectDst);
        echo "Copied: $dbConnectDst\n";
    } else {
        echo "❌ db_connect source file not found: $dbConnectSrc\n";
    }
} else {
    echo "⏭️ Skipping db_connect.php, already exists.\n";
}


$hroseIniSrc = $sourceDir . '../inc/hrose_ini.dist.php';
$hroseIniDst = $rootDir . 'src/inc/hrose_ini.php';
if (!file_exists($hroseIniDst)) {
    if (file_exists($hroseIniSrc)) {
        copy($hroseIniSrc, $hroseIniDst);
        echo "Copied: $hroseIniDst\n";
    } else {
        echo "❌ hrose_ini source file not found: $hroseIniSrc\n";
    }
} else {
    echo "⏭️ Skipping hrose_ini.php, already exists.\n";
}

$configIncSrc = $sourceDir . '../inc/config.inc.dist.php';
$configIncDst = $rootDir . 'src/inc/config.inc.php';
if (!file_exists($configIncDst)) {
    if (file_exists($configIncSrc)) {
        copy($configIncSrc, $configIncDst);
        echo "Copied: $configIncDst\n";
    } else {
        echo "❌ config.inc source file not found: $configIncSrc\n";
    }
} else {
    echo "⏭️ Skipping config.inc.php, already exists.\n";
}

$dockerComposeSrc = $sourceDir . '../docker-compose.dist.yml';
$dockerComposeDst = $rootDir . 'docker-compose.yml';
if (!file_exists($dockerComposeDst)) {
    if (file_exists($dockerComposeSrc)) {
        copy($dockerComposeSrc, $dockerComposeDst);
        echo "Copied: $dockerComposeDst\n";
    } else {
        echo "❌ docker-compose source file not found: $dockerComposeSrc\n";
    }
} else {
    echo "⏭️ Skipping docker-compose.yml, already exists.\n";
}

$dockerfileSrc = $sourceDir . '../Dockerfile.dist';
$dockerfileDst = $rootDir . 'Dockerfile';
if (!file_exists($dockerfileDst)) {
    if (file_exists($dockerfileSrc)) {
        copy($dockerfileSrc, $dockerfileDst);
        echo "Copied: $dockerfileDst\n";
    } else {
        echo "❌ Dockerfile source file not found: $dockerfileSrc\n";
    }
} else {
    echo "⏭️ Skipping Dockerfile, already exists.\n";
}

$gitignoreSrc = $sourceDir . '../.gitignore.dist';
$gitignoreDst = $rootDir . '.gitignore.dist';
if (file_exists($gitignoreSrc)) {
    copy($gitignoreSrc, $gitignoreDst);
    echo "Copied: $gitignoreDst\n";
} else {
    echo "❌ .gitignore source file not found: $gitignoreSrc\n";
}

echo "✅ Done.\n";