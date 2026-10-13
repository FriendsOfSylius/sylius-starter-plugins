<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Remover;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\RemoverInterface;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Docker;

use function Castor\io;

final readonly class BugSnagRemover implements RemoverInterface
{
    public function name(): string
    {
        return 'bugsnag';
    }

    public function description(): string
    {
        return 'Official BugSnag notifier for Symfony applications';
    }

    public function __invoke(App $app): void
    {
        io()->title('Removing BugSnag plugin');

        Composer::allowContribRecipes($app);
        Docker::run($app, 'composer remove bugsnag/bugsnag-symfony');
    }
}
