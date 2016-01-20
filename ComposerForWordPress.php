<?php
namespace Dangoodman\ComposerForWordPress;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;


class ComposerForWordPress implements PluginInterface, EventSubscriberInterface
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

        if (strpos(file_get_contents($classLoader), 'PSR-4') === false) {
            throw new \RuntimeException("
                Current composer autoload version does not seem to support PSR-4
                while 'Composer for Wordpress' composer plugin is supposed to work
                with PSR-4-compliant composer versions only. Disable the plugin if
                you don't need it."
            );
        }

        self::replaceInFiles(
            array($autoloadReal, $classLoader),
            array(
                'Composer\\Autoload;' => 'Composer\\AutoloadPsr4;',
                'Composer\\Autoload\\' => 'Composer\\AutoloadPsr4\\',
            )
        );
    }

    private static function replaceInFiles(array $files, array $replacements)
    {
        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $contents = strtr($contents, $replacements);
            file_put_contents($file, $contents);
        }
    }
}
