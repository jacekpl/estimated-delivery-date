<?php

class EstimatedDeliveryDate
{
    /** @var  DateTimeImmutable $currentDate */
    private $currentDate = 'now';
    private $deliveryMin = 0;
    private $deliveryMax = 0;
    private $preparation = 0;
    private $vacations = [];
    private $deliveryVacations = [];
    private $preparationDaysOfWeek = [1,2,3,4,5]; //1 is Monday, 5 is Friday
    private $deliveryDaysOfWeek = [1,2,3,4,5]; //1 is Monday, 5 is Friday

    public function __construct($currentDate)
    {
        $date = new DateTimeImmutable();
        $this->currentDate = $date->createFromFormat('Y-m-d', $currentDate, new DateTimeZone('UTC'));
    }

    public function delivery($min, $max)
    {
        $this->deliveryMin = $min;
        $this->deliveryMax = $max;
    }

    public function preparation($days)
    {
        $this->preparation = $days;
    }

    public function vacations(array $vacations)
    {
        $this->vacations = $vacations;
    }

    public function deliveryVacations(array $vacations)
    {
        $this->vacations = $vacations;
    }

    public function estimation()
    {
        $preparationDate = $this->prepare($this->currentDate, $this->preparation);
        $daysMin = $this->deliver($this->currentDate, $this->deliveryMin) + $preparationDate;
        $daysMax = $this->deliver($this->currentDate, $this->deliveryMax) + $preparationDate;


        $min = $this->currentDate->modify('+' . $daysMin . 'days');
        $max = $this->currentDate->modify('+' . $daysMax . 'days');

        return [
            'min' => $min->format('Y-m-d'),
            'max' => $max->format('Y-m-d'),
        ];
    }

    private function prepare(DateTimeImmutable $date, $preparation)
    {
        $workingDays = 0;

        for($i=0;; $i++) {
            $calendarDay = $date->modify('+' . $i . ' days');

            if(in_array($calendarDay->format('Y-m-d'), $this->vacations)) {
                continue;
            } elseif(!in_array($calendarDay->format('N'), $this->preparationDaysOfWeek)) {
                continue;
            }

            $workingDays++;

            if($workingDays > $preparation) {
                break;
            }
        }

        return $i;
    }

    private function deliver(DateTimeImmutable $date, $delivery)
    {
        $workingDays = 0;

        for($i=0;; $i++) {
            $calendarDay = $date->modify('+' . $i . ' days');

            if(in_array($calendarDay->format('Y-m-d'), $this->deliveryVacations)) {
                continue;
            } elseif(!in_array($calendarDay->format('N'), $this->deliveryDaysOfWeek)) {
                continue;
            }

            $workingDays++;

            if($workingDays > $delivery) {
                break;
            }
        }

        return $i;
    }
}
