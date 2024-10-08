<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Crawler Authenticator extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoCrawlerAuthenticator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ContaoCrawlerAuthenticatorBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $containerConfigurator->import('../config/services.yaml');
    }
}
