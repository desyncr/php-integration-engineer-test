<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use TiendaNube\Checkout\Service\Shipping\AddressService;
use TiendaNube\Checkout\Model\Store;

/**
 * Interface AddressServiceProvider
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
interface AddressServiceProviderInterface
{
    /**
     * Returns either an AddressService or an AddressServiceLegacy instance.
     *
     * @return AddressServiceInterface
     */
    public function getService(Store $store) : AddressService
}
