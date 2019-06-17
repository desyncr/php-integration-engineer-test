<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping; 

use TiendaNube\Checkout\Model\Address;

/**
 * Interface AddressServiceProvider
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
interface AddressServiceInterface
{
    /**
     * Get an address by its zipcode (CEP)
     *
     * The expected return format is an array like:
     * [
     *      "address" => "Avenida da França",
     *      "neighborhood" => "Comércio",
     *      "city" => "Salvador",
     *      "state" => "BA"
     * ]
     * or false when not found.
     *
     * @param string $zip
     * @return bool|array
     * @throws \InvalidArgumentException
     */
	public function getAddressByZip(string $zip) : ?Address;
}