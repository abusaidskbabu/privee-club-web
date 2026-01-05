<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Hash;
use Illuminate\Support\Facades\Auth;
use  App\Models\{User,DeviceInfo,PrivacyPolicy,TermsCondition,AboutApp,ProfileTimer};
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
 use Illuminate\Validation\Rule;
class AuthController extends Controller
{
 public function registerUSer(request $request){
      try {
           

                $rules = [
                    'first_name'   => 'required',
                    'last_name'    => 'required',
                    'profile_name' => 'required',
                    'email'        => [
                        'required',
                        'email',
                        Rule::unique('users')->where(function ($query) {
                            return $query->where('status', '!=', 0)
                                         ->where('admin_status', '!=', 2);
                        }),
                    ],
                    'country_code' => 'required',
                    'mobile_no'    => 'required',
                    'fcm_token'    => 'required',
                    'device_id'    => 'required',
                    'device_type'  => 'required',
                    'password'     => 'required|confirmed',
                    'language'     => 'required',
                ];
                
                $validator = Validator::make($request->all(), $rules);
                
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first(),
                    ], 422);
                }
               
                  
                  $lastNumber = User::max('member_number');
                  
                  if ($lastNumber) {
                    
                        $newNumber = str_pad(intval($lastNumber) + 1, 6, '0', STR_PAD_LEFT);
                    } else {
                       
                        $newNumber = '000001';
                    }

                $user = new User;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->profile_name = $request->profile_name;
                $user->email = $request->email;
                $user->country_code = $request->country_code;
                $user->mobile_no = $request->mobile_no;
                $user->password  = Hash::make($request->password);
                $user->language  = $request->language;
                $user->member_number = $newNumber;
                
                $otp = rand(100000, 999999);
                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now()->addMinutes(10); 
                
                $user->save();
                $user->refresh();
                
                  // otp send
                if ($request->language == "English") {
                        Mail::send('email_otp', ['user' => $user, 'otp' => $otp], function($message) use ($user){
                            $message->to($user->email, $user->profile_name)
                                    ->subject('Email Verification OTP');
                        });
                    }
                elseif($request->language == "Danish"){   
                 Mail::send('email_otp_dk', ['user' => $user, 'otp' => $otp], function($message) use ($user){
                        $message->to($user->email, $user->profile_name)
                                ->subject('E-mailbekræftelseskode');
                    });
                 }    
                  // confirmation email
                // Mail::send('email_registration', ['user' => $user], function($message) use ($user) {
                //         $message->to($user->email, $user->profile_name)
                //                 ->subject('Registration Successful');
                //     });

                $token  = $user->createToken('privee')->plainTextToken;
                $user['token'] =$token;
                
                      //genrate fcm token 
                  $fcmToken = new DeviceInfo;
                  $fcmToken->user_id   = $user->id;
                  $fcmToken->fcm_token = $token;
                  $fcmToken->device_id = $request->device_id;
                  $fcmToken->device_type = $request->device_type;
                  $fcmToken->save();
       
                return response()->json(['status'=>true,'status_code' => 200,'message'=>'User Registration Done succesfully','data'=>$user],200);
        
      } catch (\Throwable $e) {
         return response()->json(['status'=>false,'message'=>$e->getMessage()],422);
      }


    }
// ****************************************************Login*************************************************************************************
public function login(Request $request)
{
    // ---------------------------
    // Validation
    // ---------------------------
    $rules = [
        'email'       => 'required|email',
        'password'    => 'required',
        'device_id'   => 'required',
        'device_type' => 'required',
        'fcm_token'   => 'nullable'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
    }

 
    // Get NEWEST user with this email
     $user = User::where('email', $request->email)->orderBy('id', 'DESC')->first();

        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'status' => false,
                'message' => 'Wrong email or password',
            ]);
        }
        
        if($user->admin_status == 2 && $user->status == 0){
            
            return response()->json([
                'status_code' => 401,
                'status' => false,
                'message' => 'Profile declined',
            ]); 
            
        }
        
        // Check password manually
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status_code' => 401,
                'status' => false,
                'message' => 'Wrong email or password',
            ]);
        }

    // Login THIS user (not old one)
    Auth::login($user);
    // fetch profile time
    $profiletimer = (int) ProfileTimer::value('time');


    if (!is_null($user->admin_approve_time)) {

        $expireTime    = Carbon::parse($user->admin_approve_time)->addHours($profiletimer);
        $averageRating = $user->receivedRatings->avg('points') ?? 0;
        $totalRatings  = $user->receivedRatings->count();

        $canLogin = false;

        //  Still inside rating time window
        if ($expireTime >= now()) {
            $canLogin = true;
        }

        //  Already approved by admin
        if ($user->profile_approval == 1) {
            $canLogin = true;
        }

        //  Time expired BUT rating completed
        elseif ($expireTime && now() >= $expireTime && $averageRating > 0 && $totalRatings >= 4) {

            $user->profie_rating_status = 'IN';
            $user->save();

            $canLogin = true;
        }

        // login Not Allowed
        if (!$canLogin) {

            $user->status = 0; // block old account
            $user->save();

            Auth::logout();

            return response()->json([
                'status' => false,
                'status_code' => 403,
                'message' => 'Access denied. Your profile was not approved by our community. You’re welcome to apply again.',
            ], 403);
        }
    }

    // LOGIN SUCCESS
    $token = $user->createToken($request->device_id . '_token')->plainTextToken;
    $user['token'] = $token;

    DeviceInfo::updateOrCreate(
        ['user_id' => $user->id, 'device_id' => $request->device_id],
        [
            'device_type' => $request->device_type,
            'fcm_token'   => $request->fcm_token,
        ]
    );

    // registration status
    $incompleteFields = ['first_name', 'profile_image', 'gender', 'looking_for', 'weight', 'hear_about_us'];
    $registration_status = true;

    foreach ($incompleteFields as $field) {
        if (is_null($user->$field)) {
            $registration_status = false;
            break;
        }
    }

    $user['registration_status'] = $registration_status;

    return response()->json([
        'status' => true,
        'status_code' => 200,
        'message' => 'User login successful!',
        'data' => $user
    ], 200);
}

