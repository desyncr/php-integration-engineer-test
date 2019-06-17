<?php

namespace TiendaNube\Checkout\Service\Shipping;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GuzzleHttp\ClientInteface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

use TiendaNube\Checkout\Model\Address;

class AddressServiceTest extends TestCase
{
    public function testGetExistentAddressByZipcode()
    {
        // expected address
        $address = [
            'address' => 'Avenida da França',
            'neighborhood' => 'Comércio',
            'city' => 'Salvador',
            'state' => 'BA',
        ];

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        $body =  Psr7\stream_for('{
            "altitude":7.0,
            "cep":"40010000",
            "latitude":"-12.967192",
            "longitude":"-38.5101976",
            "address":"Avenida da França",
            "neighborhood":"Comércio",
            "city":{
                "ddd":71,
                "ibge":"2927408",
                "name":"Salvador"
            },
            "state":{
                "acronym":"BA"
            }
        }');

        // Create a mock and queue two responses.
        $mock = new MockHandler([
            new Response(200, [], $body)
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // creating service
        $service = new AddressService($client, $logger);

        // testing
        $result = $service->getAddressByZip('40010000');

        // asserts
        $this->assertNotNull($result);
        $this->assertEquals($address,$result->toArray());
    }

    public function testGetNonExistentAddressByZipcode()
    {
        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // Create a mock and queue two responses.
        $mock = new MockHandler([
            new Response(404)
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // creating service
        $service = new AddressService($client, $logger);

        // testing
        $result = $service->getAddressByZip('40010000');

        // asserts
        $this->assertNull($result);
    }

    public function testGetAddressOnServerError()
    {
        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // Create a mock and queue two responses.
        $mock = new MockHandler([
            new Response(500)
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // creating service
        $service = new AddressService($client, $logger);

        // testing
        $result = $service->getAddressByZip('40010000');

        // asserts
        $this->assertNull($result);
    }
}