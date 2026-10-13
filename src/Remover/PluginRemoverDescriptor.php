<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Remover;

use Sylius\Starter\Core\Component\RemoverInterface;
use Sylius\Starter\Plugins\Attribute\AsPluginRemover;

final readonly class PluginRemoverDescriptor
{
    public function __construct(
        public AsPluginRemover $attribute,
        public \ReflectionFunction|RemoverInterface $remover,
    ) {}
}
