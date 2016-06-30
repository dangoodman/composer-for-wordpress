<?php
namespace Dangoodman\ComposerForWordpress;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;


class ComposerForWordpress implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_AUTOLOAD_DUMP => array(
                array('onPostAutoloadDump', 0)
            ),
        );
    }

    public function onPostAutoloadDump(Event $event)
    {
        $composerAutoloadDir = "{$event->getComposer()->getConfig()->get('vendor-dir')}/composer";

        $classLoader = "{$composerAutoloadDir}/ClassLoader.php";
        $autoloadReal = "{$composerAutoloadDir}/autoload_real.php";
        $autoloadStatic = "{$composerAutoloadDir}/autoload_static.php";

        if (strpos(file_get_contents($classLoader), 'PSR-4') === false) {
            throw new \RuntimeException("
                Current composer autoload version does not seem to support PSR-4
                while 'Composer for Wordpress' composer plugin is supposed to work
                with PSR-4-compliant composer versions only. Disable the plugin if
                you don't need it."
            );
        }

        self::replaceInFiles(
            array($classLoader, $autoloadReal),
            '/Composer\\\\Autoload(;|\\\\(?!ComposerStaticInit))/',
            'Composer\\AutoloadPsr4$1'
        );

        self::replaceInFiles(
            array($autoloadStatic),
            array(
                '/\bClassLoader\b/'
                    => "ClassLoaderPsr4",
                '/'.preg_quote("\nnamespace Composer\\Autoload;\n", '/').'/'
                    => "$0\nuse Composer\\AutoloadPsr4\\ClassLoader as ClassLoaderPsr4;\n\n",
            )
        );
    }

    private static function replaceInFiles(array $files, $search, $replace = null)
    {
        if (func_num_args() == 3) {
            $search = array($search => $replace);
        }

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $contents = preg_replace(array_keys($search), array_values($search), $contents);
            file_put_contents($file, $contents);
        }
    }
}