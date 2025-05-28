<?php

namespace Gurtok\SeoBundle\Tests\Fixtures;

use Gurtok\SeoBundle\Service\SeoDefaultsProvider;

class SeoServicesTestHelper
{
    public function __construct(readonly private SeoDefaultsProvider $seoDefaultsProvider)
    {
    }

    public function getSeoDefaultsProvider(): SeoDefaultsProvider
    {
        return $this->seoDefaultsProvider;
    }
}
