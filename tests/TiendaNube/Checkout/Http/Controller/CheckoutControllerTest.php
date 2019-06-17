<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Http\Controller;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Response\ResponseBuilderInterface;
use TiendaNube\Checkout\Service\Shipping\AddressService;
use TiendaNube\Checkout\Service\Shipping\AddressServiceLegacy;
use TiendaNube\Checkout\Service\Shipping\AddressServiceProvider;
use TiendaNube\Checkout\Service\Shipping\AddressServiceInterface;
use TiendaNube\Checkout\Service\Store\StoreService;
use Psr\Log\LoggerInterface;

use TiendaNube\Checkout\Model\Address;
use TiendaNube\Checkout\Model\Store;

class CheckoutControllerTest extends TestCase
{

    public function testUseAddressServiceForBetaTesters()
    {
        // expected store
        $store = new Store();
        $store->enableBetaTesting();

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        $addressServiceProvider = new AddressServiceProvider($pdo, $logger);
        
        // asserts
        $this->assertInstanceOf(AddressServiceInterface::class, $addressServiceProvider->getService($store));
        $this->assertInstanceOf(AddressService::class, $addressServiceProvider->getService($store));
    }

    public function testUseAddressServiceLegacyForNonBetaTesters()
    {
        // expected store
        $store = new Store();
        $store->disableBetaTesting();

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        $addressServiceProvider = new AddressServiceProvider($pdo, $logger);
        
        // asserts
        $this->assertInstanceOf(AddressServiceInterface::class, $addressServiceProvider->getService($store));
        $this->assertInstanceOf(AddressServiceLegacy::class, $addressServiceProvider->getService($store));
    }

    public function testGetAddressValid()
    {
        // getting controller instance
        $controller = $this->getControllerInstance();

        // expected address
        $address = Address::fromArray(
            [
                'address' => 'Avenida da França',
                'neighborhood' => 'Comércio',
                'city' => 'Salvador',
                'state' => 'BA',
            ]
        );

        // expected store
        $store = new Store();
        $store->disableBetaTesting();

        // mocking the store service
        $storeService = $this->createMock(StoreService::class);
        $storeService->method('getCurrentStore')->willReturn($store);

        // mocking the address service
        $addressService = $this->createMock(AddressService::class);
        $addressService->method('getAddressByZip')->willReturn($address);

        // mocking the address service provider
        $addressServiceProvider = $this->createMock(AddressServiceProvider::class);
        $addressServiceProvider->method('getService')->willReturn($addressService);

        // test
        $result = $controller->getAddressAction('40010000', $storeService, $addressServiceProvider);

        // asserts
        $this->assertEquals(json_encode($address->toArray()),$result->getBody()->getContents());
        $this->assertEquals(200,$result->getStatusCode());
    }

    public function testGetAddressInvalid()
    {
        // getting controller instance
        $controller = $this->getControllerInstance();

        // expected store
        $store = new Store();
        $store->disableBetaTesting();

        // mocking the store service
        $storeService = $this->createMock(StoreService::class);
        $storeService->method('getCurrentStore')->willReturn($store);

        // mocking the address service
        $addressService = $this->createMock(AddressService::class);
        $addressService->method('getAddressByZip')->willReturn(null);

        // mocking the address service provider
        $addressServiceProvider = $this->createMock(AddressServiceProvider::class);
        $addressServiceProvider->method('getService')->willReturn($addressService);

        // test
        $result = $controller->getAddressAction('400100001', $storeService, $addressServiceProvider);

        // asserts
        $this->assertEquals(404,$result->getStatusCode());
        $this->assertEquals('{"error":"The requested zipcode was not found."}',$result->getBody()->getContents());
    }

    public function testGetAddressValidAddressServiceProvider()
    {
        // getting controller instance
        $controller = $this->getControllerInstance();

        // expected address
        $address = Address::fromArray(
            [
                'address' => 'Avenida da França',
                'neighborhood' => 'Comércio',
                'city' => 'Salvador',
                'state' => 'BA',
            ]
        );

        // expected store
        $store = new Store();
        $store->enableBetaTesting();

        // mocking the store service
        $storeService = $this->createMock(StoreService::class);
        $storeService->method('getCurrentStore')->willReturn($store);

        // mocking the address service
        $addressService = $this->createMock(AddressService::class);
        $addressService->method('getAddressByZip')->willReturn($address);

        // mocking the address service provider
        $addressServiceProvider = $this->createMock(AddressServiceProvider::class);
        $addressServiceProvider->method('getService')->willReturn($addressService);

        // test
        $result = $controller->getAddressAction('40010000', $storeService, $addressServiceProvider);

        // asserts
        $this->assertEquals(json_encode($address->toArray()),$result->getBody()->getContents());
        $this->assertEquals(200,$result->getStatusCode());
    }

    /**
     * Get a RequestStack mock object
     *
     * @param ServerRequestInterface|null $expectedRequest
     * @return MockObject
     */
    private function getRequestStackInstance(?ServerRequestInterface $expectedRequest = null)
    {
        $requestStack = $this->createMock(RequestStackInterface::class);
        $expectedRequest = $expectedRequest ?: $this->createMock(ServerRequestInterface::class);
        $requestStack->method('getCurrentRequest')->willReturn($expectedRequest);

        return $requestStack;
    }

    /**
     * Get a ResponseBuilder mock object
     *
     * @param ResponseInterface|callable|null $expectedResponse
     * @return MockObject
     */
    private function getResponseBuilderInstance($expectedResponse = null)
    {
        $responseBuilder = $this->createMock(ResponseBuilderInterface::class);

        if (is_null($expectedResponse)) {
            $expectedResponse = function ($body, $status, $headers) {
                $stream = $this->createMock(StreamInterface::class);
                $stream->method('getContents')->willReturn($body);

                $response = $this->createMock(ResponseInterface::class);
                $response->method('getBody')->willReturn($stream);
                $response->method('getStatusCode')->willReturn($status);
                $response->method('getHeaders')->willReturn($headers);

                return $response;
            };
        }

        if ($expectedResponse instanceof ResponseInterface) {
            $responseBuilder->method('buildResponse')->willReturn($expectedResponse);
        } else if (is_callable($expectedResponse)) {
            $responseBuilder->method('buildResponse')->willReturnCallback($expectedResponse);
        } else {
            throw new Exception(
                'The expectedResponse argument should be an instance (or mock) of ResponseInterface or callable.'
            );
        }

        return $responseBuilder;
    }

    /**
     * Get an instance of the controller
     *
     * @param null|RequestStackInterface $requestStack
     * @param null|ResponseBuilderInterface $responseBuilder
     * @return CheckoutController
     */
    private function getControllerInstance(
        ?RequestStackInterface $requestStack = null,
        ?ResponseBuilderInterface $responseBuilder = null
    ) {
        // mocking units
        $container = $this->createMock(ContainerInterface::class);
        $requestStack = $requestStack ?: $this->getRequestStackInstance();
        $responseBuilder = $responseBuilder ?: $this->getResponseBuilderInstance();

        return new CheckoutController($container,$requestStack,$responseBuilder);
    }
}
