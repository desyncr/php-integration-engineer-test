<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * Class AddressServiceClientProvider
 * 
 * This class aims to provide a single point to configure the AddressServices'
 * HTTP client configuration.
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
class AddressServiceClientProvider {

    private const BASE_URL = 'https://shipping.tiendanube.com/v1/';

    /**
     * Returns a configured HTTP client.
     *
     * @return ClientInterface
     */
    public function getClient() : ClientInterface
    {
        return new Client([
            'base_uri' => self::BASE_URL
        ]);
    }
}