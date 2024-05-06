<?php
use Carbon\Carbon;

function collisionsPerIntervals ($date1,$date2,$date3,$date4){

    $fecha1 = Carbon::parse($date1)->format('H:i');
    $fecha2 = Carbon::parse($date2)->format('H:i');    
    if($fecha2 < $fecha1){
        $split = explode(':', $fecha2);
        $hour =  intval($split[0]) + 24;
        $fecha2 = $hour.":".$split[1];
    }

    $fecha3 = Carbon::parse($date3)->format('H:i');  
    $fecha4 = Carbon::parse($date4)->format('H:i');
    if($fecha4 < $fecha3){
        $split = explode(':', $fecha4);
        $hour =  intval($split[0]) + 24;
        $fecha4 = $hour.":".$split[1];
    }   

    $inicio_interseccion = max($fecha1, $fecha3);
    $fin_interseccion = min($fecha2, $fecha4);
    if ($inicio_interseccion < $fin_interseccion) {
        if(explode(':',$fin_interseccion)[0] > 23)
            $fin_interseccion = Carbon::parse((explode(':',$fin_interseccion)[0]-24).":".explode(':',$fin_interseccion)[1])->format('H:i');
       return response()->json([
        'status' => false,
        'mensaje'=> "el horario se cruza de " . $inicio_interseccion . " a " . $fin_interseccion
       ]);
    } else {
        return response()->json([
            'status' => true,
            'mensaje'=> 'el horario no se cruza'
           ]);
    }
}