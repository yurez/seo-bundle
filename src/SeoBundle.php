<?php

namespace Gurtok\SeoBundle;

use Gurtok\SeoBundle\DependencyInjection\GurtokSeoExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SeoBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new GurtokSeoExtension();
    }
}
