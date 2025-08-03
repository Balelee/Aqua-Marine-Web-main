<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use DateTime;

class TimeslotController extends Controller
{
    public function timeslot(Request $request)
    {
        $store_id = $request->store_id;
        $current_time = Carbon::Now();
        $date = date('Y-m-d');
        $tes2 = "12:00";
        $tes2 = date("h:i a", strtotime($tes2));

        $time_slot = DB::table('store')->where('id', $store_id)->first();

        $starttime  = $time_slot->store_opening_time;
        $endtime  = $time_slot->store_closing_time;
        $duration  = $time_slot->time_interval;
        $selected_date  = $request->selected_date;

        $orders = $time_slot->orders;
        $array_of_time = array();
        $array_of_time1 = array();
        $min = 10;

        $currenttime = strtotime("+".$min." minutes", strtotime($current_time));
        $start_time    = strtotime($starttime); // Change to strtotime
        $end_time      = strtotime($endtime); // Change to strtotime

        $add_mins  = $duration * 60;

        // La boucle génère les créneaux horaires
        while ($start_time <= $end_time) { // Loop between time
            $array_of_time[] = date("H:i", $start_time);

            $start_time += $add_mins;
        }

        $new_array_of_time = array();
        for ($i = 0; $i < count($array_of_time) - 1; $i++) {
            $new_array_of_time[] = '' . $array_of_time[$i] . ' - ' . $array_of_time[$i + 1];
        }

        // Vérification de la disponibilité des créneaux horaires
        $new_array_of_time1 = array();
        foreach ($new_array_of_time as $time_slot) {
            $totorders = DB::table('orders')
                ->where('delivery_date', $selected_date)
                ->where('time_slot', $time_slot)
                ->count();

            if ($orders > $totorders) {
                // Marquer comme disponible
                $new_array_of_time1[] = array('timeslot' => $time_slot, 'availibility' => 'available');
            } else {
                // Marquer comme indisponible
                $new_array_of_time1[] = array('timeslot' => $time_slot, 'availibility' => 'unavailable');
            }
        }

        // Si des créneaux horaires sont disponibles, renvoyer les créneaux
        if (count($new_array_of_time1) > 0) {
            $message = array('status' => '1', 'message' => 'Present time Slot', 'data' => $new_array_of_time1);
            return $message;
        } else {
            // Si aucun créneau n'est disponible, renvoyer un message d'erreur
            $message = array('status' => '0', 'message' => 'Oops No time slot present', 'data' => $current_time);
            return $message;
        }
    }



}
