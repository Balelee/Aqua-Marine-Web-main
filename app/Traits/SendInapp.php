<?php

namespace App\Traits;
use DB;
use Mail;
use App\Users;
use Carbon\Carbon;

trait SendInapp {
    ////for user////
    ///////Order Placed///////
    public function codorderplacedinapp($cart_id,$prod_name,$price2,$delivery_date,$time_slot,$user_email,$user_name,$user_id) {
                  $notification_title = "Salut ".$user_name.", Votre commande est passée";
                $notification_text = "Commande passée avec succès: Votre numéro de commande est #".$cart_id." contient " .$prod_name." au prix de ".$price2. " est placé avec succès. Vous pouvez vous attendre à ce que votre (vos) article(s) soient livré(s) le ".$delivery_date." entre ".$time_slot.".";
                
                $date = date('d-m-Y');
        
        
                $getDevice = DB::table('users')
                         ->where('id', $user_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->first();
                            
                $getFcmKey = $getFcm->server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
             
                $dd = DB::table('user_notification')
                    ->insert(['user_id'=>$user_id,
                     'noti_title'=>$notification_title,
                     'noti_message'=>$notification_text]);
                    
                $results = json_decode($result);
                }
          }
    
    //////////confirm Order /////////
     public function orderconfirmedinapp($cart_id,$user_phone,$orr){
                 $user = DB::table('users')
                       ->where('user_phone', $user_phone)
                       ->first();
                  $user_name = $user->name;
                  $notification_title = "Salut ! ".$user_name.", Votre commande est confirmée";
                $notification_text = "Votre commande est confirmée: Votre numéro de commande est #".$cart_id." est confirmée par le magasin. Vous pouvez vous attendre à ce que votre (vos) article(s) soient livré(s) le ".$orr->delivery_date." (".$orr->time_slot.").";
                
                $date = date('d-m-Y');
        
        
                $getDevice = DB::table('users')
                         ->where('id', $orr->user_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
             
                $dd = DB::table('user_notification')
                    ->insert(['user_id'=>$orr->user_id,
                     'noti_title'=>$notification_title,
                     'noti_message'=>$notification_text]);
                    
                $results = json_decode($result);
                }
             }
             
         ///////Out For Delivery(Prepaid) /////////        
    public function deloutinappcard($cart_id, $prod_name, $price2,$currency,$ord,$user_phone,$user_id,$store_n){
          $us = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
                $user_name = $us->name; 
         $notification_title = "Salut ! ".$user_name.", Votre commande est prête à être livrée";
                        $notification_text = "En attente de livraison: Votre numéro de commande est #".$cart_id." contient " .$prod_name." au prix de " .$currency->currency_sign." ".$price2. " est prêt à être livré.";
                        $date = date('d-m-Y');
                        $getDevice = DB::table('users')
                                 ->where('id', $user_id)
                                ->select('device_id')
                                ->first();
                        $created_at = Carbon::now();
                        if($getDevice){
                        $getFcm = DB::table('fcm')
                                    ->where('id', '1')
                                    ->first();
                                    
                        $getFcmKey = $getFcm->server_key;
                        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                        $token = $getDevice->device_id;
                            $notification = [
                                'title' => $notification_title,
                                'body' => $notification_text,
                                'sound' => true,
                            ];
                            $extraNotificationData = ["message" => $notification];
                            $fcmNotification = [
                                'to'        => $token,
                                'notification' => $notification,
                                'data' => $extraNotificationData,
                            ];
                
                            $headers = [
                                'Authorization: key='.$getFcmKey,
                                'Content-Type: application/json'
                            ];
                
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                            $result = curl_exec($ch);
                            curl_close($ch);
                        $dd = DB::table('user_notification')
                            ->insert(['user_id'=>$user_id,
                             'noti_title'=>$notification_title,
                             'noti_message'=>$notification_text]);
                            
                        $results = json_decode($result);
                        }
    }         
          ///////Out For Delivery(COD) /////////   
       public function deloutinapp($cart_id, $prod_name, $price2,$currency,$ord,$user_phone,$user_id,$store_n){
                $us = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
                $user_name = $us->name;    
                $notification_title = "Salut ! ".$user_name.", Votre commande est prête à être livrée";
                $notification_text = "En attente de livraison: Votre numéro de commande est #".$cart_id." contient " .$prod_name." au prix de ".$currency->currency_sign." ".$price2. " est prêt à être livré. ".$currency->currency_sign." ".$ord->rem_price. " argent liquide.";
                
                $date = date('d-m-Y');
        
        
                $getDevice = DB::table('users')
                         ->where('id', $user_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
             
                $dd = DB::table('user_notification')
                    ->insert(['user_id'=>$user_id,
                     'noti_title'=>$notification_title,
                     'noti_message'=>$notification_text]);
                    
                $results = json_decode($result);
                } 
       }     
             
