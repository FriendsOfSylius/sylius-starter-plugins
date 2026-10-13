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

use function Castor\fs;
use function Castor\io;

final readonly class WishlistInstaller implements InstallerInterface
{
    public function name(): string
    {
        return 'wishlist';
    }

    public function description(): string
    {
        return 'Wishlist plugin for Sylius';
    }

    public function __invoke(App $app): void
    {
        io()->title('Adding Wishlist Plugin');

        Composer::allowContribRecipes($app);
        Docker::run($app, 'bin/console assets:install public');
        Docker::run($app, 'yarn install');
        Assets::build($app);
        Database::migrate($app);
        Symfony::cacheClear($app);
    }
}
