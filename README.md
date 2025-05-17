# magd
MAGD

## Installation
Add this to your composer.json:

    "scripts": 
    {
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
    ],
    "require": 
    {
      "php": ">=7.3",
     "php10-de/magd": "^1.0"
    },

run
`composer install`

## New Version
1. Update the version in composer.json
2. Add a new tag to the repository, e.g. `git tag -a v1.1.1 -m "Version 1.0.1"`
3. Push the tag to the repository, e.g. `git push origin v1.1.1`