<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{

    public function adminLogin(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $credentials = $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            if (Auth::guard('admin')->attempt($credentials)) {
                return redirect('admin/dashboard');
            } else {
                return back()->with('error_message', 'Invalid Credentials!');
            }
        }

        return view('admin::login');
    }


    public function updateStatus(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found!'
                ], 404);
            }
            // update status
            $user->admin_status = $request->admin_status;
            $user->status  = 1;

            //  mail according to condn
            if ($request->admin_status == 1) {
                $user->admin_approve_time = now();

                if ($user->language == 'English') {
                    //// send email
                    Mail::send('admin::email.ApprovedUser_email_UK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Approved Member');
                    });
                } else {
                    Mail::send('admin::email.ApprovedUser_email_DK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Godkendt medlem');
                    });
                }
            } elseif ($request->admin_status == 2) {
                $user->status  = 0;
                $user->save();
                if ($user->language == 'English') {
                    // send email
                    Mail::send('admin::email.NotapprovedUser_email_UK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Not Approved Member');
                    });
                } else {
                    Mail::send('admin::email.NotapprovedUser_email_DK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Ikke godkendt medlem');
                    });
                }
            } else {
                $user->admin_approve_time = null;
            }

            $user->save();

            $user->refresh();

            return response()->json([
                'status'  => true,
                'message' => 'Admin status updated successfully!',
                'data'    => [
                    'user_id'            => $user->id,
                    'admin_status'       => $user->admin_status,
                    'admin_approve_time' => $user->admin_approve_time,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    // direct make member
    public function makeMember(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found!'
                ], 404);
            }

            $user->admin_status = $request->admin_status;

            if ($request->admin_status == 1) {
                $user->admin_approve_time = now();

                if ($user->language == 'English') {
                    // send email
                    Mail::send('admin::email.Approvedmember_Rating_email_UK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Approved Member');
                    });
                } else {
                    Mail::send('admin::email.Approvedmember_Rating_email_DK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Godkendt medlem');
                    });
                }

                $user->profile_approval = 1;
                $user->profie_rating_status = "IN";
            } elseif ($request->admin_status == 2) {
                if ($user->language == 'English') {
                    // send email
                    Mail::send('admin::email.Notapprovedmember_Rating_email_UK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Not Approved Member');
                    });
                } else {
                    Mail::send('admin::email.Notapprovedmember_Rating_email_DK', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email, $user->profile_name)
                            ->subject('Ikke godkendt medlem');
                    });
                }
            } else {
                $user->admin_approve_time = null;
            }

            $user->save();

            $user->refresh();

            return response()->json([
                'status'  => true,
                'message' => 'Admin status updated successfully!',
                'data'    => [
                    'user_id'            => $user->id,
                    'admin_status'       => $user->admin_status,
                    'admin_approve_time' => $user->admin_approve_time,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }



    public function destroy($id)
    {

        $user = User::find($id);
        if ($user) {
            // Soft delete with tracking instead of hard delete
            $user->deleted_at = now();
            $user->deleted_by = 'admin';
            $user->deletion_type = 'admin_deleted';
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully!'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not found!'
        ]);
    }


    public function updateProfileApproval(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $user->profile_approval = $request->profile_approval;
            $user->profie_rating_status = 'IN';

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile approval updated successfully!',
                'profile_approval' => $user->profile_approval
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updateLoginStatus(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        $user->status = $request->status;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Status updated successfully']);
    }


    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function changePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($request->isMethod('post')) {
            $request->validate([
                'current_password' => 'required',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('admins', 'email')->ignore($admin->id),
                ],
                'password' => 'required|min:6|confirmed',
            ], [
                'current_password.required' => 'Current password is required',
                'email.required' => 'Email is required',
                'email.email' => 'Please enter a valid email address',
                'password.required' => 'New password is required',
                'password.min' => 'Password must be at least 6 characters',
                'password.confirmed' => 'Password confirmation does not match',
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->with('error', 'Current password is incorrect!');
            }

            // Update email and password
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->save();

            return back()->with('success', 'Login ID and password updated successfully!');
        }

        return view('admin::change_password', compact('admin'));
    }
}
