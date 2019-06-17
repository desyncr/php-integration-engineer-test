<?php
declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Store;

use TiendaNube\Checkout\Model\Store;

/**
 * @package TiendaNube\Checkout\Service\Store
 */
class StoreService implements StoreServiceInterface
{
    /**
     * Get the current store instance
     *
     * @return Store
     */
    public function getCurrentStore() : Store
    {
    }
}
