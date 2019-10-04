<?php
require_once 'Coords.php';

class Toolkit 
{
    /**
     * Вернет координаты адреса
     * Если адрес не найден вернет NULL
     *
     * @param string $address
     * @return Coords|null
     */
    public static function getCoords(string $address): ?Coords
    {
        $address = urlencode($address);
        $json = file_get_contents("https://geocode-maps.yandex.ru/1.x/?format=json&geocode=" . $address);
        $data = json_decode($json);

        if (!isset($data->response->GeoObjectCollection->featureMember[0])) {
            return null;
        }
        
        $pos = explode(" ", $data->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);

        return (new Coords($pos[1], $pos[0]));
    }

    /**
     * Вернет для телефонов вида 80001112233 +7 000 111 22 33 +7(000)111-22-22 и т.п следующий вид +7(000)111-2233
     * Телефон должен иметь 11 значный формат
     * Вернет null если телефон не был преобразован
     *
     * @param string $phone номер после +7 или 8
     * @return string|null
     */
    public static function getFormattedPhone(string $phone): ?string
	{
        $phone = preg_replace('/[^0-9,]/', '', $phone);
        return strlen($phone) == 11 ? preg_replace("/^\+?(\d{1})(\d{3})(\d{3})(\d{4})/", "+7($2)$3-$4", $phone) : null;
    }
}
