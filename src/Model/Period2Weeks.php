<?php
namespace App\Model;

class Period2Weeks implements PeriodInterface
{
    const PERIOD = "P15D";

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
