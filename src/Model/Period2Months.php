<?php
namespace App\Model;

class Period2Months implements PeriodInterface
{
    const PERIOD = "P60D";

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
