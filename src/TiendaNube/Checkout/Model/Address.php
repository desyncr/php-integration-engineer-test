<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Model;

/**
 * Class Address
 *
 * @package TiendaNube\Checkout\Model
 */
class Address
{
    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $neighborhood;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @return string
     */
    public function getAddress():string {
        return $this->address;
    }

    public function setAddress(string $address) {
        $this->address = $address;
    }

    public function getNeighborhood():string {
        return $this->neighborhood;
    }

    public function setNeighborhood(string $neighborhood) {
        $this->neighborhood = $neighborhood;
    }

    public function getCity():string {
        return $this->city;
    }

    public function setCity(string $city) {
        $this->city = $city;
    }

    public function getState():string {
        return $this->state;
    }

    public function setState(string $state) {
        $this->state = $state;
    }

    /**
     * Helper method to "publish" certain properties from this model.
     */
    public function toArray():array {
        return [
            'address' => $this->address,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
        ];
    }

    /**
     * Helper method to transform an array into a Address instance.
     */
    static public function fromArray(array $properties):Address {
        $address = new Address();
        // We're assuming these properties DO exists and they're safe to use.
        $address->setAddress($properties['address']);
        $address->setNeighborhood($properties['neighborhood']);
        $address->setCity($properties['city']);
        $address->setState($properties['state']);

        return $address;
    }
}
