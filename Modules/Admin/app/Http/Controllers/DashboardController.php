<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, ProfileTimer, UserRate, UserImage, ReportReason, ReportUser, SupportRequest, BodyType, SexOrientation, Zodiac, LookngFor, Nationality, Region, City, HearAboutUs, Country, AppSetting, UserSuggestion};
use App\Helpers\NotificationHelper;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        try {
            $currentTime = Carbon::now();
            $profiletimers = (int) ProfileTimer::value('time');

            // Total Users
            $totalUsers = User::whereNull('deleted_at')->count();
            $male = User::where('gender', 'Male')->whereNull('deleted_at')->count();
            $female = User::where('gender', 'Female')->whereNull('deleted_at')->count();

            // New Users (pending approval)
            $newUsers = User::where('admin_status', 0)
                ->where('hear_about_us', '!=', '')
                ->whereNull('deleted_at')
                ->count();

            // Active Members
            $activeMembers = User::where('admin_status', 1)
                ->where('profie_rating_status', 'IN')
                ->whereNull('deleted_at')
                ->count();

            // New Applicants (in rating window)
            $newApplicants = User::where('admin_status', 1)
                ->where('profile_approval', 0)
                ->whereNotNull('admin_approve_time')
                ->whereNull('deleted_at')
                ->get()
                ->filter(function ($user) use ($profiletimers, $currentTime) {
                    if (!$user->admin_approve_time) return false;

                    try {
                        $approveTime = Carbon::createFromFormat('Y-m-d H:i:s', $user->admin_approve_time);
                        $expiryTime = $approveTime->copy()->addHours($profiletimers);
                        return $expiryTime->greaterThan($currentTime);
                    } catch (\Exception $e) {
                        return false;
                    }
                })
                ->count();

            // Rejected Users
            $rejectedUsers = User::where('admin_status', 2)
                ->whereNull('deleted_at')
                ->count();

            // Today's Registrations
            $todayRegistrations = User::whereDate('created_at', Carbon::today())
                ->whereNull('deleted_at')
                ->count();

            // This Week's Registrations
            $weekRegistrations = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->whereNull('deleted_at')
                ->count();

            // This Month's Registrations
            $monthRegistrations = User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereNull('deleted_at')
                ->count();

            // Pending Support Requests
            $pendingSupportRequests = SupportRequest::where('status', 0)->count();

            // Pending Suggestions
            $pendingSuggestions = UserSuggestion::count();

            // Pending Profile Update Requests
            $pendingProfileUpdates = UserImage::where('profile_image_approval', 0)->count();

            // Reported Users (pending review)
            $reportedUsers = ReportUser::where('status', 0)->count();

            // Ready Members
            $readyMembers = User::where('is_fake', 1)
                ->whereNull('deleted_at')
                ->count();

            return view('admin::dashboard', compact(
                'totalUsers',
                'male',
                'female',
                'newUsers',
                'activeMembers',
                'newApplicants',
                'rejectedUsers',
                'todayRegistrations',
                'weekRegistrations',
                'monthRegistrations',
                'pendingSupportRequests',
                'pendingSuggestions',
                'pendingProfileUpdates',
                'reportedUsers',
                'readyMembers'
            ));
        } catch (\Exception $e) {
            // Return default values to prevent view errors
            $totalUsers = 0;
            $male = 0;
            $female = 0;
            $newUsers = 0;
            $activeMembers = 0;
            $newApplicants = 0;
            $rejectedUsers = 0;
            $todayRegistrations = 0;
            $weekRegistrations = 0;
            $monthRegistrations = 0;
            $pendingSupportRequests = 0;
            $pendingSuggestions = 0;
            $pendingProfileUpdates = 0;
            $reportedUsers = 0;
            $readyMembers = 0;

            return view('admin::dashboard', compact(
                'totalUsers',
                'male',
                'female',
                'newUsers',
                'activeMembers',
                'newApplicants',
                'rejectedUsers',
                'todayRegistrations',
                'weekRegistrations',
                'monthRegistrations',
                'pendingSupportRequests',
                'pendingSuggestions',
                'pendingProfileUpdates',
                'reportedUsers',
                'readyMembers'
            ));
        }
    }


    public function Newuser()
    {

        $Users = User::with(['images' => function ($q) {
            $q->where('type', 0);
        }, 'profile'])
            ->where('admin_status', 0)
            ->whereNotNull('hear_about_us')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();
        // return $Users; 
        // $femaleUsers = User::with(['images' => function($q){ $q->where('type',0);}])->where('admin_status', 0)->where('gender', 'Female')->orderBy('id', 'desc')->get();
        return view('admin::user.newuser', compact('Users'));
    }
    public function deniedUser()
    {

        $Users = User::with(['images' => function ($q) {
            $q->where('type', 0);
        }, 'profile'])
            ->where('admin_status', 2)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();
        // $femaleUsers = User::with(['images' => function($q){ $q->where('type',0);}])->where('admin_status', 2)->orderBy('id', 'desc')->get();

        return view('admin::user.deniedUser', compact('Users'));
    }

    public function rejectedUsers()
    {

        $Users = User::with(['images' => function ($q) {
            $q->where('type', 0);
        }, 'profile'])
            ->where('admin_status', 2)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin::user.rejectedUsers', compact('Users'));
    }




    public function acceptUser()
    {
        $currentTime   = Carbon::now();
        $profiletimers = (int) ProfileTimer::value('time');
        $Users = User::with([
            'images' => function ($q) {
                $q->where('type', 0);
            },
            'profile'
        ])
            ->where('admin_status', 1)
            ->where('profile_approval', 0)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($user) use ($profiletimers) {

                // Default expiryTime
                $user->expiryTime = null;

                // Only calculate if approve time exists
                if (!empty($user->admin_approve_time)) {
                    $approveTime = Carbon::parse($user->admin_approve_time);
                    $user->expiryTime = $approveTime->addHours($profiletimers);
                }

                return $user;
            })
            ->filter(function ($user) use ($currentTime) {

                // keep users without approve time
                if ($user->expiryTime === null) {
                    return true;
                }

                // keep only non-expired users
                return $user->expiryTime->greaterThan($currentTime);
            })
            ->values();

        return view('admin::user.acceptUser', compact('Users'));
    }



    // public function ratedIn(){

    //     $Users = User::with(['images' => function($q){ $q->where('type',0);}, 'profile'])->where('admin_status', 1)
    //     ->where('profie_rating_status','IN')->get();
    //     // $femaleUsers = User::with(['images' => function($q){ $q->where('type',0);}])->where('admin_status', 1)->where('profile_approval',1)->where('profie_rating_status','IN')->where('gender', 'Female')->get();
    //     return view('admin::user.ratedIn', compact('Users'));

    // }

    public function ratedOut()
    {

        $Users = User::with([
            'images' => function ($q) {
                $q->where('type', 0);
            },
            'profile'
        ])
            ->whereIn('admin_status', [1, 2])
            ->whereIn('status', [0, 1])
            ->where('profie_rating_status', 'OUT')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin::user.ratedout', compact('Users'));
    }


    public function activeProfile()
    {
        $Users = User::with(['images' => function ($q) {
            $q->where('type', 0);
        }, 'profile'])
            ->where('admin_status', 1)
            ->where('profie_rating_status', 'IN')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();


        return view('admin::user.active', compact('Users'));
    }

    public function ratedOutApplicants()
    {
        $Users = User::with([
            'images' => function ($q) {
                $q->where('type', 0);
            },
            'profile'
        ])
            ->where('admin_status', 1)
            ->where('profie_rating_status', 'OUT')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin::user.ratedOutApplicants', compact('Users'));
    }

    public function nonActiveMembers()
    {
        $Users = User::with([
            'images' => function ($q) {
                $q->where('type', 0);
            },
            'profile'
        ])
            ->whereNotNull('deleted_at')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin::user.nonActiveMembers', compact('Users'));
    }

    public function viewUser($id, $type = null)
    {
        $user = User::with('profile', 'bestImage')->where('id', $id)->first();
        $rategiven = UserRate::with('ratedTo')->where('sender_id', $id)->get();
        $raterecived = UserRate::with('ratedBy')->where('reciever_id', $id)->get();

        return view('admin::user.show', compact('user', 'rategiven', 'raterecived', 'type'));
    }


    public function Catgeory()
    {
        $title = "Catgeory";
        return view('admin::catgeory.index', compact('title'));
    }

    public function add()
    {
        return view('admin::catgeory.add');
    }

    public function timer()
    {

        $timer =  ProfileTimer::first();
        return view('admin::profileTimer.timer', compact('timer'));
    }

    public function timerUpdate(Request $request)
    {

        $request->validate([
            'timer' => 'required|numeric',
        ]);

        ProfileTimer::first()->update(['time' => $request->timer]);
        return redirect()->back()->with('success', 'Rating window Time updated successfully!');
    }

    // profile updated requests
    public function profileUpdateRequest()
    {
        $allRequest = UserImage::where('profile_image_approval', 0)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin::profile_update_request.index', compact('allRequest'));
    }

    // approve or reject image
    public function actionApproveReject(Request $request)
    {
        try {
            $image = UserImage::find($request->id);

            if (!$image) {
                return response()->json(['status' => false, 'message' => 'Image not found']);
            }

            $image->profile_image_approval = $request->status;
            $image->save();

            return response()->json(['status' => true, 'message' => $request->status == 1 ? 'Image approved successfully' : 'Image rejected successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // report reasons 
    public function reportReason()
    {
        $report_reason = ReportReason::orderBy('created_at', 'DESC')->get();

        return view('admin::report_reason.index', compact('report_reason'));
    }

    // Add / Update Report Reason
    public function addUpdateReasons(Request $request, $id = '')
    {
        $id = $id ? base64_decode($id) : null;

        if ($id) {
            $report_reason = ReportReason::find($id);
            if (!$report_reason) {
                return redirect()->back()->with('error', 'Invalid reason selected!');
            }
            $message = "Report reason has been updated successfully!";
        } else {
            $report_reason = new ReportReason();
            $message = "Report reason has been created successfully!";
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'reason' => 'required'
            ]);

            $report_reason->report_reason = $request->reason;
            $report_reason->save();

            return redirect()->route('admin.report.reason')->with('success', $message);
        }

        return view('admin::report_reason.add_edit_reasons', compact('report_reason'));
    }

    // delete reasons
    public function deleteReportReason($id)
    {
        try {
            $id = base64_decode($id);

            $reason = ReportReason::find($id);

            if (!$reason) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reason not found!'
                ]);
            }

            $reason->delete();

            return response()->json([
                'status' => true,
                'message' => 'Reason deleted successfully!'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    // get data of reported users 
    public function reportedUsers()
    {
        $report = ReportUser::with(['reported_by', 'reported_to', 'reason'])->orderBy('created_at', 'DESC')->get();

        return view('admin::reported_users.index', compact('report'));
    }

    // update report status
    public function updateReportStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:report_user,id',
                'status' => 'required|in:0,1,2',
            ]);

            ReportUser::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json(['status' => true, 'message' => 'Status updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // delete report
    public function reportDelete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:report_user,id',
            ]);

            $report = ReportUser::find($request->id);
            $report->delete();

            return response()->json(['status' => true, 'message' => 'Report deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // get all support request
    public function support()
    {
        $support_request = SupportRequest::orderBy('created_at', 'DESC')->get();

        return view('admin::support.index', compact('support_request'));
    }

    // update support request status
    public function updateSupportRequestStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:support_request,id',
                'status' => 'required|in:0,1,2',
            ]);

            SupportRequest::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json(['status' => true, 'message' => 'Status updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // delete support request
    public function supportRequestDelete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:support_request,id',
            ]);

            $report = SupportRequest::find($request->id);
            $report->delete();

            return response()->json(['status' => true, 'message' => 'Support Request deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // user search
    public function userSearch(Request $request)
    {
        // Load lookup data for dropdowns
        $bodyTypes = BodyType::orderBy('body_type')->get();
        $sexOrientations = SexOrientation::orderBy('sex_orientation')->get();
        $zodiacSigns = Zodiac::orderBy('Zodiac_Signs')->get();
        $lookingFors = LookngFor::orderBy('looking_for')->get();
        $nationalities = Nationality::orderBy('nationality')->get();
        $regions = Region::orderBy('region')->get();
        $cities = City::orderBy('city')->get();

        // Get distinct values for hair_color and eye_color
        $hairColors = User::whereNotNull('hair_color')->distinct()->orderBy('hair_color')->pluck('hair_color')->unique()->values();
        $eyeColors = User::whereNotNull('eye_color')->distinct()->orderBy('eye_color')->pluck('eye_color')->unique()->values();

        // Start building query - include ALL users (active and inactive)
        $query = User::with([
            'images' => function ($q) {
                $q->where('type', 0);
            },
            'profile'
        ]);

        // Apply filters only if request has data (POST request with filters)
        if ($request->hasAny(['first_name', 'last_name', 'email', 'mobile_no', 'gender', 'body_type', 'hair_color', 'eye_color', 'nationality', 'region', 'city', 'zodiac_sign', 'sexual_orientation', 'looking_for', 'age_min', 'age_max', 'height_min', 'height_max', 'weight_min', 'weight_max', 'signup_date_from', 'signup_date_to', 'status_category'])) {

            // Text fields - LIKE search
            if ($request->filled('first_name')) {
                $query->where('first_name', 'LIKE', '%' . $request->first_name . '%');
            }

            if ($request->filled('last_name')) {
                $query->where('last_name', 'LIKE', '%' . $request->last_name . '%');
            }

            if ($request->filled('email')) {
                $query->where('email', 'LIKE', '%' . $request->email . '%');
            }

            if ($request->filled('mobile_no')) {
                $query->where('mobile_no', 'LIKE', '%' . $request->mobile_no . '%');
            }

            // Exact matches - handle as arrays for multiple selection
            if ($request->filled('gender') && is_array($request->gender) && count($request->gender) > 0) {
                $genders = array_filter($request->gender, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($genders) > 0) {
                    $query->whereIn('gender', $genders);
                }
            }

            if ($request->filled('body_type') && is_array($request->body_type) && count($request->body_type) > 0) {
                $filteredBodyTypes = array_filter($request->body_type, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredBodyTypes) > 0) {
                    $query->whereIn('body_type', $filteredBodyTypes);
                }
            }

            if ($request->filled('hair_color') && is_array($request->hair_color) && count($request->hair_color) > 0) {
                $filteredHairColors = array_filter($request->hair_color, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredHairColors) > 0) {
                    $query->whereIn('hair_color', $filteredHairColors);
                }
            }

            if ($request->filled('eye_color') && is_array($request->eye_color) && count($request->eye_color) > 0) {
                $filteredEyeColors = array_filter($request->eye_color, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredEyeColors) > 0) {
                    $query->whereIn('eye_color', $filteredEyeColors);
                }
            }

            if ($request->filled('nationality') && is_array($request->nationality) && count($request->nationality) > 0) {
                $filteredNationalities = array_filter($request->nationality, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredNationalities) > 0) {
                    $query->whereIn('nationality', $filteredNationalities);
                }
            }

            if ($request->filled('region') && is_array($request->region) && count($request->region) > 0) {
                $filteredRegions = array_filter($request->region, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredRegions) > 0) {
                    $query->whereIn('region', $filteredRegions);
                }
            }

            if ($request->filled('city') && is_array($request->city) && count($request->city) > 0) {
                $filteredCities = array_filter($request->city, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredCities) > 0) {
                    $query->whereIn('city', $filteredCities);
                }
            }

            if ($request->filled('zodiac_sign') && is_array($request->zodiac_sign) && count($request->zodiac_sign) > 0) {
                $filteredZodiacSigns = array_filter($request->zodiac_sign, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredZodiacSigns) > 0) {
                    $query->whereIn('zodiac_sign', $filteredZodiacSigns);
                }
            }

            if ($request->filled('sexual_orientation') && is_array($request->sexual_orientation) && count($request->sexual_orientation) > 0) {
                $filteredOrientations = array_filter($request->sexual_orientation, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredOrientations) > 0) {
                    $query->whereIn('sexual_orientation', $filteredOrientations);
                }
            }

            if ($request->filled('looking_for') && is_array($request->looking_for) && count($request->looking_for) > 0) {
                $filteredLookingFors = array_filter($request->looking_for, function ($val) {
                    return $val !== '__select_all__';
                });
                if (count($filteredLookingFors) > 0) {
                    $query->whereIn('looking_for', $filteredLookingFors);
                }
            }

            // Age range filter (calculated from DOB)
            if ($request->filled('age_min') || $request->filled('age_max')) {
                $minAge = $request->age_min ?? 0;
                $maxAge = $request->age_max ?? 150;
                $query->whereRaw('YEAR(CURDATE()) - YEAR(dob) - (DATE_FORMAT(CURDATE(), "%m%d") < DATE_FORMAT(dob, "%m%d")) BETWEEN ? AND ?', [$minAge, $maxAge]);
            }

            // Height range filter
            if ($request->filled('height_min')) {
                $query->whereRaw('CAST(height AS UNSIGNED) >= ?', [$request->height_min]);
            }

            if ($request->filled('height_max')) {
                $query->whereRaw('CAST(height AS UNSIGNED) <= ?', [$request->height_max]);
            }

            // Weight range filter
            if ($request->filled('weight_min')) {
                $query->whereRaw('CAST(weight AS UNSIGNED) >= ?', [$request->weight_min]);
            }

            if ($request->filled('weight_max')) {
                $query->whereRaw('CAST(weight AS UNSIGNED) <= ?', [$request->weight_max]);
            }

            // Sign-up date range
            if ($request->filled('signup_date_from')) {
                $query->whereDate('created_at', '>=', $request->signup_date_from);
            }

            if ($request->filled('signup_date_to')) {
                $query->whereDate('created_at', '<=', $request->signup_date_to);
            }

            // Status category filter - handle multiple selections
            if ($request->filled('status_category') && is_array($request->status_category) && count($request->status_category) > 0) {
                $statusCategories = array_filter($request->status_category, function ($val) {
                    return $val !== '__select_all__';
                });

                if (count($statusCategories) > 0) {
                    $currentTime = Carbon::now();
                    $profiletimers = (int) ProfileTimer::value('time');

                    $query->where(function ($q) use ($statusCategories) {
                        $firstCondition = true;

                        foreach ($statusCategories as $statusCategory) {
                            if ($firstCondition) {
                                // First condition - use where
                                $firstCondition = false;
                                switch ($statusCategory) {
                                    case 'new_users':
                                        $q->where('admin_status', 0)->whereNull('deleted_at');
                                        break;

                                    case 'new_applicants':
                                        $q->where('admin_status', 1)
                                            ->where('profile_approval', 0)
                                            ->whereNull('deleted_at')
                                            ->whereNotNull('admin_approve_time');
                                        break;

                                    case 'new_members':
                                        $q->where('admin_status', 1)
                                            ->where('profile_approval', 1)
                                            ->where('profie_rating_status', 'IN')
                                            ->whereNull('deleted_at');
                                        break;

                                    case 'rejected_users':
                                        $q->where('admin_status', 2)->whereNull('deleted_at');
                                        break;

                                    case 'applicants_voted_out':
                                        $q->where('admin_status', 1)
                                            ->where('profie_rating_status', 'OUT')
                                            ->whereNull('deleted_at');
                                        break;

                                    case 'applicants_voted_in':
                                        $q->where('admin_status', 1)
                                            ->where('profie_rating_status', 'IN')
                                            ->whereNull('deleted_at');
                                        break;

                                    case 'deleted_by_admin':
                                        $q->whereNotNull('deleted_at')->where('deleted_by', 'admin');
                                        break;

                                    case 'deleted_by_member':
                                        $q->whereNotNull('deleted_at')->where('deleted_by', 'user');
                                        break;
                                }
                            } else {
                                // Additional conditions - use orWhere
                                $q->orWhere(function ($subQuery) use ($statusCategory) {
                                    switch ($statusCategory) {
                                        case 'new_users':
                                            $subQuery->where('admin_status', 0)->whereNull('deleted_at');
                                            break;

                                        case 'new_applicants':
                                            $subQuery->where('admin_status', 1)
                                                ->where('profile_approval', 0)
                                                ->whereNull('deleted_at')
                                                ->whereNotNull('admin_approve_time');
                                            break;

                                        case 'new_members':
                                            $subQuery->where('admin_status', 1)
                                                ->where('profile_approval', 1)
                                                ->where('profie_rating_status', 'IN')
                                                ->whereNull('deleted_at');
                                            break;

                                        case 'rejected_users':
                                            $subQuery->where('admin_status', 2)->whereNull('deleted_at');
                                            break;

                                        case 'applicants_voted_out':
                                            $subQuery->where('admin_status', 1)
                                                ->where('profie_rating_status', 'OUT')
                                                ->whereNull('deleted_at');
                                            break;

                                        case 'applicants_voted_in':
                                            $subQuery->where('admin_status', 1)
                                                ->where('profie_rating_status', 'IN')
                                                ->whereNull('deleted_at');
                                            break;

                                        case 'deleted_by_admin':
                                            $subQuery->whereNotNull('deleted_at')->where('deleted_by', 'admin');
                                            break;

                                        case 'deleted_by_member':
                                            $subQuery->whereNotNull('deleted_at')->where('deleted_by', 'user');
                                            break;
                                    }
                                });
                            }
                        }
                    });
                }
            }
        }

        // Order by ID descending
        $users = $query->orderBy('id', 'desc')->get();

        // Calculate age for each user and apply status category filter for new_applicants if needed
        if ($request->filled('status_category')) {
            $statusCategories = is_array($request->status_category) ? $request->status_category : [$request->status_category];
            if (in_array('new_applicants', $statusCategories)) {
                $profiletimers = (int) ProfileTimer::value('time');
                $currentTime = Carbon::now();
                $users = $users->filter(function ($user) use ($profiletimers, $currentTime) {
                    if (!$user->admin_approve_time) return false;
                    $approveTime = Carbon::createFromFormat('Y-m-d H:i:s', $user->admin_approve_time);
                    $expiryTime = $approveTime->copy()->addHours($profiletimers);
                    return $expiryTime->greaterThan($currentTime);
                })->values();
            }
        }

        // Calculate age for display
        $users = $users->map(function ($user) {
            if ($user->dob) {
                $user->age = Carbon::parse($user->dob)->age;
            } else {
                $user->age = null;
            }
            return $user;
        });

        return view('admin::user.search', compact(
            'users',
            'bodyTypes',
            'sexOrientations',
            'zodiacSigns',
            'lookingFors',
            'nationalities',
            'regions',
            'cities',
            'hairColors',
            'eyeColors'
        ));
    }

    // Location Management
    public function locationManagement()
    {
        // Show all records - removed deleted_at filter to show all data
        $countries = Country::whereNull('deleted_at')->orderByRaw('country IS NULL, country ASC')->get();
        $regions = Region::with('country')->orderByRaw('region IS NULL, region ASC')->where('deleted_at', null)->get();
        $cities = City::with(['region', 'region.country'])->orderByRaw('city IS NULL, city ASC')->where('deleted_at', null)->get();

        return view('admin::location.index', compact('countries', 'regions', 'cities'));
    }

    // Add/Update Country
    public function addUpdateCountry(Request $request, $id = '')
    {
        $id = $id ? base64_decode($id) : null;

        if ($id) {
            $country = Country::find($id);
            if (!$country) {
                return redirect()->back()->with('error', 'Invalid country selected!');
            }
            $message = "Country has been updated successfully!";
        } else {
            $country = new Country();
            $message = "Country has been created successfully!";
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'country' => 'required|string|max:255',
                'short_name' => 'nullable|string|max:10'
            ]);

            $country->country = $request->country;
            $country->short_name = $request->short_name;
            $country->save();

            return redirect()->route('admin.location.management')->with('success', $message);
        }

        return view('admin::location.add_edit_country', compact('country'));
    }

    // Delete Country
    public function deleteCountry($id)
    {
        try {
            $id = base64_decode($id);
            $country = Country::find($id);

            if (!$country) {
                return response()->json([
                    'status' => false,
                    'message' => 'Country not found!'
                ]);
            }

            // Check if country has regions
            if ($country->regions()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete country. It has associated regions!'
                ]);
            }

            $country->deleted_at = now();
            $country->save();

            return response()->json([
                'status' => true,
                'message' => 'Country deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Add/Update Region
    public function addUpdateRegion(Request $request, $id = '')
    {
        $id = $id ? base64_decode($id) : null;
        $countries = Country::orderBy('country')->get();

        if ($id) {
            $region = Region::find($id);
            if (!$region) {
                return redirect()->back()->with('error', 'Invalid region selected!');
            }
            $message = "Region has been updated successfully!";
        } else {
            $region = new Region();
            $message = "Region has been created successfully!";
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'region' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id'
            ]);

            $region->region = $request->region;
            $region->country_id = $request->country_id;
            $region->save();

            return redirect()->route('admin.location.management')->with('success', $message);
        }

        return view('admin::location.add_edit_region', compact('region', 'countries'));
    }

    // Delete Region
    public function deleteRegion($id)
    {
        try {
            $id = base64_decode($id);
            $region = Region::find($id);

            if (!$region) {
                return response()->json([
                    'status' => false,
                    'message' => 'Region not found!'
                ]);
            }

            // Check if region has cities
            if ($region->cities()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete region. It has associated cities!'
                ]);
            }

            $region->deleted_at = now();
            $region->save();

            return response()->json([
                'status' => true,
                'message' => 'Region deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Add/Update City
    public function addUpdateCity(Request $request, $id = '')
    {
        $id = $id ? base64_decode($id) : null;
        $regions = Region::with('country')->orderBy('region')->get();

        if ($id) {
            $city = City::find($id);
            if (!$city) {
                return redirect()->back()->with('error', 'Invalid city selected!');
            }
            $message = "City has been updated successfully!";
        } else {
            $city = new City();
            $message = "City has been created successfully!";
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'city' => 'required|string|max:255',
                'region_id' => 'required|exists:region,id'
            ]);

            $city->city = $request->city;
            $city->region_id = $request->region_id;
            $city->save();

            return redirect()->route('admin.location.management')->with('success', $message);
        }

        return view('admin::location.add_edit_city', compact('city', 'regions'));
    }

    // Delete City
    public function deleteCity($id)
    {
        try {
            $id = base64_decode($id);
            $city = City::find($id);
            if (!$city) {
                return response()->json([
                    'status' => false,
                    'message' => 'City not found!'
                ]);
            }

            $city->deleted_at = now();
            $city->save();

            return response()->json([
                'status' => true,
                'message' => 'City deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Nationalities Management
    public function nationalitiesManagement()
    {
        // Show all records - removed deleted_at filter to show all data
        $nationalities = Nationality::orderByRaw('nationality IS NULL, nationality ASC')->whereNull('deleted_at')->get();

        return view('admin::nationality.index', compact('nationalities'));
    }

    // Add/Update Nationality
    public function addUpdateNationality(Request $request, $id = '')
    {
        $id = $id ? base64_decode($id) : null;

        if ($id) {
            $nationality = Nationality::find($id);
            if (!$nationality) {
                return redirect()->back()->with('error', 'Invalid nationality selected!');
            }
            $message = "Nationality has been updated successfully!";
        } else {
            $nationality = new Nationality();
            $message = "Nationality has been created successfully!";
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'nationality' => 'required|string|max:255'
            ]);

            $nationality->nationality = $request->nationality;
            $nationality->save();

            return redirect()->route('admin.nationalities.management')->with('success', $message);
        }

        return view('admin::nationality.add_edit', compact('nationality'));
    }

    // Delete Nationality
    public function deleteNationality($id)
    {
        try {
            $id = base64_decode($id);
            $nationality = Nationality::find($id);

            if (!$nationality) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nationality not found!'
                ]);
            }

            // Check if nationality is used by any users
            $userCount = User::where('nationality', $nationality->nationality)->count();
            if ($userCount > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete nationality. It is being used by ' . $userCount . ' user(s)!'
                ]);
            }

            $nationality->deleted_at = now();
            $nationality->save();

            return response()->json([
                'status' => true,
                'message' => 'Nationality deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Ready Members Management
    public function readyMembers()
    {
        $readyMembers = User::where('is_fake', 1)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        // Get global toggle setting
        $globalSetting = AppSetting::where('setting_key', 'show_fake_users_mobile')->first();
        $showOnMobile = $globalSetting ? (int)$globalSetting->setting_value : 1;

        return view('admin::ready_member.index', compact('readyMembers', 'showOnMobile'));
    }

    // Add/Edit Ready Member
    public function addReadyMember(Request $request, $id = '')
    {
        $id = $id ? base64_decode($id) : null;

        // Load lookup data
        $bodyTypes = BodyType::orderBy('body_type')->get();
        $zodiacs = Zodiac::orderBy('Zodiac_Signs')->get();
        $sexOrientations = SexOrientation::orderBy('sex_orientation')->get();
        $nationalities = Nationality::orderByRaw('nationality IS NULL, nationality ASC')->get();
        $regions = Region::with('country')->orderByRaw('region IS NULL, region ASC')->get();
        $cities = City::with(['region', 'region.country'])->orderByRaw('city IS NULL, city ASC')->get();

        if ($id) {
            $member = User::where('is_fake', 1)->with('profile')->find($id);
            if (!$member) {
                return redirect()->route('admin.ready.members')->with('error', 'Ready member not found!');
            }
            $message = "Ready member has been updated successfully!";
        } else {
            $member = new User();
            $message = "Ready member has been created successfully!";
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|in:Male,Female',
                'dob' => 'required|date',
                'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'height' => 'nullable|numeric',
                'weight' => 'nullable|numeric',
            ]);

            // Generate member number if new
            if (!$id) {
                $lastNumber = User::max('member_number');
                if ($lastNumber) {
                    $newNumber = str_pad(intval($lastNumber) + 1, 6, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '000001';
                }
                $member->member_number = $newNumber;
            }

            $member->first_name = $request->first_name;
            $member->last_name = $request->last_name;
            $member->gender = $request->gender;
            $member->dob = $request->dob;
            $member->height = $request->height;
            $member->weight = $request->weight;
            $member->body_type = $request->body_type;
            $member->hair_color = $request->hair_color;
            $member->eye_color = $request->eye_color;
            $member->nationality = $request->nationality;
            $member->region = $request->region;
            $member->city = $request->city;
            $member->zodiac_sign = $request->zodiac_sign;
            $member->sexual_orientation = $request->sexual_orientation;
            $member->about_me = $request->about_me;
            $member->about_match = $request->about_match;
            $member->is_fake = 1;
            $member->is_active = $request->has('is_active') ? 1 : 0;
            $member->show_on_mobile = $request->has('show_on_mobile') ? 1 : 0;
            $member->status = $member->is_active;
            $member->admin_status = 0;
            $member->profile_approval = 0;
            $member->profie_rating_status = 'OUT';

            $member->save();

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = public_path('uploads/users/');

                // Create directory if it doesn't exist
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                // Upload new image
                $image->move($path, $name);

                // Check existing profile image (type = 0)
                $existingImage = UserImage::where('user_id', $member->id)
                    ->where('type', 0)
                    ->first();

                // Delete old file if exists
                if ($existingImage && $existingImage->profile_image) {
                    $oldPath = public_path(str_replace('public/', '', $existingImage->profile_image));
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $existingImage->delete();
                }

                // Create new profile image record
                UserImage::create([
                    'user_id' => $member->id,
                    'type' => 0,
                    'profile_image' => 'uploads/users/' . $name,
                    'profile_image_approval' => 1, // Auto-approve for admin-created members
                ]);
            }

            return redirect()->route('admin.ready.members')->with('success', $message);
        }

        return view('admin::ready_member.add_edit', compact('member', 'bodyTypes', 'zodiacs', 'sexOrientations', 'nationalities', 'regions', 'cities'));
    }

    // View Ready Member
    public function viewReadyMember($id)
    {
        $id = base64_decode($id);
        $member = User::where('is_fake', 1)->with(['images' => function ($q) {
            $q->where('type', 2)->orderBy('created_at', 'desc');
        }])->find($id);

        if (!$member) {
            return redirect()->route('admin.ready.members')->with('error', 'Ready member not found!');
        }

        // Get ratings (if any)
        $rategiven = UserRate::where('sender_id', $member->id)->with('ratedTo')->get();
        $raterecived = UserRate::where('reciever_id', $member->id)->with('ratedBy')->get();

        return view('admin::ready_member.view', compact('member', 'rategiven', 'raterecived'));
    }

    // Delete Ready Member
    public function deleteReadyMember($id)
    {
        try {
            $id = base64_decode($id);
            $member = User::where('is_fake', 1)->find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ready member not found!'
                ]);
            }

            $member->deleted_at = now();
            $member->deleted_by = 'admin';
            $member->deletion_type = 'admin_deleted';
            $member->save();

            return response()->json([
                'status' => true,
                'message' => 'Ready member deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Toggle Ready Member Status
    public function toggleReadyMemberStatus($id)
    {
        try {
            $id = base64_decode($id);
            $member = User::where('is_fake', 1)->find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ready member not found!'
                ]);
            }

            $member->is_active = $member->is_active == 1 ? 0 : 1;
            $member->status = $member->is_active;
            $member->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully!',
                'is_active' => $member->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Bulk Action Ready Members
    public function bulkActionReadyMembers(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|in:enable,disable,delete,toggle_mobile',
                'ids' => 'required|array',
                'ids.*' => 'integer'
            ]);

            // $ids = array_map(function ($id) {
            //     return base64_decode($id);
            // }, $request->ids);
            $ids = $request->ids;
            $members = User::where('is_fake', 1)->whereIn('id', $ids)->get();

            if ($members->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No members selected!'
                ]);
            }

            switch ($request->action) {
                case 'enable':
                    User::whereIn('id', $ids)->update(['is_active' => 1, 'status' => 1]);
                    $message = count($ids) . ' member(s) enabled successfully!';
                    break;

                case 'disable':
                    User::whereIn('id', $ids)->update(['is_active' => 0, 'status' => 0]);
                    $message = count($ids) . ' member(s) disabled successfully!';
                    break;

                case 'delete':
                    User::whereIn('id', $ids)->update([
                        'deleted_at' => now(),
                        'deleted_by' => 'admin',
                        'deletion_type' => 'admin_deleted'
                    ]);
                    $message = count($ids) . ' member(s) deleted successfully!';
                    break;

                case 'toggle_mobile':
                    // Toggle show_on_mobile for each member
                    foreach ($members as $member) {
                        $member->show_on_mobile = $member->show_on_mobile == 1 ? 0 : 1;
                        $member->save();
                    }
                    $message = count($ids) . ' member(s) mobile visibility toggled successfully!';
                    break;
            }

            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Toggle Global Mobile Visibility
    public function toggleGlobalMobileVisibility(Request $request)
    {
        try {
            $request->validate([
                'show_on_mobile' => 'required|boolean'
            ]);

            $setting = AppSetting::updateOrCreate(
                ['setting_key' => 'show_fake_users_mobile'],
                ['setting_value' => $request->show_on_mobile ? '1' : '0']
            );

            return response()->json([
                'status' => true,
                'message' => 'Global setting updated successfully!',
                'show_on_mobile' => (int)$setting->setting_value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Upload Ready Member Gallery
    public function uploadReadyMemberGallery(Request $request, $id)
    {
        try {
            $request->validate([
                'gallery_images' => 'required|array',
                'gallery_images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            $id = base64_decode($id);
            $member = User::where('is_fake', 1)->find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ready member not found!'
                ]);
            }

            $savedImages = [];
            $images = $request->file('gallery_images');

            // Ensure it's an array
            if (!is_array($images)) {
                $images = [$images];
            }

            // Use hash to prevent duplicate uploads
            $uploadedHashes = [];

            foreach ($images as $image) {
                // Check for duplicate using file hash
                $hash = md5_file($image->getRealPath());

                // Skip if already uploaded
                if (in_array($hash, $uploadedHashes)) {
                    continue;
                }

                $uploadedHashes[] = $hash;

                $name = time() . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $path = public_path('uploads/public_image/');

                // Create directory if it doesn't exist
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                $image->move($path, $name);

                $savedImages[] = UserImage::create([
                    'user_id' => $member->id,
                    'type' => 2,
                    'profile_image' => 'uploads/public_image/' . $name,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Gallery images uploaded successfully!',
                'data' => $savedImages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Delete Ready Member Image
    public function deleteReadyMemberImage($id)
    {
        try {
            $id = base64_decode($id);
            $image = UserImage::find($id);

            if (!$image) {
                return response()->json([
                    'status' => false,
                    'message' => 'Image not found!'
                ]);
            }

            // Delete file
            if ($image->profile_image) {
                $filePath = public_path(str_replace('public/', '', $image->profile_image));
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $image->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Send Notification Page
    public function sendNotification()
    {
        // Load users for multiple selection (optional - can be loaded via AJAX)
        $users = User::whereNull('deleted_at')
            ->select('id', 'member_number', 'first_name', 'last_name', 'email')
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();

        return view('admin::notification.send', compact('users'));
    }

    // Search Users for Notification (AJAX)
    public function searchUsersForNotification(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query) || strlen($query) < 1) {
            return response()->json([]);
        }

        $users = User::whereNull('deleted_at')
            ->where(function ($q) use ($query) {
                $q->where('member_number', 'LIKE', "%{$query}%")
                    ->orWhere('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
            })
            ->select('id', 'member_number', 'first_name', 'last_name', 'email')
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->limit(50)
            ->get()
            ->map(function ($user) {
                $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                return [
                    'id' => $user->id,
                    'member_number' => $user->member_number ?? '—',
                    'first_name' => $user->first_name ?? '',
                    'last_name' => $user->last_name ?? '',
                    'email' => $user->email ?? '',
                    'label' => $fullName ?: 'No Name',
                    'displayText' => ($fullName ?: 'No Name') . ' (' . ($user->member_number ?? '—') . ')'
                ];
            });

        return response()->json($users);
    }

    // Send Notification to Selected Users
    public function sendNotificationToUsers(Request $request)
    {
        try {
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer|exists:users,id',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000'
            ]);

            $userIds = $request->user_ids;
            $title = $request->title;
            $body = $request->body;

            $result = NotificationHelper::sendToMultipleUsers($userIds, $title, $body, 0);

            if ($result['success']) {
                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'success_count' => $result['success_count'],
                    'failed_count' => $result['failed_count']
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Send Notification to Group
    public function sendNotificationToGroup(Request $request)
    {
        try {
            $request->validate([
                'group' => 'required|in:new_users,new_applicants,new_members,rejected_users,applicants_voted_out,applicants_voted_in',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000'
            ]);

            $group = $request->group;
            $title = $request->title;
            $body = $request->body;
            $profiletimers = (int) ProfileTimer::value('time');
            $currentTime = Carbon::now();

            // Build query based on group
            $query = User::whereNull('deleted_at');

            $userIds = [];

            switch ($group) {
                case 'new_users':
                    $userIds = $query->where('admin_status', 0)
                        ->where('hear_about_us', '!=', '')
                        ->pluck('id')->toArray();
                    break;

                case 'new_applicants':
                    // Filter by rating window (same logic as acceptUser)
                    $users = $query->where('admin_status', 1)
                        ->where('profile_approval', 0)
                        ->whereNotNull('admin_approve_time')
                        ->get()
                        ->map(function ($user) use ($profiletimers) {
                            $approveTime = Carbon::createFromFormat('Y-m-d H:i:s', $user->admin_approve_time);
                            $user->expiryTime = $approveTime->copy()->addHours($profiletimers);
                            return $user;
                        })
                        ->filter(function ($user) use ($currentTime) {
                            return $user->expiryTime->greaterThan($currentTime);
                        });
                    $userIds = $users->pluck('id')->toArray();
                    break;

                case 'new_members':
                    $userIds = $query->where('admin_status', 1)
                        ->where('profile_approval', 1)
                        ->where('profie_rating_status', 'IN')
                        ->pluck('id')->toArray();
                    break;

                case 'rejected_users':
                    $userIds = $query->where('admin_status', 2)
                        ->pluck('id')->toArray();
                    break;

                case 'applicants_voted_out':
                    $userIds = $query->where('admin_status', 1)
                        ->where('profie_rating_status', 'OUT')
                        ->pluck('id')->toArray();
                    break;

                case 'applicants_voted_in':
                    $userIds = $query->where('admin_status', 1)
                        ->where('profie_rating_status', 'IN')
                        ->pluck('id')->toArray();
                    break;
            }

            if (empty($userIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No users found in selected group!'
                ]);
            }

            $result = NotificationHelper::sendToMultipleUsers($userIds, $title, $body, 0);

            if ($result['success']) {
                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'success_count' => $result['success_count'],
                    'failed_count' => $result['failed_count']
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Help Us Do Better - Display all suggestions
    public function helpUsDoBetter()
    {
        $suggestions = UserSuggestion::with('user')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin::help_us_do_better.index', compact('suggestions'));
    }

    // Delete suggestion
    public function deleteSuggestion(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:user_suggestions,id',
            ]);

            $suggestion = UserSuggestion::find($request->id);
            $suggestion->delete();

            return response()->json([
                'status' => true,
                'message' => 'Suggestion deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
