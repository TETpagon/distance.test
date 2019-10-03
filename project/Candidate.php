<?php
require_once 'CandidateAbstract.php';
require_once 'Toolkit.php';
require_once 'Coords.php';

class Candidate extends CandidateAbstract 
{
    public function run() {
        $status = 0;
        $data = [];
        $errors = [];
        
        $clientFio = htmlspecialchars($_POST['clientFio']);
        $clientTel = htmlspecialchars($_POST['clientTel']);
        $clientAddress = htmlspecialchars($_POST['clientAddress']);

        $clientTel = Toolkit::getFormattedPhone($clientTel);
        if ($clientTel === null) {
            $errors[] = [
                'type' => "errorTel", 
                'text' => "Номер телефона должен состоять из 11 цифр.",
            ];
        }
        $targetCoords = Toolkit::getCoords($clientAddress);

        if ($targetCoords === null) {
            $errors[] = [
                'type' => "errorAddress", 
                'text' => "Адрес введен не верно.",
            ];
        }

        if (!$errors) { 
            $distributions = $this->getDistributions();
            $nearestDistribution = $this->determineNearestDistribution($distributions, $targetCoords);

            $data = [
                'clientFio' => $clientFio,
                'clientTel' => $clientTel,
                'nearestDistribution' => $nearestDistribution,
            ];

            $response = [
                'status' => 0,
                'data' => $data,
            ];
        } else {
            $response = [
                'status' => 1,
                'data' => $errors,
            ];
        }
        
        echo json_encode($response);
    }
    
    /**
    * Вернет ближайший пункт
    * выдачи посылок и растояние 
    * до него от искомой координаты.
    *
    * @param array $distributions Массив пунктов выдачи посылок.
    * @param Coords $targetCoords Искомая координата.
    * @return array
    */
    protected function determineNearestDistribution(array $distributions, Coords $targetCoords): array {
        $minDistance = 9000000;
        $minIndex = 0;
        foreach($distributions as $key => $distribution) {
            $distributionCoords = new Coords($distribution['latitude'], $distribution['longitude']);
            $distance = $this->calculateDistance($distributionCoords, $targetCoords);
            if($minDistance > $distance) {
                $minDistance = $distance;
                $minIndex = $key;
            }
        }
        
        return [
            'name' => $distributions[$minIndex]['name'],
            'distance' =>$minDistance,
        ];
    }
    
    
    /**
    * Вернет расстояние между  
    * координатами в километрах
    * с точностью до одного 
    * знака после запятой.
    *
    * @param Coords $Coords1
    * @param Coords $Coords2
    * @return float
    */
    protected function calculateDistance(Coords $Coords1, Coords $Coords2): float {
        $R = 6371;
        $distance = 6371*acos(sin($Coords1->lng*pi()/180)*sin($Coords2->lng*pi()/180) + cos($Coords1->lng*pi()/180) * cos($Coords2->lng*pi()/180) * cos($Coords2->lat*pi()/180 - $Coords1->lat*pi()/180));
        $distance = round($distance, 1);
        return $distance;
    }
    
    /**
    * Вернет из базы данных массив 
    * пунтков выдачи посылок.
    *
    * @return array
    */
    protected function getDistributions(): array {
        $host = '127.0.0.1';
        $db   = 'delivery';
        $user = 'phpmyadmin';
        $pass = 'some_pass';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $opt);

        $distributions = $pdo->query('SELECT name, latitude, longitude FROM distributions')->fetchAll(PDO::FETCH_ASSOC);
        
        
        return $distributions;
    }
}
    