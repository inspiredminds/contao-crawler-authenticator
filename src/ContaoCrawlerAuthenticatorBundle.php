<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Crawler Authenticator extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoCrawlerAuthenticator;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoCrawlerAuthenticatorBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
