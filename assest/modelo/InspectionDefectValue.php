<?php
class InspectionDefectValue
{
    private $id;
    private $inspection_detail_id;
    private $defect_id;
    private $value;

    public function __GET($k) { return $this->$k; }
    public function __SET($k, $v) { return $this->$k = $v; }
}
