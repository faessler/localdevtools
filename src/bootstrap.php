<?php
// Inlude autoload, if installed via composer
$autoloaderViaComposer = __DIR__ . '/../../../autoload.php';
if (file_exists($autoloaderViaComposer)) {
    require_once $autoloaderViaComposer;
// ... or if just cloned as a package
} else {
    require_once __DIR__.'/../vendor/autoload.php';
}

define('LOCAL_DEV_TOOLS_ROOT', __DIR__);

use Symfony\Component\Console\Application;

$localDevTools = new Application();
$localDevTools->setName('Local Dev Tools');
$localDevTools->setVersion('beta-0.1.0');

$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\SetupCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Configuration\DiagnoseCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Code\FixCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Server\RestartCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Project\CreateCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Database\CreateCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Database\ImportCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Database\DumpCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Database\CopyFromRemoteCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Link\LinkCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\Link\LinkSetupCommand());
$localDevTools->add(new \Glowpointzero\LocalDevTools\Command\InfoCommand());

$localDevTools->setDefaultCommand(\Glowpointzero\LocalDevTools\Command\InfoCommand::COMMAND_NAME);
$localDevTools->run();
