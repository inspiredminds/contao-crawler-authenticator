<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Crawler Authenticator extension.
 *
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoCrawlerAuthenticator\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use InspiredMinds\ContaoCrawlerAuthenticator\ContaoCrawlerAuthenticatorBundle;
use InspiredMinds\ContaoCrawlerAuthenticator\Security\CrawlerAuthenticator;

class Plugin implements BundlePluginInterface, ExtensionPluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoCrawlerAuthenticatorBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, 'isotope']),
        ];
    }

    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container): array
    {
        if ('security' === $extensionName) {
            foreach ($extensionConfigs as &$extensionConfig) {
                if (isset($extensionConfig['firewalls']['contao_frontend']['guard'])) {
                    $extensionConfig['firewalls']['contao_frontend']['guard']['entry_point'] = CrawlerAuthenticator::class;
                }

                if (isset($extensionConfig['firewalls']['contao_frontend'])) {
                    $extensionConfig['firewalls']['contao_frontend']['anonymous'] = 'lazy';
                    $extensionConfig['firewalls']['contao_frontend']['guard']['authenticators'][] = CrawlerAuthenticator::class;
                }
            }
        } elseif ('contao' === $extensionName) {
            $hasAuthBasic = false;
            $hasIndexProtected = false;

            foreach ($extensionConfigs as $extensionConfig) {
                if (isset($extensionConfig['crawl']['default_http_client_options']['auth_basic'])) {
                    $hasAuthBasic = true;
                }

                if (isset($extensionConfig['search']['index_protected'])) {
                    $hasIndexProtected = true;
                }

                if ($hasAuthBasic && $hasIndexProtected) {
                    break;
                }
            }

            if (!$hasIndexProtected) {
                $extensionConfigs[] = [
                    'search' => [
                        'index_protected' => true,
                    ],
                ];
            }

            if (!$hasAuthBasic) {
                $extensionConfigs[] = [
                    'crawl' => [
                        'default_http_client_options' => [
                            'auth_basic' => '%env(CRAWLER_AUTH)%',
                        ],
                    ],
                ];

                $container->setParameter('env(CRAWLER_AUTH)', '');
            }
        }

        return $extensionConfigs;
    }
}
