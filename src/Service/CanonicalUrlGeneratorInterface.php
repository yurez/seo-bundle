<?php

namespace Gurtok\SeoBundle\Service;

use Symfony\Component\HttpFoundation\Request;

interface CanonicalUrlGeneratorInterface
{
    public function generateFromRequest(Request $request): string;
}
