<?php

namespace App\Helpers;

/**
 * Class DateTimeConverter
 * @package App\Utils
 */
class DateTimeConverter
{

    /**
     * @return string
     */
    public static function getDateTimeNow(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @param $dateTime
     * @param string $format
     * @return string
     */
    public static function dateTimeFormat($dateTime, string $format = 'Y-m-d'): string
    {
        return date($format, strtotime($dateTime));
    }

    /**
     * @param $expire
     * @return string
     */
    public static function expiredMinute($expire): string
    {
        return date("Y-m-d H:i:s", strtotime("+$expire min"));
    }

    /**
     * @param $date
     * @param $second
     * @return string
     */
    public static function dateAddSecond($date, $second)
    {
        return date('Y-m-d H:i:s', strtotime("+$second min", strtotime($date)));
    }

    /**
     * @param string $format
     * @return string
     */
    public static function dateTimeFormatNow(string $format)
    {
        return date($format);
    }

    /**
     * @param $date_first
     * @param $date_end
     * @return float
     */
    public static function calculatingDateTimeDifference($date_first, $date_end)
    {
        $diff = date_diff(date_create($date_first), date_create($date_end));
        return $diff->h;
    }
}
