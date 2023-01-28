<?php
namespace App\Model;

class Period1Day implements PeriodInterface
{
    const PERIOD = "P1D";

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
        return true;
    }

    public function getPeriodInterval(): string
    {
        return self::PERIOD;
    }
}
