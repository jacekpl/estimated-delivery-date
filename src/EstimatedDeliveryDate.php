<?php

class EstimatedDeliveryDate
{
    /**
     * Current date
     * Based on provided date script makes estimations
     * @var DateTimemutable $currentDate
     */
    private $currentDate;

    /**
     * Minimum delivery time (in days)
     * @var int
     */
    private $deliveryMin = 0;

    /**
     * Maximum delivery time (in days)
     * @var int
     */
    private $deliveryMax = 0;

    /**
     * Preparation time (in days)
     * @var int
     */
    private $preparation = 0;

    /**
     * Vacations (shop owner)
     * @var array
     */
    private $vacations = array();

    /**
     * Vacations (delivery company)
     * @var array
     */
    private $deliveryVacations = array();

    /**
     * Days during which we prepare goods for delivery
     * @var array
     */
    private $preparationDaysOfWeek = array(1,2,3,4,5); //1 is Monday, 5 is Friday

    /**
     * Delivery working days
     * @var array
     */
    private $deliveryDaysOfWeek = array(1,2,3,4,5); //1 is Monday, 5 is Friday

    public function __construct($currentDate = 'now', $format = 'Y-m-d')
    {
        $date = new DateTime();
        $this->currentDate = $date->createFromFormat($format, $currentDate, new DateTimeZone('UTC'));
    }

    public function delivery($min, $max)
    {
        $this->deliveryMin = $min;
        $this->deliveryMax = $max;
    }

    public function preparation($hour, $before, $after)
    {
        $date = new DateTime();
        $preparationHour = $date->createFromFormat('H:i', $hour, new DateTimeZone('UTC'));

        $this->preparation = $after;

        if($this->currentDate->format('H:i') < $preparationHour->format(('H:i'))) {
            $this->preparation = $before;
        }
    }

    public function vacations(array $vacations)
    {
        $this->vacations = $vacations;
    }

    public function deliveryVacations(array $vacations)
    {
        $this->vacations = $vacations;
    }

    public function preparationDaysOfWeek(array $days)
    {
        $this->preparationDaysOfWeek = $days;
    }

    public function workingDaysOfWeek(array $days)
    {
        $this->deliveryDaysOfWeek = $days;
    }

    public function estimation()
    {
        $currentDate = clone $this->currentDate;
        $preparationDate = $this->prepare($currentDate, $this->preparation);

        $currentDate = clone $this->currentDate;
        $daysMin = $this->deliver($currentDate, $this->deliveryMin) + $preparationDate;

        $currentDate = clone $this->currentDate;
        $daysMax = $this->deliver($currentDate, $this->deliveryMax) + $preparationDate;

        $currentDate = clone $this->currentDate;
        $min = $currentDate->modify('+' . $daysMin . 'days');

        $currentDate = clone $this->currentDate;
        $max = $currentDate->modify('+' . $daysMax . 'days');

        return array(
            'min' => $min->format('Y-m-d'),
            'max' => $max->format('Y-m-d'),
        );
    }

    private function prepare(DateTime $dateObj, $preparation)
    {
        $workingDays = 0;

        for($i=0;; $i++) {
            $date = clone $dateObj;
            
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

    //TODO: move to other class
    public function formatDate(array $dates)
    {
        $days = array('1' => 'lundi', '2' => 'mardi', '3' => 'mercredi', '4' => 'jeudi', '5' => 'vendredi', '6' => 'samedi', '7' => 'dimanche');
        $months = array('1' => 'janvier', '2' => 'février', '3' => 'mars', '4' => 'avril', '5' => 'mai', '6' => 'juin', '7' => 'juillet', '8' => 'août', '9' => 'septembre', '10' => 'octobre', '11' => 'novembre', '12' => 'décembre');

        $date = new DateTime();
        $minDate = $date->createFromFormat('Y-m-d', $dates['min']);
        $maxDate = $date->createFromFormat('Y-m-d', $dates['max']);

        return $days[$minDate->format('N')] . ' ' . $minDate->format('j') . ' ' . $months[$minDate->format('n')] . ' ' . $minDate->format('Y') . ' / ' . $days[$maxDate->format('N')] . ' ' . $maxDate->format('j') . ' ' . $months[$maxDate->format('n')] . ' ' . $minDate->format('Y');
    }

    private function deliver(DateTime $dateObj, $delivery)
    {
        $workingDays = 0;

        for($i=0;; $i++) {
            $date = clone $dateObj;

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
