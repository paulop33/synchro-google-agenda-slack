<?php
namespace App\Model;

class Period3Months implements PeriodInterface
{
    const PERIOD = "P90D";

    public function shouldNotifyForImportantEvent(): bool
    {
        return true;
    }

    public function shouldNotifyForMediumEvent(): bool
    {
        return false;
    }

    public function shouldNotifyForLowEvent(): bool
    {
        return false;
    }

    public function getPeriodInterval(): string
    {
        return self::PERIOD;
    }
}
