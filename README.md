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

For example run `composer require twig/twig-extensions`.

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

Adjust the file loader to be able to find the twig templates in the fractal project:

```
class TwigFilesystemLoader extends BaseTwigFilesystemLoader
{
    /**
     * Should probably be set via a setter from configuration
     *
     * @var string
     */
    private $fractalPath = '/path/to/fractal/twig/templates';

    /**
     * @param string $name
     *
     * @return string
     */
    protected function findTemplate($name)
    {
        $fractalPath = $this->getFractalPath();
        if ($fractalPath && preg_match('/^@fractal-(.*)$/', $name, $templatePath)) {
            $fullFilePath = $fractalPath . '/' . $templatePath[1];

            return $fullFilePath;
        }

        ...
    }
}
```

Load all the extensions into your `Twig_Environment` instance:

```
    if (class_exists('Frctl\TwigExtensions')) {
        $extensions = \Frctl\TwigExtensions::getExtensions();
        foreach ($extensions as $extension) {
            $twig->addExtension($extension);
        }
    }
```

# Configuration options

By default, the `strict_variables` flag of twig is set to `false`, the `debug` flag is set to `true` and the `autoescape` flag is set to `true`.
To change these variables, pass them in an optional config object with their desired values while configuring 
fractal:

```
    const frctlTwig = require("frctl-twig");
    
    fractal.components.engine(frctlTwig({
        strict_variables: true, // Or false
        debug: false, // Or true
        autoescape: false, // Or true
    }));
    
    // Further setup...
```

## Twig namespaces

You can also register Twig namespaces in the following way:

```
    const frctlTwig = require('frctl-twig');

    fractal.components.engine(frctlTwig({
        namespaces: {
            atoms: {
                paths: ['00-atoms']
            },
            molecules: {
                paths: ['01-molecules']
            },
            organisms: {
                paths: ['02-organisms']
            },
            templates: {
                paths: ['03-templates']
            },
            pages: {
                paths: ['04-pages']
            }
        }
    }));

    // Further setup...
```

The above example creates five namespaces named after the stages of atomic design. The paths are always relative paths from the Fractal components root directory.

# Credits

The code is based on the work by Benjamin Milde:
* https://github.com/LostKobrakai/twig
* https://github.com/LostKobrakai/frctl-twig
