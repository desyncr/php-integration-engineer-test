<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use TiendaNube\Checkout\Service\Shipping\AddressService;
use TiendaNube\Checkout\Service\Shipping\AddressServiceLegacy;
use TiendaNube\Checkout\Service\Shipping\AddressServiceInterface;

use TiendaNube\Checkout\Model\Store;
use Psr\Log\LoggerInterface;

/**
 * Class AddressServiceProvider
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
class AddressServiceProvider
{
    private $pdo;
    private $logger;

    /**
     * Pulling out dependencies for both AddressService and AddressServiceLegacy.
     */
    public function __construct(\PDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    /**
     * Pulling out dependencies for both AddressService and AddressServiceLegacy.
     */
    public function getService(Store $store): AddressServiceInterface
    {
        return $store->isBetaTester() ?
                new AddressService($this->logger) : new AddressServiceLegacy($this->pdo, $this->logger);
    }
}
