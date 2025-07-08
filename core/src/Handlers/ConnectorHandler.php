<?php

declare(strict_types=1);

namespace MXRVX\Autoloader\Handlers;

use MXRVX\Autoloader\App;
use MXRVX\Autoloader\PackageManager;

/**
 * @psalm-import-type ArrayNamespaceStructure from PackageManager
 */
class ConnectorHandler
{
    public function __construct(protected App $app) {}

    /**
     * @psalm-type namespace = string
     * @psalm-type connector = string
     * @psalm-type extra = string
     *
     * @return array{
     *  namespace,
     *  connector,
     *  extra,
     * }|null
     */
    public function getConnectorRequest(): ?array
    {
        if (empty($_SERVER['REQUEST_URI'])) {
            return null;
        }

        $matches = [];
        $pattern = '#^/assets/components/([^/]+)/api/([^/.]+)/(.*)?#';

        if (\preg_match($pattern, $_SERVER['REQUEST_URI'], $matches)) {
            return [
                $matches[1],
                $matches[2],
                $matches[3] ?? '',
            ];
        }

        return null;
    }

    public function __invoke(): void
    {
        $request = $this->getConnectorRequest();

        if ($request === null) {
            return;
        }

        [$namespace, $connector, ] = $request;
        [$modx, $container] = $this->app->getReferences();

        /** @var array<ArrayNamespaceStructure> $namespaces */
        $namespaces = $modx->call(\modNamespace::class, 'loadCache', [$modx]);

        if (!isset($namespaces[$namespace])) {
            return;
        }

        $connectorPath = \sprintf(
            '%s/assets/components/%s/api/%s.php',
            $_SERVER['DOCUMENT_ROOT'] ?? '',
            $namespace,
            $connector,
        );

        if (!\file_exists($connectorPath)) {
            return;
        }

        try {
            require $connectorPath;
            exit;
        } catch (\Throwable $e) {
            if ($this->app->config->getSetting('show_errors')?->getBoolValue()) {
                $this->app->log(
                    \sprintf(
                        'include `%s` failed with an error: `%s` line: `%s`',
                        $e->getFile(),
                        $e->getMessage(),
                        $e->getLine(),
                    ),
                );
            }
        }
    }
}
