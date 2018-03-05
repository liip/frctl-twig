# About

frctl-twig is an adapter consisting of an NPM and a Composer package.
It integrates the [Twig PHP](https://twig.symfony.com) template engine into [fractal](https://fractal.build).

# Installation

Inside your fractal project add a composer package by adding a composer.json:

```
{
  "name": "my/fractal-project",
  "type": "project",
  "require-dev": {
    "liip/frctl-twig": "dev-master"
  },
}
```

Run `composer install`.

Add a `devDependencies` to the fractal twig adapter into your package.json:

    "frctl-twig": "git+https://github.com/liip/frctl-twig.git#master"

Run `npm install`.

## Adding Twig Extensions

Add any relevant composer packages to your composer.json.

For exampe run `composer require twig/twig-extensions`.

Then add a file `php-twig/TwigExtensions.php` to your fractal project with the following content:

```
<?php

namespace Frctl;

class TwigExtensions
{
    static public function getExtensions()
    {
        return [
            # Add your extensions here, for example the twig-extension text extension
            # new \Twig_Extensions_Extension_Text(),
        ];
    }
}
```

Then add the following section to your fractal project composer.json:

```
  "autoload": {
    "psr-4": {
      "Frctl\\": "php-twig/"
    }
  }

```

# How to use Twig templates in another project

Add the composer package pointing to your fractal project into the composer project of this other project.

Load all the extensions into your `Twig_Environment` instance:

```
    if (class_exists('Frctl\TwigExtensions')) {
        $extensions = \Frctl\TwigExtensions::getExtensions();
        foreach ($extensions as $extension) {
            $twig->addExtension($extension);
        }
    }
```

# Credits

The code is based on the work by Benjamin Milde:
* https://github.com/LostKobrakai/twig
* https://github.com/LostKobrakai/frctl-twig
