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
    /**
     * The database connection link
     *
     * @var \PDO
     */
    private $connection;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Pulling out dependencies for both AddressService and AddressServiceLegacy.
     */
    public function __construct(\PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * Returns either an AddressService or an AddressServiceLegacy instance.
     *
     * @return AddressServiceInterface
     */
    public function getService(Store $store): AddressServiceInterface
    {
        if ($store->isBetaTester()) {            
            $addressService = new AddressService(
                (new AddressServiceClientProvider())->getClient(),
                $this->logger)
            ;

        } else {
            $addressService = new AddressServiceLegacy($this->connection, $this->logger);

        }

        return $addressService;
    }
}
