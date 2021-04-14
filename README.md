# Composer for WordPress
A plugin for Composer package manager making it working a bit better for WordPress plugins.

For now, the only purpose of the project is to fix the following error when there is a WordPress plugin built with an obsolete pre-PSR-4 Composer version:
```
Fatal error: Call to undefined method Composer\Autoload\ClassLoader::setPsr4()
in <...>/wp-content/plugins/<plugin-name>/vendor/composer/autoload_real.php on line 33
```

The plugin patches Composer's autoload scripts every time they are changed due to `composer install`, `composer update`, or other Composer actions.

<a href="https://github.com/composer/composer/issues/3852">More info and discussion</a>

## Installation

Require the package in your project's `composer.json`:
```json
{
    "require-dev": {
        "dangoodman/composer-for-wordpress": "^2.0"
    }
}
```

To set a custom class loader suffix add to your `composer.json`:
```json
{
    "config": {
        "classloader-suffix": "MySuffix"
    }
}
```
If it's not set, a random suffix will be generated instead.
