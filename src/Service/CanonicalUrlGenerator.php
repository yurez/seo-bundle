<?php

namespace Gurtok\SeoBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CanonicalUrlGenerator
{
    /**
     * @param array<string> $excludedQueryKeys
     */
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected array $excludedQueryKeys = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
            'ref',
            'fbclid',
            'page',
        ],
    ) {
    }

    public function generateFromRequest(Request $request): string
    {
        $route = $request->attributes->get('_route');
        $params = $request->attributes->get('_route_params') ?? [];

        if ($route && \is_string($route)) {
            return $this->urlGenerator->generate(
                $route,
                (array) $params,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        $uri = $request->getSchemeAndHttpHost().$request->getPathInfo();

        $query = array_diff_key(
            $request->query->all(),
            array_flip($this->excludedQueryKeys)
        );

        if (!empty($query)) {
            $uri .= '?'.http_build_query($query);
        }

        return $uri;
    }
}
