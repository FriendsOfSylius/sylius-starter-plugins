<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Remover;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\RemoverInterface;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Docker;
use Sylius\Starter\Core\Util\Symfony;

use function Castor\finder;
use function Castor\fs;
use function Castor\io;

final readonly class AltchaRemover implements RemoverInterface
{
    public function name(): string
    {
        return 'altcha';
    }

    public function description(): string
    {
        return 'Remove ALTCHA from customer registration';
    }

    public function __invoke(App $app): void
    {
        io()->title('Removing ALTCHA plugin');

        Composer::allowContribRecipes($app);

        $resourcesDir = \dirname(__DIR__, 2) . '/resources/altcha';

        foreach (finder()->files()->in($resourcesDir)->files() as $file) {
            fs()->remove($app->directory() . '/' . $file->getRelativePathname());
        }

        // The recipe unconfigure step removes the bundle, the route and the env var.
        Docker::run($app, 'composer remove tito10047/altcha-bundle');
        fs()->remove($app->directory() . '/config/packages/altcha.yaml');

        Symfony::cacheClear($app);
    }
}
