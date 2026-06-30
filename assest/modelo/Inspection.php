<?php
class Inspection
{
    private $id;
    private $producer_id;
    private $inspector_id;
    private $inspection_date;
    private $product;
    private $is_organic;
    private $total_pallets;
    private $created_by;
    private $updated_by;

    public function __GET($k) { return $this->$k; }
    public function __SET($k, $v) { return $this->$k = $v; }
}
