<?php
class Coords
{
    public $lat;
    public $lng;

    public function __construct($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }
}