      ///////Delivery Complete////////////
          public function delcominapp($cart_id, $prod_name, $price2,$currency,$ord,$user_phone,$user_id){  
                $us = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
                $user_name = $us->name; 
                $notification_title = "Salut ! ".$user_name.", Votre commande a été livrée";
                $notification_text = "Livraison terminée: Votre numéro de commande est #".$cart_id." contient " .$prod_name." au prix de ".$currency->currency_sign." ".$price2." est délivré avec succès.";
                
                $date = date('d-m-Y');
        
        
                $getDevice = DB::table('users')
                         ->where('id', $user_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
             
                $dd = DB::table('user_notification')
                    ->insert(['user_id'=>$user_id,
                     'noti_title'=>$notification_title,
                     'noti_message'=>$notification_text]);
                    
                $results = json_decode($result);
                }
           }
     ////////////Order reject By Admin////////
     
    public function sendrejectnotification($cause,$user,$cart_id,$user_id){
     $notification_title = "Désolé! nous annulons votre commande";
                        $notification_text = 'Salut '.$user->name.', Nous annulons votre commande ('.$cart_id.') pour la raison suivante:  '.$cause;
                        $date = date('d-m-Y');
                        $getDevice = DB::table('users')
                                 ->where('id', $user_id)
                                ->select('device_id')
                                ->first();
                        $created_at = Carbon::now();
                        if($getDevice){
                        $getFcm = DB::table('fcm')
                                    ->where('id', '1')
                                    ->first();
                                    
                        $getFcmKey = $getFcm->server_key;
                        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                        $token = $getDevice->device_id;
                            $notification = [
                                'title' => $notification_title,
                                'body' => $notification_text,
                                'sound' => true,
                            ];
                            $extraNotificationData = ["message" => $notification];
                            $fcmNotification = [
                                'to'        => $token,
                                'notification' => $notification,
                                'data' => $extraNotificationData,
                            ];
                
                            $headers = [
                                'Authorization: key='.$getFcmKey,
                                'Content-Type: application/json'
                            ];
                
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                            $result = curl_exec($ch);
                            curl_close($ch);
                        $dd = DB::table('user_notification')
                            ->insert(['user_id'=>$user_id,
                             'noti_title'=>$notification_title,
                             'noti_message'=>$notification_text]);
                            
                        $results = json_decode($result);
                        }
}
           
           
           
           
    ////for store////
     ///////Order Placed///////
     public function codorderplacedinappstore($cart_id,$prod_name,$price2,$delivery_date,$time_slot,$user_email,$user_name,$store_n,$store_id) {
     $notification_title = "Salut ! ".$store_n.", Vous avez une nouvelle commande";
                $notification_text = "Vous avez un identifiant de panier de commande qui est #".$cart_id." contient " .$prod_name." au prix de ".$price2. ". Elle devra être livrée sur ".$delivery_date." entre ".$time_slot.".";
                
                $date = date('d-m-Y');
                $getUser = DB::table('store')
                                ->get();
        
                $getDevice = DB::table('store')
                         ->where('id', $store_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->store_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
                     ///////send notification to store//////
             
                $dd = DB::table('store_notification')
                    ->insert(['store_id'=>$store_id,
                     'not_title'=>$notification_title,
                     'not_message'=>$notification_text]);
                    
                $results = json_decode($result);
                }
    
     }
    
    /////Out For Delivery////////////////////
    
     public function deloutinappstore($cart_id, $prod_name, $price2,$currency,$ord,$user_phone,$user_id,$store_id,$store_n) {
     $notification_title = "Salut ! ".$store_n.", Identifiant de commande #".$cart_id." est en attente de livraison.";
                $notification_text = "En attente de livraison: Identifiant de commande est #".$cart_id." contient " .$prod_name." au prix de ".$currency->currency_sign." ".$price2. " est en attente de livraison.";
                
                $date = date('d-m-Y');
                $getUser = DB::table('store')
                                ->get();
        
                $getDevice = DB::table('store')
                         ->where('id', $store_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->store_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
                     ///////send notification to store//////
             
                $dd = DB::table('store_notification')
                    ->insert(['store_id'=>$store_id,
                     'not_title'=>$notification_title,
                     'not_message'=>$notification_text]);
                    
                $results = json_decode($result);
                }
    
     }
      /////Delivery Completed////////////////////
    
