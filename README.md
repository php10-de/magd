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

## New Develop Version
To create a new version for development, follow these steps:
1. Create a new branch from the main branch, e.g. `git checkout -b specialfeature`
2. Make your changes and commit them, e.g. `git commit -m "Added a new feature"`
3. Push the branch to the repository, e.g. `git push origin specialfeature`
4. In your consuming project, update the composer.json to require the new branch, e.g. `"php10-de/magd": "dev-specialfeature"`
5. Run `composer update` to install the new branch.
6. Test the new feature in your consuming project.
7. Once the feature is stable, merge it back into the main branch.
8. In your consuming project, update the composer.json to require the main branch again, e.g. `"php10-de/magd": "^1.1"`

## New Release Version
1. Update the version in composer.json
2. Add a new tag to the repository, e.g. `git tag -a v1.1.1 -m "Version 1.0.1"`
3. Push the tag to the repository, e.g. `git push origin v1.1.1`