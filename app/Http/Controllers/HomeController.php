<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{User, LookngFor, UserRate, UserStatus, UserImage, ViewProfile, RequestModel, SeenStatus, Notification, UserReport, blockedUsers, RateUserProfile, ProfileTimer};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotificationHelper;

class HomeController extends Controller
{

    public function home()
    {
        try {

            $user  = Auth::user();
            $profiletimer = ProfileTimer::value('time');

            $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
            $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'DESC')->value('profile_image');
            $userprofile = $latestimage ? $latestimage->profile_image : $bestImage;
            // Get opposite gender
            $oppositeGender = $user->gender === 'Male' ? 'Female' : 'Male';


            // managing RATING status 
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

            // get bloked users id
            $allUserIds = blockedUsers::select('block_to as user_id')
                ->where('block_by', $user->id)
                ->union(
                    blockedUsers::select('block_by as user_id')
                        ->where('block_to', $user->id)
                )
                ->pluck('user_id')
                ->toArray();

            //fetching beautifull profiless
            $users = User::select('id', 'first_name', 'profile_name', 'region', 'dob', 'gender', 'admin_status', 'profile_approval', 'admin_approve_time', 'weight', 'height', 'city', 'profie_rating_status')
                ->with(['receivedRatings', 'images' => function ($query) {
                    $query->where('type', 0)
                        ->select('id', 'user_id', 'type', 'profile_image', 'rating');
                }])
                ->whereNotIn('id', $allUserIds)
                ->where('gender', $oppositeGender)
                ->orderBy('created_at', 'desc')
                ->get();

            // filtering the data 
            $newlyBeautiful = $users->filter(function ($user) use ($profiletimer) {
                if ($user->profile_approval == 1) {
                    return true;
                }

                // Check if admin approval time + 24 hours has expired
                if ($user->admin_approve_time) {
                    $expireTime = Carbon::parse($user->admin_approve_time)->addHours((int) $profiletimer);
                    // If current time is greater than expire time TABHI RATING CHECK KRO
                    if (now()->greaterThan($expireTime)) {

                        $votes = $user->receivedRatings->count();
                        $avgPoints = $user->receivedRatings->avg('points') ?? 0;
                        return $votes >= 4 && $avgPoints > 0;
                    } else {
                        return false;
                    }
                }
            });

            foreach ($newlyBeautiful as $user) {
                //country
                $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

                $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                $user->country = $countryname;
                $user->age = Carbon::parse($user->dob)->age;

                $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                $userImage = $latestimage ? $latestimage->profile_image : $bestImage;

                $user->profile_image = $userImage ?? null;
                unset($user->images);
            }

            $newlyBeautiful = $newlyBeautiful->take(10)->values();

            // user rated to new applicant
            $rated_usersID = UserRate::where('sender_id', auth()->user()->id)->pluck('reciever_id');

            //new applicants
            $newapplicant = User::select('*')
                ->where('gender', $oppositeGender)
                ->where('admin_status', 1)
                ->where('profile_approval', 0)
                ->where('rejection_email_status', 0)
                ->whereNotIn('id', $rated_usersID)
                ->with(['images' => function ($query) {
                    $query->where('type', 0)
                        ->select('id', 'user_id', 'type', 'profile_image', 'rating');
                }])
                ->whereNotIn('id', $allUserIds)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $validApplicants = [];
            $authId = Auth::id();

            $userRatings = UserRate::where('sender_id', $authId)->pluck('reaction', 'reciever_id');

            foreach ($newapplicant as $user) {
                $expireTime = Carbon::parse($user->admin_approve_time)->addHours((int) $profiletimer);
                //country
                $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

                $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                if (now() <= $expireTime) {
                    $user->age = Carbon::parse($user->dob)->age;
                    $latestimage1 = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage1 = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $userImage1 = $latestimage1 ? $latestimage1->profile_image : $bestImage1;

                    $user->profile_image = $userImage1 ?? null;

                    $user->rating_reaction = $userRatings[$user->id] ?? null;
                    // country name 
                    $user->country = $countryname ?? null;
                    unset($user->images);

                    $validApplicants[] = $user;
                }
            }

            // Convert array to collection for sorting
            $validApplicants = collect($validApplicants)
                ->sortBy(function ($user) {
                    // null reactions (no rating) should come first
                    return $user->rating_reaction !== null;
                })
                ->values();

            // Optional: convert back to array if you need plain array
            $validApplicants = $validApplicants->all();

            // helper fn for rest of array data
            function appendUserDetails($users)
            {
                foreach ($users as $user) {
                    // country name 
                    $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

                    $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                    $user->age = Carbon::parse($user->dob)->age;
                    // $userImage = UserImage::where('user_id', $user->id)
                    //     ->where('type', 0)
                    //     ->value('profile_image'); 

                    $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $userImage = $latestimage ? $latestimage->profile_image : $bestImage;

                    $user->profile_image = $userImage;
                    $user->profile_image_approval = $latestimage ? $latestimage->profile_image_approval : '';
                    $user->country = $countryname;
                }
                return $users;
            }

            // Popular Members
            $popularMembers = User::select('id', 'first_name', 'profile_name', 'region', 'dob', 'gender', 'weight', 'height', 'city', 'profile_approval', 'profie_rating_status')
                ->where('admin_status', 1)
                ->where('status', 1)
                ->where('profile_approval', 1)
                ->where('profie_rating_status', 'IN')
                ->where('gender', $oppositeGender)
                ->whereNotIn('id', $allUserIds)
                ->whereRaw('(SELECT ROUND(AVG(rating), 0) FROM rate_user_profile WHERE rated_to = users.id) >= 3')
                ->take(10)
                ->get();

            $popularMembers = appendUserDetails($popularMembers);


            // Online Members
            $onlineMembers = User::select('id', 'first_name', 'profile_name',  'region', 'dob', 'gender', 'weight', 'height', 'city', 'online_status', 'profile_approval', 'profie_rating_status')
                ->where('admin_status', 1)
                ->where('status', 1)
                ->where('profile_approval', 1)
                ->where('profie_rating_status', 'IN')
                ->where('gender', $oppositeGender)
                ->where('rejection_email_status', 0)
                ->where('online_status', 'online')
                ->whereNotIn('id', $allUserIds)
                ->take(10)
                ->get();

            $onlineMembers = appendUserDetails($onlineMembers);

            // profile who view me
            $profileViewed = ViewProfile::where('view_id', Auth::user()->id)
                ->pluck('viewer_id');

            $profileview = User::whereIn('id', $profileViewed)
                ->where('admin_status', 1)
                ->where('gender', $oppositeGender)
                ->where('status', 1)
                ->whereNotIn('id', $allUserIds)
                ->take(10)
                ->get(['id', 'first_name', 'profile_name', 'dob', 'city', 'height', 'weight', 'region', 'profie_rating_status']);

            $profileViewedMembers = appendUserDetails($profileview);

            // status section
            $user = Auth::user();

            $myStatus = UserStatus::with('user:id,profile_image,first_name,gender')
                ->where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($status) {
                    return [
                        'id' => $status->id,
                        'user_id' => $status->user_id,
                        'post' => $status->post,
                        'image' => $status->image,
                        'status' => $status->status,
                        'created_at' => $status->created_at,
                        'updated_at' => $status->updated_at,
                        'deleted_at' => $status->deleted_at,
                    ];
                });


