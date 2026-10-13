<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Remover;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\RemoverInterface;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Database;
use Sylius\Starter\Core\Util\Docker;

use function Castor\io;

final readonly class InvoicingRemover implements RemoverInterface
{
    public function name(): string
    {
        return 'invoicing';
    }

    public function description(): string
    {
        return 'Invoicing plugin for Sylius';
    }

    public function __invoke(App $app): void
    {
        io()->title('Removing Invoicing plugin');

        Composer::allowContribRecipes($app);
        Database::rollbackPluginMigrations($app, 'Sylius\InvoicingPlugin\Migrations');
        Docker::run($app, 'composer remove sylius/invoicing-plugin');
    }
}
