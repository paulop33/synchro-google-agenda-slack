<?php
namespace App\Model;

class Period1Week implements PeriodInterface
{
    const PERIOD = "P7D";

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
