<?php
class InspectionMeasurement
{
    private $id;
    private $inspection_detail_id;
    private $weight;
    private $temperature;
    private $brix;
    private $baxlo;
    private $average;

    public function __GET($k) { return $this->$k; }
    public function __SET($k, $v) { return $this->$k = $v; }
}
