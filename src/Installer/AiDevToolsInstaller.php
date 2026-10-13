<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Installer;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\InstallerInterface;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Docker;

use function Castor\io;

final readonly class AiDevToolsInstaller implements InstallerInterface
{
    public function name(): string
    {
        return 'ai_dev_tools';
    }

    public function description(): string
    {
        return 'Dev-only AI tooling for Sylius';
    }

    public function __invoke(App $app): void
    {
        io()->title('Adding AI dev tools plugin');

        Composer::allowContribRecipes($app);
        Docker::run($app, 'composer require --dev sylius/sylius-ai-dev-tools');

        Docker::run($app, 'vendor/bin/mate init');
        Docker::run($app, 'vendor/bin/mate discover');
    }
}
