# estimated-delivery-date
Calculate estimated delivery date

Example usage:

```
$currentDate = "2016-01-04 16:00"; //current date and time
$estimatedDeliveryDate = new EstimatedDeliveryDate($currentDate, 'Y-m-d H:i');
$estimatedDeliveryDate->preparation('17:00', 4, 5); //preparation time, days needed to prepare if ordered before 17:00 (4 days) and after (>=) 17:00 (5 days)
$estimatedDeliveryDate->preparationDaysOfWeek(array(1, 2, 3, 4, 5, 6));
$estimatedDeliveryDate->delivery(0, 1); //days min/max for delivery
$estimatedDeliveryDate->deliveryDaysOfWeek(array(1, 2, 3, 4, 5, 6));
$estimatedDeliveryDate->vacations(array('2016-01-05')); //sender not working days
$estimatedDeliveryDate->deliveryVacations(array('2016-01-06')); //delivery not working days

$deliveryDate = $estimatedDeliveryDate->estimation(); //returns array
```

Estimation returns array:
```
$deliveryDate = array(
  'min' => "2015-12-28",
  'max' => "2015-12-29"
);
```

You can format that date (French translation):
```
$estimatedDeliveryDate->formatDate($deliveryDate);
```

Which returns:
```
lundi 28 décembre 2015 / mardi 29 décembre 2015
```

Feel free to ask any questions on jacek@opcode.pl
