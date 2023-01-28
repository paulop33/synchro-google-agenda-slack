<?php
namespace App\Model;

class Period1Month implements PeriodInterface
{
    const PERIOD = "P30D";

    public function shouldNotifyForImportantEvent(): bool
    {
        return true;
    }

    public function shouldNotifyForMediumEvent(): bool
    {
        return true;
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
