<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Models\Admin;
use Auth;

class PagesController extends Controller
{
    public function about_us(Request $request)
    {
        $title = "About Us";
          $admin_email=Auth::guard('admin')->user()->email;
    	$admin= DB::table('admin')
    	         ->leftJoin('roles','admin.role_id','=','roles.role_id')
    	 		 ->where('admin.email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
          $check = DB::table('aboutuspage')
                ->first();
    	return view('admin.about_us', compact('title',"admin", "logo", "check"));
    }
    
    public function updateabout_us(Request $request)
    {
        $title="About Us";
        $description = $request->description;
         $check = DB::table('aboutuspage')
                ->first();
                
        if($check){
            $update = DB::table('aboutuspage')
                    ->update(['description'=>$description]);
        }   
        else{
            $update = DB::table('aboutuspage')
                    ->insert(['title'=>$title,
                    'description'=>$description]);
        }
     if($update){
          return redirect()->back()->withSuccess(trans('keywords.Updated successfully'));
     }            
     else{
          return redirect()->back()->withErrors(trans('keywords.Something Wents Wrong'));
     }
    }
    
    public function terms(Request $request)
    {
        $title = "Terms & Condition";
         $admin_email=Auth::guard('admin')->user()->email;
    	$admin= DB::table('admin')
    	         ->leftJoin('roles','admin.role_id','=','roles.role_id')
    	 		 ->where('admin.email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
          $check = DB::table('termspage')
                ->first();
    	return view('admin.terms', compact('title',"admin", "logo", "check"));
    }
    
    public function updateterms(Request $request)
    {
        $title="Terms & Condition";
        $description = $request->description;
         $check = DB::table('termspage')
                ->first();
                
        if($check){
            $update = DB::table('termspage')
                    ->update(['description'=>$description]);
        }   
        else{
            $update = DB::table('termspage')
                    ->insert(['title'=>$title,
                    'description'=>$description]);
        }
     if($update){
          return redirect()->back()->withSuccess(trans('keywords.Updated successfully'));
     }            
     else{
          return redirect()->back()->withErrors(trans('keywords.Something Wents Wrong'));
     }
    }
    
    public function privacy_policy(Request $request)
    {
        $title = "Privacy Policy";
        $admin_email=Auth::guard('admin')->user()->email;
    	$admin= DB::table('admin')
    	         ->leftJoin('roles','admin.role_id','=','roles.role_id')
    	 		 ->where('admin.email',$admin_email)
    	 		   ->first();
    	$logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
        $check = DB::table('privacypage')
                ->first();
    	return view('admin.privacy', compact('title',"admin", "logo", "check"));
    }
    
    public function updateprivacy(Request $request)
    {
        $title = "Privacy Policy";
        $description = $request->description;
        $check = DB::table('privacypage')
                ->first();
                
        if($check){
            $update = DB::table('privacypage')
                    ->update(['description'=>$description]);
        }   
        else{
            $update = DB::table('privacypage')
                    ->insert(['title'=>$title,
                    'description'=>$description]);
        }
     if($update){
          return redirect()->back()->withSuccess(trans('keywords.Updated successfully'));
     }            
     else{
          return redirect()->back()->withErrors(trans('keywords.Something Wents Wrong'));
     }
    }
}
