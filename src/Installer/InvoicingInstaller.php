<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Installer;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\InstallerInterface;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Database;
use Sylius\Starter\Core\Util\Docker;
use Sylius\Starter\Core\Util\Symfony;

use function Castor\fs;
use function Castor\io;

final readonly class InvoicingInstaller implements InstallerInterface
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
        io()->title('Adding Invoicing Plugin');

        Composer::allowContribRecipes($app);
        Docker::run($app, 'composer require sylius/invoicing-plugin');
        Database::migrate($app);
        Symfony::cacheClear($app);
    }
}
