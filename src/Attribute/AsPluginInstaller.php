<?php

declare(strict_types=1);

namespace Sylius\Starter\Plugins\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_FUNCTION)]
class AsPluginInstaller
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