            //user status ie is dusro ke status 
            $userStatuses = UserStatus::with([
                'user.images' => function ($q) {
                    $q->where('type', 0);
                },
                'user:id,first_name,gender,profile_name'
            ])
                ->whereHas('user', function ($q) use ($oppositeGender) {
                    $q->where('gender', $oppositeGender);
                })
                ->whereNotIn('user_id', $allUserIds)
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('user_id')
                ->map(function ($statuses, $userId) {
                    $user = $statuses->first()->user;
                    if (!$user) return null;
                    $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $profileimage = $latestimage ? $latestimage->profile_image : $bestImage;

                    // get type=0 image from relation jo best image hai user_image me
                    $profileImage = $profileimage ?? null;

                    return [
                        'user' => [
                            'id' => $user->id,
                            'profile_image' => $profileImage,
                            'first_name' => $user->first_name,
                            'gender' => $user->gender,
                            'profile_name' => $user->profile_name,
                        ],
                        'stories' => $statuses->map(function ($status) {
                            $authId = Auth::id();

                            $isSeen = SeenStatus::where('status_view_by_id', $authId)
                                ->where('status_id', $status->id)
                                ->where('status_view_of_id', $status->user_id)
                                ->value('is_seen');

                            return [
                                'id' => $status->id,
                                'user_id' => $status->user_id,
                                'post' => $status->post,
                                'image' => $status->image,
                                'status' => $isSeen,
                                'created_at' => $status->created_at,
                                'updated_at' => $status->updated_at,
                                'deleted_at' => $status->deleted_at,
                            ];
                        })->values(),
                    ];
                })
                ->filter()
                ->values();

