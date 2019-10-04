<?php
require_once 'CandidateAbstract.php';
require_once 'Toolkit.php';
require_once 'Coords.php';
require_once 'Distributions.php';

class Candidate extends CandidateAbstract 
{
    public function run() {
        $status = 0;
        $data = [];
        $errors = [];
        $distributions = new Distributions();
        
        // Извлечение данных из запроса
        $clientFio = htmlspecialchars($_POST['clientFio']);
        $clientTel = htmlspecialchars($_POST['clientTel']);
        $clientAddress = htmlspecialchars($_POST['clientAddress']);

        // Форматирование номера телефона
        $clientTel = Toolkit::getFormattedPhone($clientTel);
        
        // Если номер не соответствует формату, то сообщаем об ошибке 
        if ($clientTel === null) {
            $errors[] = [
                'type' => "errorTel", 
                'text' => "Номер телефона должен состоять из 11 цифр.",
            ];
        }
        // Определение координат по адресу
        //$targetCoords = Toolkit::getCoords($clientAddress);
        $targetCoords = new Coords(59.971942,30.324294);
        
        // Если адрес не найден, то сообщаем об ошибке 
        if ($targetCoords === null) {
            $errors[] = [
                'type' => "errorAddress", 
                'text' => "Адрес введен не верно.",
            ];
        }

        // Если ошибок не выявлено, то отправляем данные, иначе отправляем ошибки 
        if (!$errors) { 
            $distributs = $distributions->getAll();
            $nearestDistribution = $this->determineNearestDistribution($distributs, $targetCoords);

            $data = [
                'clientFio' => $clientFio,
                'clientTel' => $clientTel,
                'nearestDistribution' => $nearestDistribution,
                'targetCoords' => $targetCoords,
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
    * до пункта от искомой координаты.
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
        // Радиус Земли
        $R = 6371;
        
        // Перевод в радианы
        $radCoords1Lat = $Coords1->lat * pi() / 180;
        $radCoords2Lat = $Coords2->lat * pi() / 180;
        $radCoords1Lng = $Coords1->lng * pi() / 180;
        $radCoords2Lng = $Coords2->lng * pi() / 180;
        $abcDiffLat = abs($radCoords1Lat-$radCoords2Lat);
        $abcDiffLng = abs($radCoords1Lng-$radCoords2Lng);

        // Сферическая теорема косинусов 
        $distance = $R * acos(sin($radCoords1Lat) * sin($radCoords2Lat) + cos($radCoords1Lat) * cos($radCoords2Lat) * cos($abcDiffLng));
        
        // Формула для определения более точных значений на маленьких расстояниях (до 1 км)
        //$a = pow(sin($abcDiffLat / 2), 2);
        //$b = cos($radCoords1Lat) * cos($radCoords2Lat) * pow(sin($abcDiffLng / 2), 2);
        //$distance = $R * 2 * asin(sqrt($a + $b));

        // Формула для определения более точных значений на больших расстояниях 
        //$a = pow(cos($radCoords2Lat) * sin($abcDiffLng), 2);
        //$b = pow(cos($radCoords1Lat) * sin($radCoords2Lat) - sin($radCoords1Lat) * cos($radCoords2Lat) * cos($abcDiffLng), 2);
        //$c = sin($radCoords1Lat) * sin($radCoords2Lat);
        //$d = cos($radCoords1Lat) * cos($radCoords2Lat) * cos($abcDiffLng);
        //$distance = $R * atan(sqrt($a + $b) / ($c + $d));
        
        $distance = round($distance, 1);
        
        return $distance;
    }
}
    