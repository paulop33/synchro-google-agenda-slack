<?php
namespace App\Model;

interface PeriodInterface {
    public function getPeriodInterval(): string;
    public function shouldNotifyForImportantEvent(): bool;
    public function shouldNotifyForMediumEvent(): bool;
    public function shouldNotifyForLowEvent(): bool;
}