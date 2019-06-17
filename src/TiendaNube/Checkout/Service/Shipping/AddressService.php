<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Model\Address;
use GuzzleHttp\ClientInterface;

/**
 * Class AddressService
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
class AddressService implements AddressServiceInterface
{
    /** @var LoggerInteface */
    private $logger;

    /** @var ClientInterface */
    private $client;

    /** Let's assume the token is actually an insecure secret key */
    private const TOKEN = 'YouShallNotPass';

    /**
     * AddressService constructor.
     *
     * @param \PDO $pdo
     * @param LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getAddressByZip(string $zip): ?Address
    {
        $headers = [
            'Authorization' => 'Bearer ' . self::TOKEN,
            'Accept'        => 'application/json',
        ];

        try {
            $response = $this->client->request('GET', 'address/'.$zip, [
                'headers' => $headers
            ]);

            if ($response->getStatusCode() == 200) {
                return Address::fromObject(json_decode($response->getBody()->getContents()));
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve result from client: ' . $e->getMessage());

            return null;
        }

        return null;
    }
}
