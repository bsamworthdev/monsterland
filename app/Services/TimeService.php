<?php

namespace app\Services;

use Carbon\Carbon;

class TimeService{

  function getDateFromTimeFilter($time_filter){
    switch ($time_filter){
        case 'day':
            $date = \Carbon\Carbon::today()->subHours(24);
        break;
        case 'week':
            $date = \Carbon\Carbon::today()->subDays(7);
        break;
        case 'month':
            $date = \Carbon\Carbon::today()->subWeeks(4);
        break;
        case 'year':
            $date = \Carbon\Carbon::today()->subWeeks(52);
        break;
        case 'ever':
            $date = \Carbon\Carbon::today()->subYears(10);
        break;
    }
    return $date;
  }
  
}