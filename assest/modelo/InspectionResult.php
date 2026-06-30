<?php
class InspectionResult
{
    private $id;
    private $inspection_id;
    private $major_defects_sum;
    private $minor_defects_sum;
    private $score;
    private $category_id;

    public function __GET($k) { return $this->$k; }
    public function __SET($k, $v) { return $this->$k = $v; }
}