// ***************************************************************logout ********************************************************************************

    public function logout(request $request){

       try{
          $validated = $request->validate([
              'device_id' => 'required',
          ]);
          
          if(auth()->user()){
                $device = DeviceInfo::where('user_id', auth()->user()->id)->where('device_id', $request->device_id)->delete();
                auth()->user()->tokens()->delete();
               return response()->json(['status' => true,'code'=>200,'message' => 'Logout successfully']);
          }else{
              $error_message = 'failed to logout';
              return response()->json(['status' => false, 'message' => $error_message]);
          }            
        }catch(\Exception $e){
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
      
//*****************************************************************sendotp *********************************************************************************
    public function sendOtp(request $request){
        
     $request->validate([
        'email'=>'required|email',
        'language'=>'required'
      ]);
        
          $user = User::where('email', $request->email)->orderBy('id', 'DESC')->first();  
           
           
         if (!$user) {
                return response()->json(['status' => false, 'message' => 'Email not found, Please  enter vallid email'], 404);
            }
        $otp =rand(100000,999999); 
        $user->otp = $otp;
        $user->save();
                if ($request->language == "English") {
                                Mail::send('email_otp', ['user' => $user, 'otp' => $otp], function($message) use ($user){
                                    $message->to($user->email, $user->profile_name)
                                            ->subject('Email Verification OTP');
                                });
                            }
                elseif($request->language == "Danish"){   
                         Mail::send('email_otp_dk', ['user' => $user, 'otp' => $otp], function($message) use ($user){
                                $message->to($user->email, $user->profile_name)
                                        ->subject('E-mailbekræftelseskode');
                            });
                         }    
                
           return response()->json(['status' => true,'code'=>200, 'message' => 'OTP sent to your Registered Email']);
       
    }

// *****************************************************************verifyy otp ***************************************************************************************
public function verifyOtp(request $request){
   

     $request->validate([
        'otp'=>'required',
        'email'=>'required|email'
      ]);
    
        // $user = User::where('email',$request->email)->first();
        $user = User::where('email', $request->email)->orderBy('id', 'DESC')->first();  
    
        $otp = $user->otp;
    
      
        if( $user->otp != $request->otp){
        return response()->json(['status' => false,'code'=>400,  'message' => 'Invalid OTP'], 400);
        }

        return response()->json(['status' => true,'code'=>200, 'message' => 'OTP verified','otp' =>$otp]);
    
}

//****************************************************************************change password ************************************************************************

 public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || $user->otp != $request->otp) {
            return response()->json(['status' => false, 'code'=>400,'message' => 'Invalid request'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->save();

       return response()->json(['status' => true,'code'=>200, 'message' => ' Your Password reset successfully']);
        
  }

// ***************************************************************privacy pliciy******************************************************************************************************
 public function getPrivicy(Request $request)
  {
        $data =PrivacyPolicy::all(); 

       return response()->json(['status' => true,'code'=>200, 'message' => ' Data retrieve sucessfully','data'=>$data]);
        
  }

// ***********************************************************************term and condition *************************************************************************************************
 public function terms(Request $request)
  {
        $data =TermsCondition::all(); 

       return response()->json(['status' => true,'code'=>200, 'message' => ' Data retrieve sucessfully','data'=>$data]);
        
  }
  
 public function aboutApp(Request $request)
  {
    $data =AboutApp::all(); 

    return response()->json(['status' => true,'code'=>200, 'message' => ' Data retrieve sucessfully','data'=>$data]);
        
  }
  
   //verify email    
   public function verifyEmailOtp(request $request){
       
     $request->validate([
         'email'=>'required',
         'otp'=>'required'
        ]);
        
          //latest email
          $user = User::where('email', $request->email)->orderBy('id', 'DESC')->first();  
          
            if(!$user){
             return response()->json(['status'=>false,'message'=>'User not found'], 404);
            }
            
            if($user->otp !== $request->otp){
                return response()->json(['status'=>false,'message'=>'Invalid OTP'], 422);
            }
        
            if(Carbon::now()->greaterThan($user->otp_expires_at)){
                return response()->json(['status'=>false,'message'=>'OTP expired'], 422);
            }
        
            // OTP verified → update user status
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();
    
        return response()->json([
            'status'=>true,
            'status_code'=>200,
            'message'=>'Email verified successfully',
            'data'=> $user
        ]);
    }    
        
       
       
  
       
    







}
