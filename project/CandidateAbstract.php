<?php
abstract class CandidateAbstract 
{
    abstract public function run();
    
    abstract protected function calculateDistance(Coords $Coords1, Coords $Coords2): float;
}
    