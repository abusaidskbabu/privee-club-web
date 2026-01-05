<?php
namespace Modules\Admin\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{PrivacyPolicy,TermsCondition};
class CommonController extends Controller
{ 
    
 public function privacy(Request $request, $id = null)
{
    // Find existing or create new
    $privacy = $id ? PrivacyPolicy::find(base64_decode($id)) : PrivacyPolicy::first();

    if ($request->isMethod('post')) {
        $request->validate([
            'privacy' => 'required'
        ]);

        if ($privacy) {
            $privacy->privacy_policy = $request->privacy;
            $privacy->save();
        } else {
            PrivacyPolicy::create([
                'privacy_policy' => $request->privacy
            ]);
        }

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }

    return view('admin::privacy', compact('privacy'));
}
    
    
  public function term(Request $request, $id = null)
{
    // Decode ID if provided (from base64_encode)
    $term = $id ? TermsCondition::findOrFail(base64_decode($id)) : TermsCondition::first();

    if ($request->isMethod('post')) {
        $request->validate([
            'description' => 'required',
        ]);

        $term->description = $request->description;
        $term->save();

        return redirect()->back()->with('success', 'Terms & Conditions updated successfully.');
    }

    return view('admin::term', compact('term'));
}
    
  
}
