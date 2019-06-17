<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use Psr\Log\LoggerInterface;

/**
 * Class AddressService
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
class AddressService
{
    /**
     * The database connection link
     *
     * @var \PDO
     */
    private $connection;

    private $logger;

    /**
     * AddressService constructor.
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
    public function getAddressByZip(string $zip): ?array
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
                return $stmt->fetch(\PDO::FETCH_ASSOC);
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
