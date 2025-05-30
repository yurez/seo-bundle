<?php

namespace Gurtok\SeoBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ImageUrlResolver implements ImageUrlResolverInterface
{
    public function __construct(
        protected readonly RequestStack $requestStack,
    ) {
    }

    public function resolve(string $path): ?string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $request = $this->requestStack->getMainRequest();
        if (!$request) {
            return null;
        }

        return $request->getSchemeAndHttpHost().'/'.ltrim($path, '/');
    }
}
