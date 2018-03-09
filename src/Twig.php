<?php

namespace Frctl;

class Twig
{
    /*
     * Calculate the relative path from the template directory to the actual template file.
     *
     * Twig uses a root directory and all includes are based upon that directory.
     * The following examples clarify why it's necessary to specify he root directory independently
     * from the template file that should be rendered.
     *
     * Including partials/_partial.twig from index.twig at the root level would be fine when
     * rendering index.twig.
     *
     * Rendering partials/_partial.twig from sections/_section.twig would break because the root
     * directory is sections and Twig would try to incude sections/partials/_partial.twig.
     *
     * @param string $rootDir
     *    Path to the root directory where all templates live in.
     * @param string $fileDir
     *    Path to the template file that should be rendered.
     * @return string
     *    The relative path from the root directory to the template file's directory.
     */
    private static function getFilepathPrefix($rootDir, $fileDir)
    {
        // Get the path segments for each path.
        $rootChunks = explode('/', $rootDir);
        $fileChunks = explode('/', $fileDir);

        $prefixChunks = array_diff($fileChunks, $rootChunks);

        return $prefixChunks ? implode('/', $prefixChunks) . '/' : '';
    }

    /**
     * Renders a Twig template.
     *
     * @param string $entry
     *    The full path to the template.
     * @param array $options
     *    An optional array of options. Valid options can be found in the NPM package's README file.
     * @return string
     *    The rendered template.
     */
    public static function render($entry, $options = array())
    {
        $fileInfo = pathinfo($entry);

        $options = array_merge(array(
            'aliases' => array(),
            'context' => array(),
            'staticRoot' => ''
        ), $options);

        // Get the root template directory either from the given file or specified in the options.
        $isRootOption = array_key_exists('root', $options) && $options['root'];
        $rootDir = $isRootOption ? $options['root'] : $fileInfo['dirname'];

        $prefix = self::getFilepathPrefix($rootDir, $fileInfo['dirname']);
        $staticRoot = $options['staticRoot'];

        $loader = new \Twig_Loader_Chain(array(
            new Loader($options['aliases']),
            new \Twig_Loader_Filesystem($rootDir),
        ));

        $twig = new \Twig_Environment($loader, array('debug' => true));
        $twig->addExtension(new \Twig_Extension_Debug());

        $extensions = array();
        if (class_exists('Frctl\TwigExtensions')) {
            $extensions = \Frctl\TwigExtensions::getExtensions();
            foreach ($extensions as $extension) {
                $twig->addExtension($extension);
            }
        }

        $twig->addFunction(new \Twig_SimpleFunction('static', function ($path) use($staticRoot) {
            return rtrim($staticRoot, '/') . '/' . ltrim($path, '/');
        }));

        try {
            return $twig->render($prefix . $fileInfo['basename'], $options['context']);
        } catch (\Exception $e) {
            return self::createPrettyError($e->getMessage());
        }
    }

    /**
     * Creates a pretty looking page that displays the error message.
     *
     * @param string $message
     *    The error message to display.
     *
     * @return string
     */
    private static function createPrettyError($message = '') {
        return <<<EOT
<html>
  <head>
    <title>Twig Error</title>
    <style>
      @import 'https://fonts.googleapis.com/css?family=Roboto+Mono';
      body {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #0b0c12;
      }
      .error {
        color: #fff;
        padding: 10px 20px;
        font-size: 18px;
        border-left: 3px solid #a4d233;
        font-family: "Roboto Mono", monospace;
        margin: 20px;
      }
    </style>
  </head>
  <body>
    <div class="error">{$message}</pre>
  </body>
</html>
EOT;
    }
}
