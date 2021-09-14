<?php

namespace App\Service;

use DateTime;

Class Helper
{
    /**
     * Get the start and end date of a week from a given date
     */
    public static function getWeekStartAndEnd($date)
    {
        $datetime = new DateTime($date);
        $datetime->setISODate($datetime->format('Y'), $datetime->format('W'), 1);
        $result['week_start'] = $datetime->format('Y-m-d');
        $datetime->modify('+6days');
        $result['week_end'] = $datetime->format('Y-m-d');
        return $result;
    }

}
