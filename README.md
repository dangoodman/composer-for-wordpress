# Composer for WordPress
A plugin for Composer package manager making it working a bit better within WordPress plugins.

For now, the only purpose of the project is to fix the following error when there is a WordPress plugin built with an obsolete pre-PSR-4 Composer version:
```
Fatal error: Call to undefined method Composer\Autoload\ClassLoader::setPsr4()
in <...>/wp-content/plugins/<plugin-name>/vendor/composer/autoload_real.php on line 33
```

The plugin patches Composer's autoload scripts every time they are changed due to `composer install`, `composer update` or other Composer actions.

<a href="https://github.com/composer/composer/issues/3852">More info and discussion</a>

## Installation

Just require the package in your project's `composer.json`:
```json
"require-dev": {
    "dangoodman/composer-for-wordpress": "^1.0"
}
```

You can set the class loader suffix in `composer.json`:
```json
"classloader-suffix": "MySuffix"
```
If you don't do that, a random suffix will be generated.
