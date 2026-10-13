<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Installer;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\InstallerInterface;
use Sylius\Starter\Core\Util\Assets;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Database;
use Sylius\Starter\Core\Util\Docker;
use Sylius\Starter\Core\Util\Symfony;

use function Castor\io;

final readonly class CmsInstaller implements InstallerInterface
{
    public function name(): string
    {
        return 'cms';
    }

    public function description(): string
    {
        return 'CMS plugin for Sylius applications';
    }

    public function __invoke(App $app): void
    {
        io()->title('Adding CMS plugin');

        Composer::allowContribRecipes($app);
        // sylius/cms-plugin requires api-platform/state ^4, while Sylius 2.3 installs API Platform 5 by default.
        // Without an explicit constraint, Composer silently falls back to v1.0.0, which ships no JS package.
        // Pinning ^1.1 and allowing dependency updates (-W) downgrades API Platform to 4.x (still supported by Sylius 2.3).
        Docker::run($app, 'composer require "sylius/cms-plugin:^1.1" --with-all-dependencies');
        Docker::run($app, 'yarn add trix@^2.0.0 swiper@^11.2.6 @sylius-cms-plugin/admin@file:vendor/sylius/cms-plugin/assets/admin');

        Symfony::addJsController(
            $app,
            '@sylius-cms-plugin/admin',
            'preview',
            [
                'enabled' => true,
                'fetch' => 'lazy',
            ],
        );

        Assets::install($app);
        Assets::build($app);
        Database::migrate($app);
        Symfony::cacheClear($app);
    }
}
