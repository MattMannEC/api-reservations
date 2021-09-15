<?php

namespace App\Service;

use DateTime;
use JMS\Serializer\SerializerBuilder;

Class Helper
{
    /**
     * Get the start and end date of a week from a given date
     */
    public static function getWeekStartAndEnd($date): array
    {
        $datetime = new DateTime($date);
        $datetime->setISODate($datetime->format('Y'), $datetime->format('W'), 1);
        $result['week_start'] = $datetime->format('Y-m-d');
        $datetime->modify('+6days');
        $result['week_end'] = $datetime->format('Y-m-d');

        return $result;
    }

    public static function serialize($data): string
    {
        $seriaizer = SerializerBuilder::create()->build();

        return $seriaizer->serialize($data, 'json');
    }


}
