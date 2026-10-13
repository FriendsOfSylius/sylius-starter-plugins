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

final readonly class CmsRemover implements RemoverInterface
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
        io()->title('Removing CMS plugin');

        Composer::allowContribRecipes($app);
        Database::rollbackPluginMigrations($app, 'Sylius\CmsPlugin\Migrations');
        Docker::run($app, 'composer remove sylius/cms-plugin');
        Assets::build($app);
        Symfony::cacheClear($app);
    }
}
