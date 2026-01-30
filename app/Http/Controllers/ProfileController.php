<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{UserIntrest, UserImage, User, LookngFor, City, Region, Nationality, SexOrientation, Zodiac, BodyType, UserRate, Notification, blockedUsers, RequestModel, ProfileTimer, DeviceInfo, Children, CustomOption, Translation};
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{


    public function viewUser($id)
    {
        $user = User::with('profile', 'bestImage')->where('id', $id)->first();
        $rategiven = UserRate::with('ratedTo')->where('sender_id', $id)->get();
        $raterecived = UserRate::with('ratedBy')->where('reciever_id', $id)->get();


        return response()->json([
            'status'      => true,
            'status_code' => 200,
            "user"     => $user,
            "rategiven"     => $rategiven,
            "raterecived"     => $raterecived,
        ], 200);
    }

    //************************************************************edit account ******************************************************************
    public function editAccount(Request $request)
    {
        try {

            $request->validate([
                'profile_name' => 'nullable',
                'first_name'  => 'nullable',
                'last_name'   => 'nullable',
                'mobile_no'   => 'nullable',
                'email'       => 'nullable',
                'dob'         => 'nullable',
                'gender'      => 'nullable'
            ]);

            $user = Auth::user();

            if ($request->profile_name) {
                $user->profile_name = $request->profile_name ?? $user->profile_name;
            }
            if ($request->first_name) {
                $user->first_name = $request->first_name ?? $user->first_name;
            }
            if ($request->last_name) {
                $user->last_name = $request->last_name ?? $user->last_name;
            }
            if ($request->mobile_no) {
                $user->mobile_no = $request->mobile_no ?? $user->mobile_no;
            }
            if ($request->email) {
                $user->email = $request->email ?? $user->email;
            }
            if ($request->dob) {
                $user->dob = $request->dob ?? $user->dob;
            }
            if ($request->gender) {
                $user->gender = $request->gender ?? $user->gender;
            }

            $user->save();

            return response()->json([
                'status'      => true,
                'status_code' => 200,
                'message'     => "Account detail updated sucessfully",
                'data'        => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // ********************************************************************edit about**************************************************************************
    public function editAbout(Request $request)
    {
        try {

            $request->validate([
                'about_me' => 'nullable'
            ]);

            $user = Auth::user();

            if ($request->about_me) {
                $user->about_me = $request->about_me ?? $user->about_me;
            }

            $user->save();

            return response()->json([
                'status'      => true,
                'status_code' => 200,
                'message'     => "User about me detail updated sucessfully",
                'data'        => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }


    // ***********************************************************************editperfectMatch**********************************************************  
    public function editAboutMatch(Request $request)
    {
        try {

            $request->validate([
                'about_match' => 'nullable'
            ]);

            $user = Auth::user();

            if ($request->about_match) {
                $user->about_match = $request->about_match ?? $user->about_match;
            }

            $user->save();

            return response()->json([
                'status'      => true,
                'status_code' => 200,
                'message'     => "User about match detail updated sucessfully",
                'data'        => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    //  *********************************************************************edit personall info********************************************************
    public function editPersonallInfo(request $request)
    {

        try {

            $rules = [

                "height" => "nullable",
                "weight" => "nullable",
                "body_type" => "nullable",
                "hair_color" => "nullable",
                "eye_color" => "nullable",
                "nationality" => "nullable",
                "region" => "nullable",
                "city" => "nullable",
                "sexual_orientation" => "nullable",
                "education" => "nullable",
                "field_of_work" => "nullable",
                "relationship_status" => "nullable",
                "zodiac_sign" => "nullable",
                "smoking" => "nullable",
                "drinking" => "nullable",
                "tattoos" => "nullable",
                "piercings" => "nullable",


            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    "status" => false,
                    "message" => "Unauthorized"
                ], 401);
            }

            if ($request->height) {

                $user->height = $request->height ?? $user->height;
            }

            if ($request->weight) {

                $user->weight = $request->weight ?? $user->weight;
            }

            if ($request->body_type) {

                $user->body_type = $request->body_type ?? $user->body_type;
            }

            if ($request->hair_color) {

                $user->hair_color = $request->hair_color ?? $user->hair_color;
            }

            if ($request->eye_color) {

                $user->eye_color = $request->eye_color ?? $user->eye_color;
            }

            if ($request->nationality) {

                $user->nationality = $request->nationality ?? $user->nationality;
            }

            if ($request->region) {

                $user->region = $request->region ?? $user->region;
            }

            if ($request->city) {

                $user->city = $request->city ?? $user->city;
            }

            if ($request->sexual_orientation) {

                $user->sexual_orientation = $request->sexual_orientation ?? $user->sexual_orientation;
            }

            if ($request->education) {

                $user->education = $request->education ?? $user->education;
            }

            if ($request->field_of_work) {

                $user->field_of_work = $request->field_of_work ?? $user->field_of_work;
            }

            if ($request->relationship_status) {

                $user->relationship_status = $request->relationship_status ?? $user->relationship_status;
            }

            if ($request->zodiac_sign) {

                $user->zodiac_sign = $request->zodiac_sign ?? $user->zodiac_sign;
            }

            if ($request->has('smoking')) {

                $user->smoking = $request->smoking ?? $user->smoking;
            }

            if ($request->has('drinking')) {

                $user->drinking = $request->drinking ?? $user->drinking;
            }

            if ($request->has('tattoos')) {

                $user->tattoos = $request->tattoos ?? $user->tattoos;
            }

            if ($request->has('piercings')) {

                $user->piercings = $request->piercings ?? $user->piercings;
            }

            $user->save();

            //   user intreset
            $UserIntrestData = UserIntrest::where('user_id', $user->id)->first();

            if ($request->fav_music) {
                $UserIntrestData->fav_music = $request->fav_music ??  $UserIntrestData->fav_music;
            }

            if ($request->fav_tv_show) {
                $UserIntrestData->fav_tv_show = $request->fav_tv_show ??  $UserIntrestData->fav_tv_show;
            }

            if ($request->fav_movie) {
                $UserIntrestData->fav_movie = $request->fav_movie ??  $UserIntrestData->fav_movie;
            }


            if ($request->fav_book) {
                $UserIntrestData->fav_book = $request->fav_book ??  $UserIntrestData->fav_book;
            }


            if ($request->fav_sport) {
                $UserIntrestData->fav_sport = $request->fav_sport ??  $UserIntrestData->fav_sport;
            }

            $UserIntrestData->save();


            return response()->json([
                "status_code" => 200,
                "status" => true,
                "message" => "Data updated successfully",
                "data" => $user

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }


    //   ******************************************************edit lookingfor ********************************************************************************************
    public function editLookingFor(Request $request)
    {
        try {

            $request->validate([
                'looking_for_ids' => 'nullable|array',
            ]);

            $user = Auth::user();

            if ($request->filled('looking_for_ids')) {
                $ids = $request->looking_for_ids;
                // if (in_array(6, $ids)) {
                //     $looking_for = LookngFor::all();
                //     $ids = [];
                //     foreach ($looking_for as $item) {
                //         if ($item->id != 6) {
                //             $ids[] = $item->id;
                //         }
                //     }
                // }
                $user->looking_for = implode(',', $ids); // 34
                $user->save();
            } else {
                return response()->json([
                    "status_code" => 200,
                    "status"      => true,
                    "message"     => "No change made",
                ], 200);
            }

            return response()->json([
                "status_code" => 200,
                "status"      => true,
                "message"     => "Data saved successfully",
                "data"        => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status"  => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    // *****************************************************************************edit user image ***************************************************************************
    public function deleteUserImage(Request $request)
    {
        try {
            $request->validate([
                "image_id"   => "required_without:delete_all|exists:user_images,id",
                "delete_all" => "nullable|boolean",
            ]);


            $user = Auth::user();

            if ($request->delete_all) {

                $images = UserImage::where('user_id', $user->id)->get();

                if ($images->isEmpty()) {
                    return response()->json([
                        "status"  => false,
                        "message" => "No images found for this user",
                    ], 404);
                }

                foreach ($images as $image) {
                    $image->delete();
                }

                return response()->json([
                    "status"      => true,
                    "status_code" => 200,
                    "message"     => "All user images deleted successfully",
                ], 200);
            } elseif ($request->filled('image_id')) {

                $image = UserImage::where('id', $request->image_id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$image) {
                    return response()->json([
                        "status"  => false,
                        "message" => "Image not found or does not belong to you",
                    ], 404);
                }


                $image->delete();
                return response()->json([
                    "status"      => true,
                    "status_code" => 200,
                    "message"     => "User image deleted successfully",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status"  => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }


    // *******************************************************************store privatephoto *************************************************************************
    public function privatePhoto(Request $request)
    {
        $request->validate([
            "private_photo" => "required",
        ]);

        $user = Auth::user();
        $savedImages = [];

        if ($request->hasFile('private_photo')) {

            $images = $request->file('private_photo');

            if (!is_array($images)) {
                $images = [$images];
            }

            foreach ($images as $image) {
                $name = time() . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $path = public_path('uploads/private_image/');
                $image->move($path, $name);

                $savedImages[] = UserImage::create([
                    'user_id' => $user->id,
                    'profile_image' => 'uploads/private_image/' . $name,
                    'type' => 1,
                ]);
            }
        }

        return response()->json([
            "status" => true,
            "code" => 200,
            "message" => "Private photos uploaded successfully",
            "data" => $savedImages,
        ]);
    }




    //  ******************************************************************************admin status *********************************************************************************************
    public function adminStatus(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            "status"        => true,
            "status_code"   => 200,
            "admin_status"  => $user->admin_approved
        ], 200);
    }
    //******************************************************************************profile detail of user ***************************************************************************************************
    public function profileDetail()
    {
        try {

            $userid = Auth::id();

            $user = User::where('id', $userid)->first();

            $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

            $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

            $age = Carbon::parse($user->dob)->age;

            $looking_forids = $user->looking_for;
            // string to array via explode fn
            $lookin_for = explode(',', $looking_forids);


            $lookingforname = LookngFor::whereIn('id', $lookin_for)->pluck('looking_for');

            $latestImage = UserImage::where('user_id', $userid)
                ->where('type', 0)
                ->orderBy('created_at', 'DESC')
                ->where(function ($query) {
                    $query->where('profile_image_approval', 0)
                        ->orWhere('profile_image_approval', 2);
                })
                ->first();

            $currentImage = UserImage::where('user_id', $userid)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
            $bestImage = UserImage::select('id', 'user_id', 'profile_image as gallery_image')->where('user_id', $userid)->where('type', 3)->orderBy('created_at', 'ASC')->first();
            $profileimage = $currentImage ? $currentImage->profile_image : ($bestImage ? $bestImage->gallery_image : '');

            // fetching multiple image 
            $publicimages = UserImage::select('id', 'user_id', 'profile_image as gallery_image')
                ->where('user_id', $userid)
                ->where('type', 2)
                ->get();
            $merged = collect([]);
            if ($bestImage) {
                $merged->push($bestImage);
            }
            $merged = $merged->merge($publicimages);

            $privateImage = UserImage::select('id', 'user_id', 'profile_image as gallery_image')
                ->where('user_id', $userid)
                ->where('type', 1)
                ->get();

            $userIntrest = UserIntrest::where('user_id', $userid)->first();

            $body_type = BodyType::where('body_type', $user->body_type)->first();
            $cityData = City::where('city', $user->city)->first();
            $regionData = Region::where('region', $user->region)->first();
            $nationality = Nationality::where('nationality', $user->nationality)->first();
            $sexuallorientation = SexOrientation::where('sex_orientation', $user->sexual_orientation)->first();
            $zodiacSign = Zodiac::where('Zodiac_Signs', $user->zodiac_sign)->first();

            $children = CustomOption::where('id', $user->children)->select('id', 'value', 'name', 'parent_id')->first();
            $exercise = CustomOption::where('id', $user->exercise)->select('id', 'value', 'name', 'parent_id')->first();
            $pets = CustomOption::where('id', $user->pets)->select('id', 'value', 'name', 'parent_id')->first();
            $income_level = CustomOption::where('id', $user->income_level)->select('id', 'value', 'name', 'parent_id')->first();


            $data   = [
                "id"          => $user->id,
                "first_name"  => $user->first_name,
                "last_name"   => $user->last_name,
                "profile_name"   => $user->profile_name,
                "city"        => $cityData,
                "email" => $user->email,
                "gender" => $user->gender,
                "mobile_no" => $user->mobile_no,
                "country_code" => $user->country_code,
                "dob" => $user->dob,
                "age" => $age,
                "looking_for" => $lookingforname,
                "profile_image" => $profileimage,
                'profile_image_latest' => $latestImage ? $latestImage->profile_image : '',
                'profile_image_latest_id' => $latestImage ? (int) $latestImage->id : null,
                'profile_image_approval' => $latestImage ? (int)$latestImage->profile_image_approval : null,
                "about" => [
                    "about_me" => $user->about_me,
                    "about_match" => $user->about_match,
                ],
                "custom_options" => [
                    "children" => [
                        "selected_id" => $user->children,
                        "data" => CustomOption::where('parent_id', $children?->parent_id)->where('status', 1)->select('id', 'value', 'name')->get(),
                    ],
                    "exercise" => [
                        "selected_id" => $user->exercise,
                        "data" => CustomOption::where('parent_id', $exercise?->parent_id)->where('status', 1)->select('id', 'value', 'name')->get(),
                    ],
                    "pets" => [
                        "selected_id" => $user->pets,
                        "data" => CustomOption::where('parent_id', $pets?->parent_id)->where('status', 1)->select('id', 'value', 'name')->get(),
                    ],
                    "income_level" => [
                        "selected_id" => $user->income_level,
                        "data" => CustomOption::where('parent_id', $income_level?->parent_id)->where('status', 1)->select('id', 'value', 'name')->get(),
                    ],
                ],
                "info" => [
                    "age" => $age,
                    "height" => $user->height,
                    "weight" => $user->weight,
                    "hair_color" => $user->hair_color,
                    "eye_color" => $user->eye_color,
                    "body_type" => $body_type,
                    "nationality" => $nationality,
                    "region" => $regionData,
                    "city" => $cityData,
                    "sexual_orientation" => $sexuallorientation,
                    "education" => $user->education,
                    "field_of_work" => $user->field_of_work,
                    "relationship_status" => $user->relationship_status,
                    "zodiac_sign" => $zodiacSign,



                ],

                "personal_information" => [
                    "smoking" => $user->smoking,
                    "drinking" => $user->drinking,
                    "tattoos" => $user->tattoos,
                    "piercings" => $user->piercings,
                    "relationship_status" => $user->relationship_status
                ],
                "public_images" => $merged,

                "private_images" => $privateImage,

                "user_intrest"  => $userIntrest,
                "country_name" => $countryname



            ];
            return response()->json(['status' => true, 'status_code' => 200, 'message' => 'User Data Retrieve succesfully', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // *************************************************************************PROFILE TIMER *****************************************************************************************
    public function profileTimer()
    {
        $user = Auth::user();

        $age = Carbon::parse($user->dob)->age;

        $profiletimer = ProfileTimer::value('time');

        $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

        $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

        $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
        $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
        $profileimage = $latestimage ? $latestimage->profile_image : $bestImage;

        $useraverageRating = $user->receivedRatings->avg('points') ?? 0;
        $usertotalRatings   = $user->receivedRatings->count();

        $data = [
            'id' => $user->id,
            'name' => $user->first_name,
            'last_name' => $user->last_name,
            'profile_name' => $user->profile_name,
            'age' => $age,
            'profile_image' => $profileimage,
            'profile_image_approval' => $latestimage ? (int)$latestimage?->profile_image_approval : null,
            'email' => $user->email,
            'city' => $user->city,
            'region' => $user->region,
            'country' => $countryname,
            'height' => $user->height,
            'weight' => $user->weight,

        ];

        $now = now();



        // ratings
        $yes = UserRate::where('reciever_id', $user->id)->where('reaction', 'YES')->count();
        $ok = UserRate::where('reciever_id', $user->id)->where('reaction', 'OK')->count();
        $maybe = UserRate::where('reciever_id', $user->id)->where('reaction', 'Maybe')->count();
        $no = UserRate::where('reciever_id', $user->id)->where('reaction', 'No')->count();

        // Total points
        $abc = $yes + $ok + $maybe + $no;

        if ($abc == 0) {
            $abc = 1;
        }
        // Avoid division by zero

        $yesPct = $yes * $abc;
        $okPct = $ok * $abc;
        $maybePct = $maybe * $abc;
        $noPct = $no * $abc;



        $allRatings = UserRate::where('reciever_id', $user->id)->get();
        $totalRaters = $allRatings->count();
        $overallAvgPoints = $totalRaters > 0 ? $allRatings->sum('points') : 0;

        // managing RATING status  for in and out
        if ($user->profile_approval == 1) {
            $user->profie_rating_status = 'IN';
            $user->save();
        } else {
            $expireTime = Carbon::parse($user->admin_approve_time)->addHours((int) $profiletimer);
            $averageRating = $user->receivedRatings->avg('points') ?? 0;
            $totalRatings = $user->receivedRatings->count();
            //current time agar expiry se jada tab he rate krskta hai 
            if (now() >= $expireTime) {
                //   tab he chec kro 
                if ($averageRating > 0 && $totalRatings >= 4) {

                    $user->profie_rating_status  = 'IN';
                    $user->save();
                }
            }
        }

        $expiryTime = Carbon::parse($user->admin_approve_time)->addHours((int) $profiletimer);
        // agar timer kahatam hua toh
        if ($now->greaterThanOrEqualTo($expiryTime)) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Profile rating period expired',
                'data' => $data,
                'time_left' => '00:00:00',
                'overall_rating' => $overallAvgPoints,
                'expires_at' => $expiryTime->toDateTimeString(),
                'profile_rating_status' => $user->profie_rating_status,
                "in_out_status" => ($useraverageRating > 0 && $usertotalRatings >= 4) ? 'IN' : 'OUT',
            ]);
        }

        $timeLeft = $now->diffInSeconds($expiryTime);
        $hours = floor($timeLeft / 3600);
        $minutes = floor(($timeLeft % 3600) / 60);
        $seconds = $timeLeft % 60;

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $data,
            'message' => 'Profile is still active',
            'time_left' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
            'expires_at' => $expiryTime->toDateTimeString(),
            'profile_rating_status' => $user->profie_rating_status,
            'ratings' => [
                'yes_percentage' => $yesPct,
                'ok_percentage' => $okPct,
                'maybe_percentage' => $maybePct,
                'no_percentage' => $noPct,
                'overall_avg' => $overallAvgPoints,
            ],
            "in_out_status" => ($useraverageRating > 0 && $usertotalRatings >= 4) ? 'IN' : 'OUT',
        ]);
    }


    // ************************************************************************edit profile ***************************************************************************************************
    public function editProfileImage(request $request)
    {

        $request->validate([
            'profile_image' => 'required'
        ]);

        $user = Auth::user();

        $userimage = UserImage::where('user_id', $user->id)
            ->where('type', 0)
            ->where(function ($query) {
                $query->where('profile_image_approval', 0)
                    ->orWhere('profile_image_approval', '');
            })
            ->first();

        if ($userimage) {
            $imagePath = public_path(str_replace('public/', '', $userimage->profile_image));
            if ($userimage->profile_image && file_exists($imagePath)) {
                unlink($imagePath);
            } else {
                $userimage->delete();
                $userimage = new UserImage;
                $userimage->user_id = $user->id;
                $userimage->type = 0;
            }
        } else {
            $userimage = new UserImage;
            $userimage->user_id = $user->id;
            $userimage->type = 0;
        }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $name  = time() . '.' . $image->getClientOriginalExtension();
            $path  = public_path('uploads/users/');
            $image->move($path, $name);

            $userimage->profile_image = 'uploads/users/' . $name;
            $userimage->profile_image_approval = 0;
            $userimage->save();
        }

        return response()->json([
            'status'      => true,
            'status_code' => 200,
            'message'     => "Profile image updated successfully",
            'data'        => $userimage->profile_image
        ], 200);
    }

    // *****************************************************************************online offline status *********************************************************************
    public function onlineStatus(Request $request)
    {
        $request->validate([
            'online_status' => 'required',
        ]);

        // online,offline
        $user = Auth::user();
        $user->online_status = $request->online_status;
        $user->save();

        return response()->json([
            'status'      => true,
            'status_code' => 200,
            'user_id'     => $user->id,
            'message'     => "Status updated successfully",
        ], 200);
    }

    //************************************************************************************filter **********************************************************************
    public function filterUser(Request $request)
    {
        $authUser = Auth::user();

        // get bloked users id
        $allUserIds = blockedUsers::select('block_to as user_id')
            ->where('block_by', $authUser->id)
            ->union(
                blockedUsers::select('block_by as user_id')
                    ->where('block_to', $authUser->id)
            )
            ->pluck('user_id')
            ->toArray();


        $query = User::query()
            ->where('status', 1)
            ->whereNotIn('id', $allUserIds)
            ->where('admin_status', 1)
            ->where('rejection_email_status', 0);

        //  Gender filter (opposite gender)
        if (!empty($authUser->gender)) {
            $oppositeGender = $authUser->gender == 'Male' ? 'Female' : 'Male';
            $query->where('gender', $oppositeGender);
        }

        // Search by profile name
        $searchKey = $request->key;

        if ($searchKey != '') {
            $query->where('profile_name', 'LIKE', '%' . $searchKey . '%');

            //  Cache key only for search keywords
            $cacheKey = 'recent_search_keywords_user_' . $authUser->id;
            $recentSearches = Cache::get($cacheKey, []);
            $recentSearches = array_filter($recentSearches, 'is_string');

            $recentSearches = array_values(array_diff($recentSearches, [$searchKey]));
            array_unshift($recentSearches, $searchKey);

            $recentSearches = array_slice($recentSearches, 0, 10);
            Cache::put($cacheKey, array_values($recentSearches), now()->addYear());
        }

        //  Online status filter
        if (!empty($request->online_status)) {
            if ($request->online_status != 'Both') {
                $query->where('online_status', $request->online_status);
            }
        }

        // Region filter
        if (!empty($request->region)) {
            $query->whereIn('region', $request->region);
        }

        // Looking for filter
        if (!empty($request->looking_for)) {
            $ids = $request->looking_for;
            $query->where(function ($q) use ($ids) {
                foreach ($ids as $id) {
                    $q->orWhereRaw("FIND_IN_SET(?, looking_for)", [$id]);
                }
            });
        }

        //  Age range filter
        if (
            !empty($request->start_age) &&
            !empty($request->end_age) &&
            strtolower($request->start_age) != 'all' &&
            strtolower($request->end_age) != 'all'
        ) {
            $startDate = now()->subYears($request->end_age)->startOfDay();
            $endDate = now()->subYears($request->start_age)->endOfDay();
            $query->whereBetween('dob', [$startDate, $endDate]);
        }

        //  Fetch filtered users
        $users = $query->with(['images' => function ($q) {
            $q->where('type', 0)
                ->select('id', 'user_id', 'profile_image', 'type');
        }])->get();


        $userRatings = UserRate::where('sender_id', $authUser->id)
            ->pluck('reaction', 'reciever_id');


        //  Transform user data
        $users->transform(function ($user) use ($userRatings) {
            $currentImage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
            $bestImage = UserImage::select('id', 'user_id', 'profile_image as gallery_image')->where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->first();
            $profileimage = $currentImage ? $currentImage->profile_image : ($bestImage ? $bestImage->gallery_image : '');

            $user->best_image = $profileimage ?? null;
            $user->age = Carbon::parse($user->dob)->age;
            $countryid = DB::table('region')->where('region', $user->region)->value('country_id');
            $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');
            $user->rating_reaction = $userRatings[$user->id] ?? null;
            $user->country = $countryname;

            $user->profie_rating_status = $user->profile_approval == 0 ? 'OUT' : "IN";

            unset($user->images);
            return $user;
        });


        //  Get cached keywords
        $cacheKey = 'recent_search_keywords_user_' . $authUser->id;
        $recentKeywords = Cache::get($cacheKey, []);
        $recentKeywords = array_filter($recentKeywords, 'is_string');

        //  Get full user data for each recent search
        $recentSearchDetails = [];

        if (!empty($recentKeywords)) {
            $recentSearchDetails = User::where('status', 1)->whereIn('profile_name', $recentKeywords)
                ->with(['images' => function ($q) {
                    $q->where('type', 0)->select('id', 'user_id', 'profile_image', 'type');
                }])
                ->get()
                ->map(function ($user) use ($userRatings) {
                    $currentImage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage = UserImage::select('id', 'user_id', 'profile_image as gallery_image')->where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->first();
                    $profileimage = $currentImage ? $currentImage->profile_image : ($bestImage ? $bestImage->gallery_image : '');

                    $user->best_image = $profileimage ?? null;
                    $user->age = Carbon::parse($user->dob)->age;
                    $user->rating_reaction = $userRatings[$user->id] ?? null;
                    $countryid = DB::table('region')->where('region', $user->region)->value('country_id');
                    $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');
                    $user->rating_reaction = $userRatings[$user->id] ?? null;
                    $user->country = $countryname;

                    unset($user->images);
                    return $user;
                });
        }

        //  Static data
        $region = Region::all();
        $looking_for = LookngFor::all();


        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $users, // search result
            'recent_search_details' => $recentSearchDetails, // full user data for each keyword
            'region' => $region,
            'looking_for' => $looking_for,
            // 'recent_searches' => array_values($recentKeywords), // just keywords
        ]);
    }

    // **************************************************************************IMAGE DELETE ********************************************************
    public function imageDelete(Request $request)
    {
        $request->validate([
            'image_id' => 'required|exists:user_images,id'
        ]);

        $image = UserImage::find($request->image_id);

        if ($image) {

            if (file_exists(public_path($image->profile_image))) {
                unlink(public_path($image->profile_image));
            }

            $image->delete();

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => "Image deleted successfully",
            ]);
        }

        return response()->json([
            'code' => 404,
            'status' => false,
            'message' => "Image not found",
        ]);
    }

    // **************************************************************************clear recent search *****************************************************
    public function clearRecentSearches()
    {
        $authUser = Auth::user();

        $cacheKey = 'recent_search_keywords_user_' . $authUser->id;


        Cache::forget($cacheKey);

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Recent searches cleared successfully.',
        ]);
    }

    // ****************************************************************************Notification list *******************************************************
    public function NotificationList()
    {
        $user = Auth::user();
        $data = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $sender = User::find($item->sender_id);
                if ($sender) {
                    $latestimage = UserImage::where('user_id', $sender->sender_id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage = UserImage::where('user_id', $sender->sender_id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $profileimage = $latestimage ? $latestimage->profile_image : $bestImage;

                    $item->sender_name = $sender->profile_name ?? '';
                    $item->profile_image = $profileimage ?? '';
                } else {
                    $item->sender_name = '';
                    $item->profile_image = '';
                }

                if ($item->notification_type == "1") {
                    $private_access_request = RequestModel::where('request_to', $item->user_id)
                        ->where('request_from', $item->sender_id)
                        ->where('status', 'pending')
                        ->count();

                    $item->request_status = $private_access_request > 0 ? 1 : 0;
                } else {
                    $item->request_status = 0;
                }

                return $item;
            });

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Notification Fetch Sucessfully',
            'data' => $data
        ]);
    }


    public function ChildrenList()
    {
        $user = Auth::user();
        $childrens = Children::where('status', 1)->get();
        if (count($childrens) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Childrens Fetch Sucessfully',
                'data' => $childrens
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Childrens not found.',
                'data' => null
            ]);
        }
    }


    public function CustomOptions(Request $request)
    {
        $lang = $request->lang ?? 'da';
        $user = Auth::user();
        $customOptions = CustomOption::select('id', 'value', 'name', 'deleted_at')->whereNull('deleted_at')->where('status', 1)->get();
        foreach ($customOptions as $opiotn) {
            $transform = Translation::where('translatable_type', CustomOption::class)->where('language_code', $lang)->where('translatable_id', $opiotn->id)->where('field', $opiotn->name)->first();
            if ($transform) {
                $opiotn->value = $transform->value;
            }
        }



        $result = $customOptions
            ->groupBy('name')
            ->map(function ($items) {
                return $items->values();
            });

        if (count($result) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Childrens Fetch Sucessfully',
                'data' => $result
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Childrens not found.',
                'data' => null
            ]);
        }
    }


    // ****************************************************************************change language *********************************************************
    public function ChangeLanguage(request $request)
    {

        $request->validate([
            'language' => 'required'
        ]);

        $user = Auth::user();
        $user->language = $request->language;
        $user->save();

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Language set succesfully ',

        ]);
    }


    // delete rejected profile image
    public function deleteRejectedProfileImage(Request $request)
    {
        try {
            $image = UserImage::where('id', $request->id)->first();

            if (!$image) {
                return response()->json([
                    'status' => false,
                    'message' => 'Image not found'
                ], 404);
            }

            if ($image->profile_image) {
                $filePath = public_path(str_replace('public/', '', $image->profile_image));
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $image->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image has been deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    // delete notification 
    public function deleteNotifications(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (!$ids) {
                return response()->json(['status' => false, 'message' => 'No IDs provided']);
            }
            $idArray = array_map('trim', explode(',', $ids));

            Notification::whereIn('id', $idArray)->delete();

            return response()->json(['status' => true, 'message' => 'Notifications deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // Delete Account
    public function deleteAccount(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string'
            ]);

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'status_code' => 404,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if account is already deleted
            if ($user->deleted_at) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'Account is already deleted'
                ], 400);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'status_code' => 401,
                    'message' => 'Incorrect password'
                ], 401);
            }

            // Perform soft delete
            $user->deleted_at = Carbon::now();
            $user->deleted_by = 'user';
            $user->deletion_type = 'user_deleted';
            $user->status = 0; // Block login
            $user->save();

            // Revoke all Sanctum tokens
            $user->tokens()->delete();

            // Delete all device info
            DeviceInfo::where('user_id', $user->id)->delete();

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Account deleted successfully'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'status_code' => 422,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
