<?php
declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Store;

use TiendaNube\Checkout\Model\Store;

class StoreService implements StoreServiceInterface
{
    public function getCurrentStore() : Store {
    }
}