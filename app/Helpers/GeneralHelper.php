<?php

use Carbon\Carbon;
use App\Models\Projects\Calendar;
use App\Models\Projects\CalendarInterval;
use App\Models\Projects\ProjectWithCalendar;
function collisionsPerIntervals($date1, $date2, $date3, $date4)
{

    $fecha1 = Carbon::parse($date1)->format('H:i');
    $fecha2 = Carbon::parse($date2)->format('H:i');
    if ($fecha2 < $fecha1) {
        $split = explode(':', $fecha2);
        $hour =  intval($split[0]) + 24;
        $fecha2 = $hour . ":" . $split[1];
    }

    $fecha3 = Carbon::parse($date3)->format('H:i');
    $fecha4 = Carbon::parse($date4)->format('H:i');
    if ($fecha4 < $fecha3) {
        $split = explode(':', $fecha4);
        $hour =  intval($split[0]) + 24;
        $fecha4 = $hour . ":" . $split[1];
    }

    $inicio_interseccion = max($fecha1, $fecha3);
    $fin_interseccion = min($fecha2, $fecha4);
    if ($inicio_interseccion < $fin_interseccion) {
        if (explode(':', $fin_interseccion)[0] > 23)
            $fin_interseccion = Carbon::parse((explode(':', $fin_interseccion)[0] - 24) . ":" . explode(':', $fin_interseccion)[1])->format('H:i');
        return [$inicio_interseccion, $fin_interseccion];
    }
    return false;
}

function createDefaultCalendar(){
    $calendar = Calendar::create([
        'name'=> 'Calendario Predeterminado',
        'expanded'=>1,
        'version'=> 2,
        'unspecifiedTimeIsWorking' => false
    ]);
    $calendar->save();
    $days = explode(',','MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY');
    foreach ($days as $day) {
        CalendarInterval::create([
            'calendar_id' => $calendar->id,
            'isWorking' => true,
            'priority' => 30,
            'recurrentEndDate' => 'on '.$day.' at 12:00',
            'recurrentStartDate' =>'on '.$day.' at 07:00'
        ]);
        CalendarInterval::create([
            'calendar_id' => $calendar->id,
            'isWorking' => true,
            'priority' => 30,
            'recurrentEndDate' => 'on '.$day.' at 16:30',
            'recurrentStartDate' =>'on '.$day.' at 13:00'
        ]);
    }
    return $calendar;
}
