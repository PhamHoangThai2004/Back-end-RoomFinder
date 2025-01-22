<?php

namespace Pht\Roomfinder;

use PDO;
use PDOException;

require_once '../vendor/autoload.php';

class Location {
    private $connect;

    public function __construct($_connect)
    {
        $this->connect = $_connect;
    }

    public function newLocation($location) {
        $sql = "INSERT INTO location (Address, Longitude, Latitude)
                VALUES  (?, ?, ?)";
        $query = $this->connect->prepare($sql);
        try {
            $result = $query->execute([$location['address'], $location['longitude'], $location['latitude']]);

            if ($result) {
                $locationId = $this->connect->lastInsertId();
                return $locationId;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -2;
        }
    }

    public function deleteLocation($locationId) {
        $sql = "DELETE FROM location WHERE LocationID = ?";
        $query = $this->connect->prepare($sql);
        try {
            $result = $query->execute([$locationId]);

            if ($result) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }

}

?>