<?php
class InspectionDetail
{
    private $id;
    private $inspection_id;
    private $pallet_number;
    private $sample;
    private $variety_id;

    public function __GET($k) { return $this->$k; }
    public function __SET($k, $v) { return $this->$k = $v; }
}
