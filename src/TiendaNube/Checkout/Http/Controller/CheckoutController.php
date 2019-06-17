<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Http\Controller;

use Psr\Http\Message\ResponseInterface;
use TiendaNube\Checkout\Service\Shipping\AddressServiceProvider;
use TiendaNube\Checkout\Service\Store\StoreService;
use TiendaNube\Checkout\Model\Address;

class CheckoutController extends AbstractController
{
    /**
     * Returns the address to be auto-fill the checkout form
     *
     * Expected JSON:
     * {
     *     "address": "Avenida da França",
     *     "neighborhood": "Comércio",
     *     "city": "Salvador",
     *     "state": "BA"
     * }
     *
     * @Route /address/{zipcode}
     *
     * @param string $zipcode
     * @param AddressServiceProvider $addressServiceProvider
     * @return ResponseInterface
     */
    public function getAddressAction(string $zipcode, StoreService $storeService, AddressServiceProvider $addressServiceProvider):ResponseInterface
    {
        
        // We always have a 'store' available so we retrieve our AddressService for a Store.
        $address = $addressServiceProvider
            ->getService(
                $storeService->getCurrentStore()
            )
            ->getAddressByZip($zipcode);

        // Should be an instance of Address or null.
        if ($address instanceof Address) {
            return $this->json($address->toArray());
        }

        // returning the error when not found
        return $this->json(['error'=>'The requested zipcode was not found.'], 404);
    }
}