            $data = [
                'user_image'              => $userprofile,
                'user_gender'             => $user->gender,
                'rating_status'           => $user->profie_rating_status,
                'newly_beautiful_members' => $newlyBeautiful,
                'new_applicants'          => $validApplicants,
                'popular_members'         => $popularMembers,
                'online_members'          => $onlineMembers,
                'profile_viewed_members'  => $profileViewedMembers,
                'user_status'             => $userStatuses,
                'my_status'               => $myStatus,
                'sender_id'               => (string) $user->id
            ];

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // ******************************************************************************home list *******************************************************************************

    public function homeList(Request $request)
    {
        $request->validate(['key' => 'required']);
        $user = Auth::user();
        $profiletimer = ProfileTimer::value('time');

        $oppositeGender = $user->gender === 'Male' ? 'Female' : 'Male';
        // get bloked users id
        $allUserIds = blockedUsers::select('block_to as user_id')
            ->where('block_by', $user->id)
            ->union(
                blockedUsers::select('block_by as user_id')
                    ->where('block_to', $user->id)
            )
            ->pluck('user_id')
            ->toArray();

        // managing status 
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

        // beautyfull  members
        if ($request->key == "beautifull_member") {

            $allUsers = User::select('*')
                ->with(['receivedRatings', 'images' => function ($query) {
                    $query->where('type', 0)
                        ->select('id', 'user_id', 'type', 'profile_image', 'rating');
                }])
                ->whereNotIn('id', $allUserIds)
                ->where('gender', $oppositeGender)
                ->orderBy('created_at', 'desc')
                ->get();

            // Filter logic (same as before)
            $filtered = $allUsers->filter(function ($user)  use ($profiletimer) {
                if ($user->profile_approval == 1) {
                    return true;
                }

                if ($user->admin_approve_time) {
                    $expireTime = Carbon::parse($user->admin_approve_time)->addHours((int) $profiletimer);

                    if (now()->greaterThan($expireTime)) {
                        $votes = $user->receivedRatings->count();
                        $avgPoints = $user->receivedRatings->avg('points') ?? 0;
                        return $votes >= 4 && $avgPoints > 0;
                    }
                }

                return false;
            });

            // Transform filtered users
            $filtered = $filtered->map(function ($user) {
                $countryid = DB::table('region')->where('region', $user->region)->value('country_id');
                $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                $user->country = $countryname;
                $user->age = Carbon::parse($user->dob)->age;

                $latestimage2 = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                $bestImage2 = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                $userImage2 = $latestimage2 ? $latestimage2->profile_image : $bestImage2;

                $user->user_profile_image = $userImage2 ?? null;

                unset($user->images, $user->receivedRatings);
                return $user;
            });

            // Apply manual pagination after filtering
            $page = request('page', 1);
            $perPage = 10;
            $total = $filtered->count();
            $pagedData = $filtered->forPage($page, $perPage)->values();

            //Return response
            return response()->json([
                'status' => true,
                'status_code' => 200,
                'total_page' => ceil($total / $perPage),
                'message' => 'Data retrieved successfully',
                'data' => $pagedData,
            ]);
        }

        // new user
        if ($request->key == "new_applicant") {
            $newapplicant =    User::select('*')
                ->where('gender', $oppositeGender)
                ->where('admin_status', 1)
                ->whereNotIn('id', $allUserIds)
                ->where('profile_approval', 0)
                ->where('gender', $oppositeGender)
                ->with(['images' => function ($query) {
                    $query->where('type', 0)
                        ->select('id', 'user_id', 'type', 'profile_image', 'rating');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $validApplicants = [];

            $authId = Auth::id();

            $userRatings = UserRate::where('sender_id', $authId)
                ->pluck('reaction', 'reciever_id');

            foreach ($newapplicant as $user) {
                $expireTime = Carbon::parse($user->admin_approve_time)->addHours((int) $profiletimer);

                if (now() <= $expireTime) {
                    $user->age = Carbon::parse($user->dob)->age;

                    $latestimage0 = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage0 = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $userImage0 = $latestimage0 ? $latestimage0->profile_image : $bestImage0;

                    $user->user_profile_image = $userImage0 ?? null;

                    // for country
                    $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

                    $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                    $user->country = $countryname;


                    $user->rating_reaction = $userRatings[$user->id] ?? null;

                    unset($user->images);

                    $validApplicants[] = $user;
                }
            }

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Data retrieved successfully',
                'profile_rating_status' => $user->profie_rating_status,
                'data' => $validApplicants
            ]);
        }



        // helper fn
        function appendProfileImageAndAge($users)
        {
            foreach ($users as $user) {
                // Calculate age
                $user->age = Carbon::parse($user->dob)->age;
                // Fetch profile image from user_images table (type=0)
                $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                $userImage = $latestimage ? $latestimage->profile_image : $bestImage;

                $user->user_profile_image = $userImage ?? null;

                // for country
                $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

                $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                $user->country = $countryname;
                // Remove images relation for clean JSON
                unset($user->images);
            }
            return $users;
        }


        // Popular Members
        if ($request->key == "popular_member") {

            $popularMembers = User::with(['images' => function ($query) {
                $query->where('type', 0);
            }])
                ->where('admin_status', 1)
                ->where('status', 1)
                ->where('profile_approval', 1)
                ->where('profie_rating_status', 'IN')
                ->where('gender', $oppositeGender)
                ->whereNotIn('id', $allUserIds)
                ->whereRaw('(SELECT ROUND(AVG(rating), 0) FROM rate_user_profile WHERE rated_to = users.id) >= 3')
                ->paginate(10);

            $popularMembers = appendProfileImageAndAge($popularMembers);

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Data retrieved successfully',
                "total_page" => $popularMembers->lastPage(),
                'profile_rating_status' => $user->profie_rating_status,
                'data' => $popularMembers->items(),

            ]);
        }


