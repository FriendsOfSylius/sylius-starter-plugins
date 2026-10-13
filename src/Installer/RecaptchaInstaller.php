<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Installer;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\InstallerInterface;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Docker;
use Sylius\Starter\Core\Util\Symfony;
use Sylius\Starter\Core\Util\Yaml;
use Sylius\Starter\Plugins\PluginResourceCopier;

use function Castor\io;

final readonly class RecaptchaInstaller implements InstallerInterface
{
    public function name(): string
    {
        return 'recaptcha';
    }

    public function description(): string
    {
        return 'Add Google reCAPTCHA v3 to customer registration';
    }

    public function __invoke(App $app): void
    {
        io()->title('Adding Google reCAPTCHA plugin');

        Composer::allowContribRecipes($app);
        Docker::run($app, 'composer require karser/karser-recaptcha3-bundle');

        Yaml::import($app, 'config/packages/_sylius.yaml', '../sylius/twig_hooks/**/**.php');

        PluginResourceCopier::copy($app, 'recaptcha');
        Symfony::cacheClear($app);
    }
}
