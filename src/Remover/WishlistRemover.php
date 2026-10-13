<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Remover;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\RemoverInterface;
use Sylius\Starter\Core\Util\Assets;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Database;
use Sylius\Starter\Core\Util\Docker;
use Sylius\Starter\Core\Util\Symfony;

use function Castor\io;

final readonly class WishlistRemover implements RemoverInterface
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
        io()->title('Removing Wishlist plugin');

        Composer::allowContribRecipes($app);
        Database::rollbackPluginMigrations($app, 'Sylius\WishlistPlugin\Migrations');
        Docker::run($app, 'composer remove sylius/wishlist-plugin');
        Docker::run($app, 'bin/console assets:install public');
        Docker::run($app, 'yarn install');
        Assets::build($app);
        Symfony::cacheClear($app);
    }
}
