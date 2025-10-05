<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // First try direct patient_id relationship, then fallback to name/email matching
        $preEmploymentResults = PreEmploymentExamination::where('status', 'sent_to_patient')
            ->where(function($query) use ($user, $userFullName) {
                // Primary: Match by patient_id (direct relationship)
                $query->where('patient_id', $user->id);
                
                // Fallback: Match by examination name or pre-employment record details
                $query->orWhere(function($fallbackQuery) use ($user, $userFullName) {
                    $fallbackQuery->where('name', 'like', '%' . $userFullName . '%')
                                  ->orWhere('name', 'like', '%' . $user->fname . '%')
                                  ->orWhere('name', 'like', '%' . $user->lname . '%');
                    
                    $fallbackQuery->orWhereHas('preEmploymentRecord', function($subQuery) use ($user, $userFullName) {
                        $subQuery->where('email', $user->email)
                                 ->orWhere('first_name', 'like', '%' . $user->fname . '%')
                                 ->orWhere('last_name', 'like', '%' . $user->lname . '%')
                                 ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $userFullName . '%']);
                    });
                });
            })
            ->with(['preEmploymentRecord', 'patient'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get annual physical examinations sent to this patient
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
