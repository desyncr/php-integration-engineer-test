<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use Psr\Log\LoggerInterface;

use TiendaNube\Checkout\Model\Address;

/**
 * Class AddressServiceLegacy
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
class AddressServiceLegacy implements AddressServiceInterface
{
    /**
     * The database connection link
     *
     * @var \PDO
     */
    private $connection;

    private $logger;

    /**
     * AddressServiceLegacy constructor.
     *
     * @param \PDO $pdo
     * @param LoggerInterface $logger
     */
    public function __construct(\PDO $pdo, LoggerInterface $logger)
    {
        $this->connection = $pdo;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getAddressByZip(string $zip): ?Address
    {    
        if (!$zipcode = $this->filterZipcode($zip)) {
            throw new \InvalidArgumentException('Invalid or badly formated zipcode given.');
        }

        $this->logger->debug('Getting address for the zipcode [' . $zipcode . '] from database');

        try {
            // getting the address from database
            $stmt = $this->connection->prepare('SELECT address, neighborhood, city, state FROM `addresses` WHERE `zipcode` = ?');
            $stmt->execute([$zipcode]);

            // checking if the address exists
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                return Address::fromArray($result);
            }

            return null;

        } catch (\PDOException $ex) {
            $this->logger->error(
                'An error occurred at try to fetch the address from the database, exception with message was caught: ' .
                $ex->getMessage()
            );

            return null;
        }
    }

    /**
     * Returns a zipcode as int.
     * @return string
     */
    protected function filterZipcode(string $zip): ?string
    {
        return preg_replace("/[^\d]/","", $zip);
    }
}
