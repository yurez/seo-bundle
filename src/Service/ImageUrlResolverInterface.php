<?php

namespace Gurtok\SeoBundle\Service;

interface ImageUrlResolverInterface
{
    /**
     * Resolves a given image path to a full URL.
     *
     * @param string $path the image path to resolve
     *
     * @return string|null the resolved URL or null if it cannot be resolved
     */
    public function resolve(string $path): ?string;
}
