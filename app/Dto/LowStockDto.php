<?php

namespace App\Dto;

class LowStockDto extends BaseDto
{
    private $item_name, $total_item_in_stock;

    /**
     * @param $item_name
     * @param $total_item_in_stock
     * @param string $type
     */
    public function __construct($item_name, $total_item_in_stock,  string $type)
    {
        parent::setType($type);
        $this->item_name = $item_name;
        $this->total_item_in_stock = $total_item_in_stock;
    }

    /**
     * @return mixed
     */
    public function getItemName()
    {
        return $this->item_name;
    }

    /**
     * @param mixed $item_name
     */
    public function setItemName($item_name): void
    {
        $this->item_name = $item_name;
    }

    /**
     * @return mixed
     */
    public function getTotalItemInStock()
    {
        return $this->total_item_in_stock;
    }

    /**
     * @param mixed $total_item_in_stock
     */
    public function setTotalItemInStock($total_item_in_stock): void
    {
        $this->total_item_in_stock = $total_item_in_stock;
    }
}