        // Online Members
        elseif ($request->key == "online_member") {
            $onlineMembers = User::where('admin_status', 1)
                ->where('status', 1)
                ->where('gender', $oppositeGender)
                ->where('profie_rating_status', 'IN')
                ->whereNotIn('id', $allUserIds)
                ->where('online_status', 'online')
                ->with(['images' => function ($query) {
                    $query->where('type', 0); // profile images only
                }])
                ->paginate(10);

            $onlineMembers = appendProfileImageAndAge($onlineMembers);

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Data retrieved successfully',
                'profile_rating_status' => $user->profie_rating_status,
                'data' => $onlineMembers->items(),
                "total_page" => $onlineMembers->lastPage(),
            ]);
        }


        // Profile Viewed Members
        elseif ($request->key == "profile_view") {

            // IDs of users who viewed my profile
            $profileViewed = ViewProfile::where('view_id', Auth::User()->id)
                ->pluck('viewer_id');


            $users = User::whereIn('id', $profileViewed)
                ->where('admin_status', 1)
                ->where('status', 1)
                ->whereNotIn('id', $allUserIds)
                ->with(['images' => function ($query) {
                    $query->where('type', 0);
                }])
                ->paginate(10, ['id', 'profile_name', 'dob', 'city', 'height', 'weight', 'region', 'profie_rating_status']);;

            // Append profile image & age
            $profileViewedMembers = appendProfileImageAndAge($users);

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Data retrieved successfully',
                'profile_rating_status' => $user->profie_rating_status,
                'data' => $profileViewedMembers->items(),
                "total_page" => $profileViewedMembers->lastPage(),
            ]);
        }
    }


    // ****************************view detail *********************
    public function viewDetail(request $request)
    {
        try {

            $userid = $request->id;
            $latestimage2 = UserImage::where('user_id', $userid)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
            $bestImage2 = UserImage::where('user_id', $userid)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
            $userImage2 = $latestimage2 ? $latestimage2->profile_image : $bestImage2;

            $user = User::where('id', $userid)->first();

            $age = Carbon::parse($user->dob)->age;

            $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

            $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

            $looking_forids = $user->looking_for;
            // string to array via explode fn
            $lookin_for = explode(',', $looking_forids);


            $lookingforname = LookngFor::whereIn('id', $lookin_for)
                ->pluck('looking_for');


            $latestimagePublic = UserImage::where('user_id', $userid)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
            $bestImagePublic = UserImage::where('user_id', $userid)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
            $userImagePublic = $latestimagePublic ? $latestimagePublic->profile_image : $bestImagePublic;
            // fetching multiple image 
            $public_images = $userImagePublic;


            // fetching multiple image 
            $bestimages = UserImage::select('id', 'user_id', 'profile_image as gallery_image')
                ->where('user_id', $userid)
                ->where('type', 0)
                ->get();



            $privateImage = UserImage::select('id', 'user_id', 'profile_image as gallery_image')
                ->where('user_id', $userid)
                ->where('type', 1)
                ->get();


            $name = $user->profile_name;

            // rating ka funda
            $yesRatingAvg = UserRate::where('reciever_id', $userid)->where('reaction', 'YES')->count('points') ?? 0;
            $okRatingAvg = UserRate::where('reciever_id', $userid)->where('reaction', 'OK')->count('points') ?? 0;
            $maybeRatingAvg = UserRate::where('reciever_id', $userid)->where('reaction', 'Maybe')->count('points') ?? 0;
            $noRatingAvg = UserRate::where('reciever_id', $userid)->where('reaction', 'No')->count('points') ?? 0;
            $allRatings = UserRate::where('reciever_id', $userid)->get();


            // similar profile 
            $authuser = Auth::user();

            // Get opposite gender
            $oppositeGender = $authuser->gender === 'Male' ? 'Female' : 'Male';

            $rated_usersID = UserRate::where('sender_id', auth()->user()->id)->pluck('reciever_id');

            $similar_profile = User::where('id', '!=', $userid)
                ->whereNotIn('id', $rated_usersID)
                ->where('rejection_email_status', 0)
                ->where('admin_status', 1)
                ->where('admin_approve_time', '!=', '')
                ->where('profile_approval', 0)
                ->where('gender', $oppositeGender)
                ->get();


            $similar_profile->transform(function ($user) use ($authuser) {
                $latestimage = UserImage::where('user_id', $user->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                $bestImage = UserImage::where('user_id', $user->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                $userImage = $latestimage ? $latestimage->profile_image : $bestImage;
                $user->profile_image = $userImage ?? null;
                $user->age = Carbon::parse($user->dob)->age;
                $user_rate = UserRate::where('sender_id', $authuser->id)->where('reciever_id', $user->id)->orderBy('created_at', "DESC")->first();
                $user->rating_reaction = $user_rate ? $user_rate->reaction : null;
                $user->profie_rating_status = $user->profile_approval == 0 ? 'OUT' : "IN";
                unset($user->images);
                return $user;
            });


            // view profile datat store
            $viewData = new ViewProfile;
            $viewData->view_id = $request->id;
            $viewData->viewer_id = $authuser->id;
            $viewData->save();


            $ratingreaction = UserRate::where('sender_id', $authuser->id)->where('reciever_id', $userid)->value('reaction');



            // request sended to user or not to acces private image 
            $requestSent = RequestModel::where('request_to', $user->id)
                ->where('request_from', $authuser->id)
                ->exists();

            if ($requestSent) {
                $response = 'yes';
            } else {
                $response = 'no';
            }

            // accept request status to shown private image 
            $acceptedReq = RequestModel::where('request_to', $user->id)
                ->where('request_from', $authuser->id)
                ->where('status', 'confirm')
                ->first();

            $averageRating = $user->receivedRatings->avg('points') ?? 0;
            $totalRatings = $user->receivedRatings->count();
            $currentImagesender = UserImage::where('user_id', Auth::user()->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
            $bestImagesender = UserImage::select('id', 'user_id', 'profile_image as gallery_image')->where('user_id', Auth::user()->id)->where('type', 3)->orderBy('created_at', 'ASC')->first();
            $profileimageSender = $currentImagesender ? $currentImagesender->profile_image : ($bestImagesender ? $bestImagesender->gallery_image : '');

            $sender_image = $profileimageSender;

            // check block status 
            $block_status = blockedUsers::where('block_by', $authuser->id)->where('block_to', $user->id)->count();

            // profile rating 
            $rate_user_profile = RateUserProfile::where('rated_to', $userid)->avg('rating');
            $rated_user = RateUserProfile::where(['rated_to' => $userid, 'user_id' => $authuser->id])->count();

            $data = [
                "sender_id"   => $authuser->id,
                "block_status"   => $block_status > 0 ? 1 : 0,
                "id"          => $user->id,
                "first_name"  => $user->first_name,
                "last_name"   => $user->last_name,
                "city"        => $user->city,
                "country"     => $countryname,
                "rating_reaction" => $ratingreaction,
                "age" => $age,
                "user_image" => $userImage2,
                "looking_for" => $lookingforname,
                "rating_status" => $authuser->profie_rating_status,
                "user_rating_status" => $user->profie_rating_status,
                "user_profile_rating" => $rate_user_profile ? (string) round($rate_user_profile, 1) : '',
                "user_rated" => $rated_user > 0 ? 1 : 0,
                "about" => [
                    "about_me" => $user->about_me,
                    "about_match" => $user->about_match,
                ],
                "info" => [
                    "profile_name" => $user->profile_name,
                    "age" => $age,
                    "height" => $user->height,
                    "weight" => $user->weight,
                    "hair_color" => $user->hair_color,
                    "eye_color" => $user->eye_color,
                    "body_type" => $user->body_type,
                    "nationality" => $user->nationality,
                    "city" => $user->city,
                    "sexual_orientation" => $user->sexual_orientation,
                    "education" => $user->education,
                    "field_of_work" => $user->field_of_work,
                    "relationship_status" => $user->relationship_status,
                    "zodiac_sign" => $user->zodiac_sign,
                    "admin_approve_time" => $user->admin_approve_time,
                ],
                "personal_information" => [
                    "smoking" => $user->smoking,
                    "drinking" => $user->drinking,
                    "tattoos" => $user->tattoos,
                    "piercings" => $user->piercings,
                    "relationship_status" => $user->relationship_status
                ],
                'ratings' => [
                    'hot' => round($yesRatingAvg, 2),
                    'ok_avg' => round($okRatingAvg, 2),
                    'maybe_avg' => round($maybeRatingAvg, 2),
                    'no_avg' => round($noRatingAvg, 2),
                    'overall_avg' => 2
                ],
                "public_images" => $public_images,
                "private_images" => $privateImage,
                "similar_profile" => $similar_profile,
                "requested" => $response,
                "like_status" => $user->like_status,
                "add_to_fav" => $user->fav_user_status,
                "private_photo_status" => $acceptedReq ? "true" : "false",
                "sender_id" => (string) $authuser->id,
                "sender_name" => $authuser->profile_name,
                "sender_image" => $sender_image,
                "auth_user_profile_approval" => $authuser->profile_approval,
                "in_out_status" => ($averageRating > 0 && $totalRatings >= 4) ? 'IN' : 'OUT',
            ];


            return response()->json(['status' => true, 'status_code' => 200, 'message' => 'User Data Retrieve succesfully', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }
    // ***********************************************************************user rate ********************************************************************************

    public function userRate(Request $request)
    {
        $request->validate([
            'reciever_id' => 'required',
            'reaction' => 'required|in:YES,OK,Maybe,No'
        ]);


        $user = Auth::user();

        $pointsMap = [
            'YES' => +2,
            'OK' => +1,
            'Maybe' => 0,
            'No' => -3,
        ];

        $reaction = $request->reaction;
        $points = $pointsMap[$reaction];

        $rate = UserRate::create([
            'sender_id' => (string) $user->id,
            'reciever_id' => (string) $request->reciever_id,
            'reaction' => $reaction,
            'points' => $points,
        ]);

        // notifucation
        NotificationHelper::sendToUser(
            $request->reciever_id,
            'User Rated',
            $user->profile_name . ' has rated your profile with reaction: ' . $reaction,
            $user->id
        );



        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'User rated successfully',
            'data' => $rate
        ]);
    }


    // *****************************************************************************user status ************************************************************************************

    public function userStory(Request $request)
    {
        $request->validate([
            'story' => 'nullable|string',
            'image' => 'nullable'
        ]);


        if (!$request->story && !$request->hasFile('image')) {
            return response()->json([
                'status' => false,
                'message' => 'You must upload a story text or an image.',
            ], 422);
        }

        $user = Auth::user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $name = time() . '.' . $img->getClientOriginalExtension();
            $path = public_path('uploads/story_image');

            // move file
            $img->move($path, $name);

            // store relative path for DB
            $imagePath = 'public/uploads/story_image/' . $name;
        }

        $status = UserStatus::create([
            'user_id' => $user->id,
            'post' => $request->story,
            'image' => $imagePath,
            'status' => 'unseen',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Your Shout-out was posted succesfully',
            'data' => $status,
        ], 200);
    }

    //******************************************************************update story status ie seen and unseen ***********************************************************************************  
    public function updateStoryStatus(Request $request)
    {
        $request->validate([

            'id'     => 'required',
        ]);

        $authuser = Auth::User();

        $user_status_id = UserStatus::where('id', $request->id)->value('user_id');
        $data  = new SeenStatus();
        $data->status_view_by_id = $authuser->id;
        $data->status_view_of_id = $user_status_id;
        $data->status_id         = $request->id;
        $data->is_seen           = "seen";

        $data->save();

        return response()->json([
            'status' => true,
            'message' => 'Story status updated successfully',

        ]);
    }
}
