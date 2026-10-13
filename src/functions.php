<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins;

use Castor\Attribute\AsListener;
use Castor\Event\AfterBootEvent;
use Sylius\Starter\Core\App;
use Sylius\Starter\Core\Component\CallableInstaller;
use Sylius\Starter\Core\Component\CallableRemover;
use Sylius\Starter\Core\Component\ComponentResolver;
use Sylius\Starter\Core\Service\SyliusService;
use Sylius\Starter\Core\Task\TaskProviderRegistry;
use Sylius\Starter\Plugins\Attribute\AsPluginInstaller;
use Sylius\Starter\Plugins\Attribute\AsPluginRemover;
use Sylius\Starter\Plugins\Installer\AiDevToolsInstaller;
use Sylius\Starter\Plugins\Installer\AltchaInstaller;
use Sylius\Starter\Plugins\Installer\BugSnagInstaller;
use Sylius\Starter\Plugins\Installer\CmsInstaller;
use Sylius\Starter\Plugins\Installer\GdprInstaller;
use Sylius\Starter\Plugins\Installer\InvoicingInstaller;
use Sylius\Starter\Plugins\Installer\MediaInstaller;
use Sylius\Starter\Plugins\Installer\PluginInstallerDescriptor;
use Sylius\Starter\Plugins\Installer\ProductBundleInstaller;
use Sylius\Starter\Plugins\Installer\RecaptchaInstaller;
use Sylius\Starter\Plugins\Installer\RefundInstaller;
use Sylius\Starter\Plugins\Installer\WishlistInstaller;
use Sylius\Starter\Plugins\Remover\AiDevToolsRemover;
use Sylius\Starter\Plugins\Remover\AltchaRemover;
use Sylius\Starter\Plugins\Remover\ApiRemover;
use Sylius\Starter\Plugins\Remover\BugSnagRemover;
use Sylius\Starter\Plugins\Remover\CmsRemover;
use Sylius\Starter\Plugins\Remover\GdprRemover;
use Sylius\Starter\Plugins\Remover\InvoicingRemover;
use Sylius\Starter\Plugins\Remover\PluginRemoverDescriptor;
use Sylius\Starter\Plugins\Remover\RecaptchaRemover;
use Sylius\Starter\Plugins\Remover\WishlistRemover;
use Sylius\Starter\Plugins\Tasks\PluginTasks;

TaskProviderRegistry::register(
    'plugins',
    static fn(SyliusService $service): iterable => (new PluginTasks($service->getName(), $service->getDirectory()))(),
);

#[AsListener(AfterBootEvent::class)]
function initialize(AfterBootEvent $afterBootEvent): void
{
    PluginTasks::addInstaller(new AiDevToolsInstaller());
    PluginTasks::addInstaller(new AltchaInstaller());
    PluginTasks::addInstaller(new BugSnagInstaller());
    PluginTasks::addInstaller(new CmsInstaller());
    PluginTasks::addInstaller(new GdprInstaller());
    PluginTasks::addInstaller(new InvoicingInstaller());
    PluginTasks::addInstaller(new MediaInstaller());
    PluginTasks::addInstaller(new ProductBundleInstaller());
    PluginTasks::addInstaller(new RecaptchaInstaller());
    PluginTasks::addInstaller(new RefundInstaller());
    PluginTasks::addInstaller(new WishlistInstaller());

    PluginTasks::addRemover(new AiDevToolsRemover());
    PluginTasks::addRemover(new AltchaRemover());
    PluginTasks::addRemover(new ApiRemover());
    PluginTasks::addRemover(new BugSnagRemover());
    PluginTasks::addRemover(new CmsRemover());
    PluginTasks::addRemover(new GdprRemover());
    PluginTasks::addRemover(new InvoicingRemover());
    PluginTasks::addRemover(new RecaptchaRemover());
    PluginTasks::addRemover(new WishlistRemover());

    foreach (ComponentResolver::candidates() as $reflection) {
        $descriptor = resolve_plugin_installer($reflection);

        if (null !== $descriptor) {
            PluginTasks::addInstaller($descriptor->installer instanceof \ReflectionFunction
                ? new CallableInstaller($descriptor->attribute->name, $descriptor->installer->getClosure(), $descriptor->attribute->description)
                : $descriptor->installer);
        }

        $descriptor = resolve_plugin_remover($reflection);

        if (null !== $descriptor) {
            PluginTasks::addRemover($descriptor->remover instanceof \ReflectionFunction
                ? new CallableRemover($descriptor->attribute->name, $descriptor->remover->getClosure(), $descriptor->attribute->description)
                : $descriptor->remover);
        }
    }
}

function resolve_plugin_installer(\ReflectionFunction|\ReflectionClass $reflection): ?PluginInstallerDescriptor
{
    $resolved = ComponentResolver::resolve($reflection, AsPluginInstaller::class);

    if (null === $resolved) {
        return null;
    }

    [$attribute, $installer] = $resolved;

    if ($installer instanceof \ReflectionFunction) {
        return new PluginInstallerDescriptor($attribute, $installer);
    }

    return new PluginInstallerDescriptor($attribute, new CallableInstaller($attribute->name, static fn(App $app) => $installer($app), $attribute->description));
}

function resolve_plugin_remover(\ReflectionFunction|\ReflectionClass $reflection): ?PluginRemoverDescriptor
{
    $resolved = ComponentResolver::resolve($reflection, AsPluginRemover::class);

    if (null === $resolved) {
        return null;
    }

    [$attribute, $remover] = $resolved;

    if ($remover instanceof \ReflectionFunction) {
        return new PluginRemoverDescriptor($attribute, $remover);
    }

    return new PluginRemoverDescriptor($attribute, new CallableRemover($attribute->name, static fn(App $app) => $remover($app), $attribute->description));
}
