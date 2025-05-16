# magd
MAGD

## Installation
Add this to your composer.json:
  "scripts": {
    "post-install-cmd": [
      "php vendor/php10-de/magd/src/scripts/setup.php"
    ],
    "post-update-cmd": [
      "php vendor/php10-de/magd/src/scripts/setup.php"
    ]
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/php10-de/magd"
    },
    {
      "type": "vcs",
      "url": "https://github.com/php10-de/bigredbutton"
    }
    ],
    "require": {
    "php": ">=7.3",
    "php10-de/magd": "^1.0"
  },`

run
`composer install`