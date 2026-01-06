<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use  App\Models\{
    User,
    HearAboutUs,
    LookngFor,
    UserImage,
    UserIntrest,
    CountryCode,
    Nationality,
    Region,
    City,
    BodyType,
    SexOrientation,
    Zodiac,
    RequestModel,
    UserLike,
    FavouriteUser,
    ReportReason,
    ImageRating,
    ReportUser,
    SupportRequest,
    blockedUsers,
    RateUserProfile,
    UserSuggestion
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{

    // ***********************************************selfie  image for user ************************************************************************************
    public function profileImage(request $request)
    {
        try {

            $rule = [
                'profile_image' => 'required',

            ];

            $validator = Validator::make($request->all(), $rule);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
            }


            $user = Auth::User();

            if ($request->has('profile_image')) {
                $image = $request->profile_image;
                $name  = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path('uploads/users/');
                $image->move($path, $name);
                $user->profile_image = 'public/uploads/users/' . $name;
                $user->save();
            };
            return response()->json(['status' => true, 'status_code' => 200, 'message' => "Selfie uploded succesfully", 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ***********************************************************USER Image *************************************************************************
    public function userImage(Request $request)
    {
        try {
            $rule = [
                'user_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            ];

            $validator = Validator::make($request->all(), $rule);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = Auth::user();

            if ($request->hasFile('user_image')) {
                $image = $request->file('user_image');
                $name  = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = public_path('uploads/public_image/');

                // Upload new image
                $image->move($path, $name);

                // Check existing image (user_id + type = 3)
                $existing = UserImage::where('user_id', $user->id)
                    ->where('type', 3)
                    ->first();

                // Delete old file if exists
                if ($existing && $existing->profile_image) {
                    $oldPath = public_path(str_replace('public/', '', $existing->profile_image));
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Update or create new record
                $savedImage = UserImage::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'type' => 3
                    ],
                    [
                        'profile_image' => 'public/uploads/public_image/' . $name
                    ]
                );

                return response()->json([
                    'status' => true,
                    'status_code' => 200,
                    'message' => "Your best image is uploaded succesfully",
                    'data' => $savedImage
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'No image found in request'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }




    // ******************************************************additional detail ****************************************************************************
    public function additionalDetail(request $request)
    {

        try {

            $rules = [
                "dob" => "required",
                "height" => "required",
                "weight" => "required",
                "body_type" => "required",
                "hair_color" => "required",
                "eye_color" => "required",
                "nationality" => "required",
                "region" => "required",
                "city" => "required",
                "sexual_orientation" => "required",
                "education" => "required",
                "field_of_work" => "required",
                "relationship_status" => "required",
                "zodiac_sign" => "required",
                "smoking" => "required",
                "drinking" => "required",
                "tattoos" => "required",
                "piercings" => "required",
                "about_me" => "required",

                'children' => 'required|integer|min:1',
                'exercise' => 'required|integer|min:1',
                'pets' => 'required|integer|min:1',
                'income_level' => 'required|integer|min:1',



                "about_match" => "required"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
            }

            $user = Auth::User();

            $user->dob = $request->dob;
            $user->height = $request->height;
            $user->weight = $request->weight;
            $user->body_type = $request->body_type;
            $user->hair_color = $request->hair_color;
            $user->eye_color = $request->eye_color;
            $user->nationality = $request->nationality;
            $user->region = $request->region;
            $user->city = $request->city;
            $user->sexual_orientation = $request->sexual_orientation;
            $user->education = $request->education;
            $user->field_of_work = $request->field_of_work;
            $user->relationship_status = $request->relationship_status;
            $user->zodiac_sign = $request->zodiac_sign;
            $user->smoking = $request->smoking;
            $user->drinking = $request->drinking;
            $user->tattoos = $request->tattoos;
            $user->piercings = $request->piercings;
            $user->about_me = $request->about_me;
            $user->about_match = $request->about_match;

            $user->children = $request->children;
            $user->exercise = $request->exercise;
            $user->pets = $request->pets;
            $user->income_level = $request->income_level;

            $user->save();
            $user->refresh();

            return response()->json([
                "status_code" => 200,
                "status" => true,
                "message" => "Details inserted successfully",
                "data" => $user

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }


    // *********************************************************api to get hear abt us ********************************************************************

    public function hearAboutUsListing()
    {
        $data = HearAboutUs::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => " HearAboutUs detail retrive successfully",
            "data" => $data
        ], 200);
    }

    // *****************************************************store hear abt us *************************************************************************
    public function hearAboutUS(request $request)
    {
        try {
            $rule = [
                "id" => "required"
            ];

            $validator = Validator::make($request->all(), $rule);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
            }


            $user = Auth::User();

            $user->hear_about_us = $request->id;

            $user->save();

            // Mail::send('email_registration', ['user' => $user], function($message) use ($user) {
            //     $message->to($user->email, $user->profile_name)->subject('Registration Successful');
            // });

            return response()->json([
                "status_code" => 200,
                "status" => true,
                "message" => " Data retrive successfully",
                "data" => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
    // ******************************************************************* get lookingFor *********************************************************************
    public function getLookingFor()
    {
        $data = LookngFor::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => " LookngFor  detail retrive successfully",
            "data" => $data
        ], 200);
    }

    // ************************************************************************store looking for ********************************************************

    public function lookingFor(request $request)
    {
        try {
            $request->validate(
                [
                    'looking_for_ids' => 'required',
                ],
                [
                    'looking_for_ids.required' => 'Please select atleast one option!'
                ]
            );

            $user = Auth::User();
            $ids = $request->looking_for_ids;
            // converting into string and save
            $user->looking_for = implode(',', $ids);
            $user->save();

            return response()->json([
                "status_code" => 200,
                "status" => true,
                "message" => " Data save successfully",
                "data" => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    // ****************************************************************************gender save*********************************************************************

    public function gender(Request $request)
    {
        try {
            $request->validate([
                "Gender" => "required",

            ]);


            $user = Auth::User();
            $user->gender = $request->Gender;
            $user->save();

            return response()->json([
                "status_code" => 200,
                "status"      => true,
                "message"     => "Gender saved successfully",
                "data"        => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status"  => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    // *******************************************************************store USER INTREST *************************************************************************

    public function userIntrest(Request $request)
    {
        try {


            $user = Auth::User();
            if (!$user) {
                return response()->json([
                    "status"  => false,
                    "message" => "Unauthorized access",
                ], 401);
            }

            $data = UserIntrest::create([
                'user_id'     => $user->id,
                'fav_music'   => $request->fav_music,
                'fav_tv_show' => $request->fav_tv_show,
                'fav_movie'   => $request->fav_movie,
                'fav_book'    => $request->fav_book,
                'fav_sport'   => $request->fav_sport,
                'other'       => $request->other,
            ]);

            $data->refresh();
            return response()->json([
                "status_code" => 200,
                "status"      => true,
                "message"     => "User Interest saved successfully",
                "data"        => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status"  => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }


    // *****************************************************************************country code****************************************************************************************************

    public function countryCode()
    {
        $data = CountryCode::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "Data detail retrive successfully",
            "data" => $data
        ], 200);
    }


    // *****************************************************************************nationality ****************************************************************************************************

    public function Nationality()
    {
        $data = Nationality::orderBy('nationality', 'asc')->get();;
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "Data detail retrive successfully",
            "data" => $data
        ], 200);
    }

    // ********************************************************************************get region ***************************************************************************************************

    public function getRegion()
    {
        $data = Region::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "Region retrive successfully",
            "data" => $data
        ], 200);
    }

    public function getCity(request $request)
    {


        $request->validate([
            'region_id' => 'required'
        ]);


        $region_id = $request->region_id;


        $data = City::where('region_id', $region_id)->orderBy('city', 'asc')->get();

        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "City retrieved successfully.",
            "data" => $data
        ], 200);
    }


    public function bodyType()
    {
        $data = BodyType::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "Body Type retrive successfully",
            "data" => $data
        ], 200);
    }



    public function sexOrientation()
    {
        $data = SexOrientation::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "SexOrientationretrive successfully",
            "data" => $data
        ], 200);
    }



    public function zodiacSign()
    {
        $data = Zodiac::get();
        return response()->json([
            "status_code" => 200,
            "status" => true,
            "message" => "Zodiac data ritreve successfully",
            "data" => $data
        ], 200);
    }


    // ****************************************************************************gallery image **********************************************************************************************

    public function galleryImages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_image' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $user = Auth::user();
            $savedImages = [];

            $images = $request->file('user_image');


            if (!is_array($images)) {
                $images = [$images];
            }

            $uploadedFileNames = [];

            foreach ($images as $image) {


                $hash = md5_file($image->getRealPath());


                if (in_array($hash, $uploadedFileNames)) {
                    continue;
                }

                $uploadedFileNames[] = $hash;


                $name = time() . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $path = public_path('uploads/public_image/');
                $image->move($path, $name);


                $savedImages[] = UserImage::create([
                    'user_id' => $user->id,
                    'type' => 2,
                    'profile_image' => 'public/uploads/public_image/' . $name,
                ]);
            }

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => "Gallery images uploaded successfully",
                'data' => $savedImages,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }





    // *******************************************************************************request private access******************************************************************************
    public function requestPrivateAccess(Request $request)
    {
        // Validate input
        $request->validate([
            'id' => 'required' // make sure the target user exists
        ]);

        $authUser = Auth::User();

        $userAccess = new RequestModel();
        $userAccess->request_to = $request->id;
        $userAccess->request_from = $authUser->id;
        $userAccess->save();


        // pass notification
        NotificationHelper::sendToUser(
            $request->id,
            'New Private Access Request ',
            $authUser->profile_name . ' has sent you a private access request.',
            $authUser->id
        );

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Private access request sent successfully'
        ]);
    }
    // *****************************************************************************request list ***********************************************************************************
    public function requestList(Request $request)
    {

        $request->validate([
            'key' => 'required', // expecting "confirm" or "pending"
        ]);
        $user = Auth::user();


        if ($request->key == 'pending') {
            $requests = RequestModel::with([
                'sender' => function ($q) {
                    $q->select('id', 'first_name', 'profile_name');
                },
                'sender.images' => function ($q) {
                    $q->where('type', 0)->select('id', 'user_id', 'profile_image');
                }
            ])
                ->where('request_to', $user->id)
                ->where('status', $request->key)
                ->get();

            $requests = $requests->map(function ($req) {
                if ($req->sender) {
                    $latestimage = UserImage::where('user_id', $req->sender->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage = UserImage::where('user_id', $req->sender->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $profileimage = $latestimage ? $latestimage->profile_image : $bestImage;
                    $req->sender->image = $profileimage;

                    $req->sender->first_name = $req->sender->profile_name;

                    unset($req->sender->images);
                }
                return $req;
            });

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => ucfirst($request->key) . ' requests fetched successfully',
                'data' => $requests,
            ]);
        }

        // confirm
        if ($request->key == 'confirm') {
            $requests = RequestModel::with([
                'sender' => function ($q) {
                    $q->select('id', 'first_name', 'profile_name');
                },
                'sender.images' => function ($q) {
                    $q->where('type', 0)->select('id', 'user_id', 'profile_image');
                }
            ])
                ->where('request_to', $user->id)
                ->where('status', $request->key)
                ->get();

            $requests = $requests->map(function ($req) {
                if ($req->sender) {
                    $latestimage = UserImage::where('user_id', $req->sender->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                    $bestImage = UserImage::where('user_id', $req->sender->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                    $profileimage = $latestimage ? $latestimage->profile_image : $bestImage;
                    $req->sender->image = $profileimage;

                    $req->sender->first_name = $req->sender->profile_name;

                    unset($req->sender->images);
                }
                return $req;
            });

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => ucfirst($request->key) . ' requests fetched successfully',
                'data' => $requests,
            ]);
        }
    }
    // ********************************************************************************accept request for private access*****************************************************************
    public function acceptRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required',
            'key' => 'required|in:confirm,delete',
        ]);

        $authUser = Auth::user();

        $requestData = RequestModel::where('request_to', $authUser->id)
            ->where('request_from', $request->request_id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$requestData) {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Request not found.',
            ], 404);
        }

        $senderId = $requestData->request_from;


        if ($request->key === 'confirm') {
            $requestData->status = 'confirm';
            $requestData->save();

            //notification
            NotificationHelper::sendToUser(
                $senderId,
                'Private Access Request Accepted',
                $authUser->profile_name . 'has accepted your private access request.',
                $authUser->id

            );

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Request accepted successfully.',
            ]);
        }

        if ($request->key === 'delete') {
            $requestData->delete();
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Request deleted successfully.',
            ]);
        }
    }


    // *****************************************************************************like user ********************************************************************************************
    public function likeUser(Request $request)
    {
        $request->validate([
            'like_to' => 'required|exists:users,id',
            'action'  => 'required|in:like,dislike' // new field: either 'like' or 'dislike'
        ]);

        $auth = Auth::user();
        $authUserId = $auth->id;
        $likeToUserId = $request->like_to;

        if ($request->action === 'like') {
            // Check if already liked
            $existingLike = UserLike::where('like_by', $authUserId)
                ->where('like_to', $likeToUserId)
                ->first();

            if (!$existingLike) {
                $userLike = new UserLike();
                $userLike->like_by = $authUserId;
                $userLike->like_to = $likeToUserId;
                $userLike->save();
            }

            // Update user table like_status if needed
            $likeToUser = User::find($likeToUserId);
            if ($likeToUser) {
                $likeToUser->like_status = 1;
                $likeToUser->save();
            }

            $title = "New Like Received";
            $body = $auth->profile_name . " liked your profile.";

            NotificationHelper::notification($likeToUserId, $title, $body, $auth->id);

            $message = 'User liked successfully';
        } else {
            // Dislike means remove the like entry (or mark as 0)
            UserLike::where('like_by', $authUserId)
                ->where('like_to', $likeToUserId)
                ->delete();

            $likeToUser = User::find($likeToUserId);
            if ($likeToUser) {
                $likeToUser->like_status = 0;
                $likeToUser->save();
            }

            $message = 'User disliked successfully';
        }

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => $message
        ]);
    }

    // *********************************************************************************like user list ********************************************************************
    public function likeUserList()
    {

        $user = Auth::User();

        $data =  UserLike::where('like_by', $user->id)
            ->with([
                'likeByUser:id,first_name,city,height,weight,profile_name,dob,region',
                'likeByUser.images' => function ($query) {
                    $query->where('type', 0)->select('id', 'user_id', 'profile_image');
                }
            ])->get();

        $data = $data->map(function ($item) {
            $likeByUser = $item->likeByUser;

            if ($likeByUser) {
                $latestimage2 = UserImage::where('user_id', $likeByUser->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                $bestImage2 = UserImage::where('user_id', $likeByUser->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                $userImage2 = $latestimage2 ? $latestimage2->profile_image : $bestImage2;
                // take the first image (type = 0)
                $likeByUser->profile_image = $userImage2;

                $likeByUser->age = Carbon::parse($likeByUser->dob)->age;

                $countryid = DB::table('region')->where('region', $likeByUser->region)->value('country_id');

                $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                $likeByUser->country = $countryname;
            } else {
                $likeByUser->profile_image = '';
                $likeByUser->country = '';
                $likeByUser->age = '';
            }

            unset($likeByUser->images);

            return $item;
        });


        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'User Like data retrieve successfully',
            'data' => $data
        ]);
    }

    //*************************************************************************add to favourite******************************************************************************************************

    public function addFavourite(Request $request)
    {
        $request->validate([
            'fav_to' => 'required|exists:users,id',
            'action' => 'required|in:add,remove'
        ]);

        $authUserId = Auth::id();
        $favToUserId = $request->fav_to;

        if ($request->action === 'add') {

            $existingFav = FavouriteUser::where('fav_by', $authUserId)
                ->where('fav_to', $favToUserId)
                ->first();

            if (!$existingFav) {
                $favUser = new FavouriteUser();
                $favUser->fav_by = $authUserId;
                $favUser->fav_to = $favToUserId;
                $favUser->save();
            }

            // Update favourite status on user table
            $favToUser = User::find($favToUserId);
            if ($favToUser) {
                $favToUser->fav_user_status = 1;
                $favToUser->save();
            }

            $message = 'User added to favourites successfully';
        } else {
            // Remove from favourites
            FavouriteUser::where('fav_by', $authUserId)
                ->where('fav_to', $favToUserId)
                ->delete();

            $favToUser = User::find($favToUserId);
            if ($favToUser) {
                $favToUser->fav_user_status = 0;
                $favToUser->save();
            }

            $message = 'User removed from favourites successfully';
        }

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => $message
        ]);
    }


    //*************************************************************************favourit user list *******************************************************************

    public function favUserList()
    {

        $user = Auth::User();


        //country
        $countryid = DB::table('region')->where('region', $user->region)->value('country_id');

        $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

        $user->country = $countryname;
        $user->age = Carbon::parse($user->dob)->age;






        $data = FavouriteUser::where('fav_by', $user->id)
            ->with([
                'favByUser:id,first_name,city,height,weight,profile_name,dob,region',
                'favByUser.images' => function ($query) {
                    $query->where('type', 0)->select('id', 'user_id', 'profile_image');
                }
            ])
            ->get();


        // FETCHING BEST PIC OF USER FROM USER_IMAGE TABLE         
        $data = $data->map(function ($item) {
            $favByUser = $item->favByUser;

            if ($favByUser) {
                $latestimage = UserImage::where('user_id', $favByUser->id)->where('type', 0)->where('profile_image_approval', 1)->orderBy('created_at', 'DESC')->first();
                $bestImage = UserImage::where('user_id', $favByUser->id)->where('type', 3)->orderBy('created_at', 'ASC')->value('profile_image');
                $profileimage = $latestimage ? $latestimage->profile_image : $bestImage;
                // take the first image (type = 0)
                $favByUser->profile_image = $profileimage;
                $favByUser->age  =  Carbon::parse($favByUser->dob)->age;

                $countryid = DB::table('region')->where('region', $favByUser->region)->value('country_id');

                $countryname = DB::table('countries')->where('id', $countryid)->value('short_name');

                $favByUser->country = $countryname;
            } else {
                $favByUser->profile_image = '';
                $favByUser->age = '';
                $favByUser->country = '';
            }
            // remove the 'images' array
            unset($favByUser->images);
            return $item;
        });

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'User Add To Favourite data retrieve successfully',
            'data' => $data
        ]);
    }


    // report reason get

    public function getReportReason()
    {
        $data = ReportReason::select('id', 'report_reason')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'No report reasons found',
                'data' => []
            ]);
        }

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $data
        ]);
    }

    // report user api 
    public function userReport(request $request)
    {

        $request->validate([
            'report_to' => 'required',
            'report_reason_id' => 'required',
            'other' => 'required'
        ]);

        $user = Auth::User();

        $reports = new ReportUser();
        $reports->report_by = $user->id;
        $reports->report_to = $request->report_to;
        $reports->report_reason = $request->report_reason_id;
        $reports->message = $request->other;
        $reports->save();

        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'User Reported successfully'
        ]);
    }
    // **************************************************************************image rating individuall image*******************************************************************

    public function imageRate(Request $request)
    {
        $request->validate([
            'rating'   => 'required',
            'image_id' => 'required',
            'rate_to'  => 'required',
        ]);

        $authUser = Auth::user();

        // Check if already rated
        $existingRating = ImageRating::where([
            'image_id' => $request->image_id,
            'rate_by'  => $authUser->id,
        ])->first();

        if ($existingRating) {
            $existingRating->rating = $request->rating;
            $existingRating->save();

            return response([
                'code' => 200,
                'status' => true,
                'message' => 'Image rating updated successfully',
            ]);
        }

        // Create new rating
        $data = new ImageRating();
        $data->image_id = $request->image_id;
        $data->rate_to  = $request->rate_to;
        $data->rate_by  = $authUser->id;
        $data->rating   = $request->rating;
        $data->save();

        return response([
            'code' => 200,
            'status' => true,
            'message' => 'Image rated successfully',
        ]);
    }


    // support request 
    public function supportRequest(Request $request)
    {
        try {
            $request->validate([
                'subject'   => 'required',
                'message' => 'required'
            ]);

            $data = new SupportRequest();
            $data->user_id = auth()->user()->id;
            $data->subject = $request->subject;
            $data->message = $request->message;
            $data->save();

            return response(['status' => true, 'message' => 'Request has been submitted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // submit suggestion
    public function submitSuggestion(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required|string'
            ]);

            $user = Auth::user();

            $suggestion = new UserSuggestion();
            $suggestion->user_id = $user->id;
            $suggestion->description = $request->description;
            $suggestion->save();

            return response()->json([
                'status' => true,
                'message' => 'Suggestion submitted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // block user 
    public function blockUnblockUser(Request $request)
    {
        try {
            $request->validate([
                'block_to' => 'required|integer'
            ]);

            $authId = auth()->id();
            $targetId = $request->block_to;

            $exists = blockedUsers::where('block_by', $authId)->where('block_to', $targetId)->exists();

            if ($exists) {
                blockedUsers::where('block_by', $authId)->where('block_to', $targetId)->delete();

                return response()->json(['status' => true, 'message' => 'User unblocked successfully!']);
            }

            blockedUsers::create([
                'block_by' => $authId,
                'block_to' => $targetId
            ]);

            return response()->json(['status' => true, 'message' => 'User blocked successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // get blocked users list
    public function blockedUsersList(Request $request)
    {
        try {
            $authId = auth()->id();

            $blockedIds = blockedUsers::where('block_by', $authId)->pluck('block_to');

            if ($blockedIds->isEmpty()) {
                return response()->json(['status' => true, 'message' => 'No blocked users found', 'sender_id' => $authId, 'data'   => []]);
            }

            $users = User::select('id', 'first_name', 'last_name', 'profile_name')->with('profile')->whereIn('id', $blockedIds)->get();

            return response()->json(['status' => true, 'message' => 'Blocked users fetched successfully', 'sender_id' => $authId, 'data'   => $users]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // rate user profile
    public function rateUserProfile(Request $request)
    {
        try {
            $request->validate([
                'rated_to' => 'required',
                'rating' => 'required'
            ]);

            $auth = Auth::user();

            RateUserProfile::updateOrCreate(
                ['user_id' => $auth->id, 'rated_to' => $request->rated_to],
                ['rating' => $request->rating]
            );

            return response()->json(['status' => true, 'message' => 'Profile rated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
