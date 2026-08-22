<?php

/**
 * SGMC Scanner Agent — entry point.
 *
 * Usage:
 *   php bin/agent.php start
 *   php bin/agent.php health
 *   php bin/agent.php scanners
 */

require __DIR__.'/../vendor/autoload.php';

use ScannerAgent\AgentServer;
use ScannerAgent\AgentService;
use ScannerAgent\Providers\NetworkProvider;
use ScannerAgent\Providers\TwainProvider;
use ScannerAgent\Providers\WiaProvider;
use ScannerAgent\Security\TokenManager;
use ScannerAgent\Transport\SgmcClient;

$config = json_decode(
    (string) file_get_contents(__DIR__.'/../config/agent.json'),
    true,
);

$command = $argv[1] ?? 'start';

$tokenManager = new TokenManager($config['job_timeout_minutes'] ?? 15);
$client = new SgmcClient($config['sgmc_url'] ?? 'http://localhost:8000');
$service = new AgentService(
    $tokenManager,
    $client,
    __DIR__.'/../'.$config['temp_dir'] ?? __DIR__.'/../storage/temp',
);

// Register providers (contract stubs — require Windows validation)
$service->addProvider(new WiaProvider());
$service->addProvider(new TwainProvider());
$service->addProvider(new NetworkProvider());

$server = new AgentServer(
    $service,
    $config['host'] ?? '127.0.0.1',
    (int) ($config['port'] ?? 8765),
);

match ($command) {
    'start' => $server->start(),
    'health' => print(json_encode(['status' => 'ok', 'providers' => array_map(fn ($p) => $p->name(), $service->providers())], JSON_PRETTY_PRINT)."\n"),
    'scanners' => print(json_encode($service->discoverScanners(), JSON_PRETTY_PRINT)."\n"),
    default => print("Usage: php bin/agent.php [start|health|scanners]\n"),
};
