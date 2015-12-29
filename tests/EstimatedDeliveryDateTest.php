<?php

namespace Tests\EstimatedDeliveryDate;

use EstimatedDeliveryDate;

class EstimatedDeliveryDateTest extends \PHPUnit_Framework_TestCase
{
    public function testPreparation()
    {
        $currentDate = "2015-12-28";
        $estimatedDeliveryDate = new EstimatedDeliveryDate($currentDate);
        $estimatedDeliveryDate->preparation(2);
        $estimatedDeliveryDate->vacations(['2015-12-29']);

        $deliveryDate = [
            'min' => "2015-12-31",
            'max' => "2015-12-31"
        ];

        $this->assertEquals($deliveryDate, $estimatedDeliveryDate->estimation());
    }

    public function testDelivery()
    {
        $currentDate = "2015-12-28";
        $estimatedDeliveryDate = new EstimatedDeliveryDate($currentDate);
        $estimatedDeliveryDate->delivery(0, 1);

        $deliveryDate = [
            'min' => "2015-12-28",
            'max' => "2015-12-29"
        ];

        $this->assertEquals($deliveryDate, $estimatedDeliveryDate->estimation());
    }

    public function testDeliveryAndPreparation()
    {
        $currentDate = "2015-12-28";
        $estimatedDeliveryDate = new EstimatedDeliveryDate($currentDate);
        $estimatedDeliveryDate->delivery(0, 1);
        $estimatedDeliveryDate->preparation(2);

        $deliveryDate = [
            'min' => "2015-12-30",
            'max' => "2015-12-31"
        ];

        $this->assertEquals($deliveryDate, $estimatedDeliveryDate->estimation());
    }

    public function testDeliveryAndPreparationWithDaysOff()
    {
        $currentDate = "2016-01-04";
        $estimatedDeliveryDate = new EstimatedDeliveryDate($currentDate);
        $estimatedDeliveryDate->delivery(0, 1);
        $estimatedDeliveryDate->preparation(5);
        $estimatedDeliveryDate->vacations(['2016-01-06']);

        $deliveryDate = [
            'min' => "2016-01-12",
            'max' => "2016-01-13"
        ];

        $this->assertEquals($deliveryDate, $estimatedDeliveryDate->estimation());
    }

    public function testDeliveryAndPreparationWithDeliveryVacations()
    {
        $currentDate = "2016-01-04";
        $estimatedDeliveryDate = new EstimatedDeliveryDate($currentDate);
        $estimatedDeliveryDate->delivery(0, 1);
        $estimatedDeliveryDate->preparation(5);
        $estimatedDeliveryDate->deliveryVacations(['2016-01-06']);

        $deliveryDate = [
            'min' => "2016-01-12",
            'max' => "2016-01-13"
        ];

        $this->assertEquals($deliveryDate, $estimatedDeliveryDate->estimation());
    }
}
