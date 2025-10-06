<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PreEmploymentExamination;
use App\Models\AnnualPhysicalExamination;
use App\Models\User;

class PatientController extends Controller
{
    /**
     * Show the patient dashboard
     */
    public function dashboard()
    {
        return view('patient.dashboard');
    }

    /**
     * Show the patient profile
     */
    public function profile()
    {
        return view('patient.profile');
    }

    /**
     * Update the patient profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $user->update($request->only(['fname', 'lname', 'mname', 'email', 'phone', 'company']));

        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show medical results for the patient
     */
    public function medicalResults()
    {
        $user = Auth::user();
        $userFullName = trim($user->fname . ' ' . $user->lname);
        
        // Get pre-employment examinations sent to this patient
        $preEmploymentResults = PreEmploymentExamination::where('status', 'sent_to_patient')
            ->whereHas('patient', function($query) use ($user) {
                // Only match by exact email - more strict filtering
                $query->where('email', $user->email);
            })
            ->with(['preEmploymentRecord', 'patient'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get annual physical examinations sent to this patient
        // Only show examinations that have been properly sent to this specific patient
        $annualPhysicalResults = AnnualPhysicalExamination::where('status', 'sent_to_patient')
            ->whereHas('patient', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['patient'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('patient.medical-results', compact('preEmploymentResults', 'annualPhysicalResults'));
    }

    /**
     * View specific pre-employment examination result
     */
    public function viewPreEmploymentResult($id)
    {
        $user = Auth::user();
        
        $examination = PreEmploymentExamination::where('id', $id)
            ->where('status', 'sent_to_patient')
            ->whereHas('preEmploymentRecord', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['preEmploymentRecord', 'drugTestResults'])
            ->firstOrFail();
        
        return view('patient.view-pre-employment-result', compact('examination'));
    }

    /**
     * View specific annual physical examination result
     */
    public function viewAnnualPhysicalResult($id)
    {
        $user = Auth::user();
        
        $examination = AnnualPhysicalExamination::where('id', $id)
            ->where('status', 'sent_to_patient')
            ->whereHas('patient', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['patient', 'drugTestResults'])
            ->firstOrFail();
        
        return view('patient.view-annual-physical-result', compact('examination'));
    }
}
