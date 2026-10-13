<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Installer;

use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\InstallerInterface;
use Sylius\Starter\Core\PhpFile;
use Sylius\Starter\Core\Util\Assets;
use Sylius\Starter\Core\Util\Composer;
use Sylius\Starter\Core\Util\Database;
use Sylius\Starter\Core\Util\Docker;
use Sylius\Starter\Core\Util\Symfony;

use function Castor\io;

final readonly class ProductBundleInstaller implements InstallerInterface
{
    public function name(): string
    {
        return 'product_bundle';
    }

    public function description(): string
    {
        return 'Product bundle for Sylius';
    }

    public function __invoke(App $app): void
    {
        io()->title('Adding Product Bundle plugin');

        Composer::allowContribRecipes($app);
        Docker::run($app, 'composer require sylius/product-bundle-plugin');

        (new PhpFile($app->directory() . '/src/Entity/Order/OrderItem.php'))
            ->addInterface(\Sylius\ProductBundlePlugin\Entity\OrderItemInterface::class) // @phpstan-ignore class.notFound
            ->addTrait(\Sylius\ProductBundlePlugin\Entity\ProductBundleOrderItemsAwareTrait::class) // @phpstan-ignore class.notFound
            ->addConstructor(<<<'PHP'

                    public function __construct()
                    {
                        parent::__construct();

                        $this->initializeProductBundleOrderItems();
                    }

                PHP)
            ->save()
        ;

        (new PhpFile($app->directory() . '/src/Entity/Product/Product.php'))
            ->addInterface(\Sylius\ProductBundlePlugin\Entity\ProductInterface::class) // @phpstan-ignore class.notFound
            ->addTrait(\Sylius\ProductBundlePlugin\Entity\ProductBundlesAwareTrait::class) // @phpstan-ignore class.notFound
            ->save()
        ;

        Docker::run($app, 'yarn install');
        Assets::build($app);
        Symfony::cacheClear($app);
    }
}