     public function delcominappstore($cart_id, $prod_name, $price2,$currency,$ord,$user_phone,$user_id,$store_id) {
         $st = DB::table('store')
             ->where('id', $store_id)
             ->first();
        $store_n = $st->store_name;     
     $notification_title = "Salut ! ".$store_n.", Identifiant de commande #".$cart_id." a été délivré.";
                $notification_text = "Livraison terminée: Identifiant de commande est #".$cart_id." contient " .$prod_name." au prix de ".$currency->currency_sign." ".$price2. " a été délivré.";
                
                $date = date('d-m-Y');
                $getUser = DB::table('store')
                                ->get();
        
                $getDevice = DB::table('store')
                         ->where('id', $store_id)
                        ->select('device_id')
                        ->first();
                $created_at = Carbon::now();
        
                if($getDevice){
                
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->store_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
                     ///////send notification to store//////
             
                $dd = DB::table('store_notification')
                    ->insert(['store_id'=>$store_id,
                     'not_title'=>$notification_title,
                     'not_message'=>$notification_text]);
                    
                $results = json_decode($result);
                }
    
     }
    
    
    /////for driver////
    public function orderconfirmedinappdriver($getDDevice,$cart_id,$user_phone,$orr,$curr){
       $notification_title = "Salut ! ".$getDDevice->boy_name.", Vous avez reçu une nouvelle commande à livrer le ".$orr->delivery_date;
                $notification_text = "vous avez reçu une commande avec l'identifiant du panier est #".$cart_id." au prix de ".$curr->currency_sign." " .$orr->total_price. ". Elle devra être livrée le ".$orr->delivery_date." entre ".$orr->time_slot.".";
                
                $date = date('d-m-Y');
               
        
                $created_at = Carbon::now();
        
                
                $getFcm = DB::table('fcm')
                            ->where('id', '1')
                            ->first();
                            
                $getFcmKey = $getFcm->driver_server_key;
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                $token = $getDDevice->device_id;
                    
        
                    $notification = [
                        'title' => $notification_title,
                        'body' => $notification_text,
                        'sound' => true,
                    ];
                    
                    $extraNotificationData = ["message" => $notification];
        
                    $fcmNotification = [
                        'to'        => $token,
                        'notification' => $notification,
                        'data' => $extraNotificationData,
                    ];
        
                    $headers = [
                        'Authorization: key='.$getFcmKey,
                        'Content-Type: application/json'
                    ];
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    
                    
                      $dd = DB::table('driver_notification')
                    ->insert(['dboy_id'=>$getDDevice->dboy_id,
                     'not_title'=>$notification_title,
                     'not_message'=>$notification_text]);
                   $results = json_decode($result);
                   
    }
    
    
     public function sendnotification($user,$userin,$notification_title,$notification_text,$notify_image) {
         
         
            
            $userin = DB::table('users')->select('device_id','name','id')
                        ->WhereIn('id', $user)
                        ->get();
                        
         $getFcm = DB::table('fcm')
                    ->where('id', '1')
                    ->first();
                    
        $getFcmKey = $getFcm->server_key;
        
        

        //you can insert post data into db for record here
        
        foreach($userin as $us){
        $get_device_id[] = $us;
        }
        $loop =  count(array_chunk($get_device_id,400));  // count array chunk 1000
        $arrayChunk = array_chunk($get_device_id,400);   // devide array in 1000 chunk
        $device_id = array();
        for($i=0; $i<$loop;$i++)
        {
            foreach($arrayChunk[$i] as $all_device_id)
            {       
                   
                        $device_id[] =  $all_device_id->device_id;
                        
                                    $insertNotification = DB::table('user_notification')
                                    ->insert([
                                        'user_id'=>$all_device_id->id,
                                        'noti_title'=>$notification_title,
                                         'image'=>$notification_text,
                                        'noti_message'=>$notify_image,
                                      
                                    ]);
            }
             $url = 'https://fcm.googleapis.com/fcm/send';
            $body=$notification_text;
            $customData=$url;
            $json_data = 
                [
                    "registration_ids" => $device_id,
                    "notification" => [
                        "body" => $body,
                        "title" => $notification_title,
                    ],
                    "data" => [
                        "extra" => $customData
                    ]
                ];
         $data = json_encode($json_data); 
       
        //api_key in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
        $server_key = $getFcmKey;
        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$server_key
        );
        // CURL request to route notification to FCM connection server (provided by Google)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Oops! FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
            unset($device_id); // unset the array value 

        }
        
                        
     }
    
             
}