<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Crawler Authenticator extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoCrawlerAuthenticator\EventListener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\PageModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Adds Vary for 'Authorization' on applicable responses.
 */
#[AsEventListener(priority: -900)]
class AddVaryListener
{
    public function __construct(private readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$this->scopeMatcher->isContaoMainRequest($event)) {
            return;
        }

        $response = $event->getResponse();

        // Do not modify headers, if this response cannot be cached anyway
        if (!$response->isCacheable() || $response->headers->hasCacheControlDirective('private')) {
            return;
        }

        // Do not add Vary if a page is associated with this request and 'alwaysLoadFromCache' is enabled
        if (($page = $this->getPageModel($event->getRequest())) && $page->cache && $page->alwaysLoadFromCache) {
            return;
        }

        $response->setVary('Authorization', false);
    }

    private function getPageModel(Request $request): PageModel|null
    {
        if (($pageModel = $request->attributes->get('pageModel')) instanceof PageModel) {
            return $pageModel;
        }

        if (($GLOBALS['objPage'] ?? null) instanceof PageModel) {
            return $GLOBALS['objPage'];
        }

        return null;
    }
}
