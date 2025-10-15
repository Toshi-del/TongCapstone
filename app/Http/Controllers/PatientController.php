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
        
        // Get all pre-employment examinations for this patient
        // Show only examinations that have been sent to the patient
        // Check both patient relationship (User model) and preEmploymentRecord relationship
        $preEmploymentResults = PreEmploymentExamination::where(function($query) use ($user) {
                // Check patient relationship (patient_id -> users table)
                $query->whereHas('patient', function($q) use ($user) {
                    $q->where('email', $user->email);
                })
                // OR check preEmploymentRecord relationship
                ->orWhereHas('preEmploymentRecord', function($q) use ($user) {
                    $q->where('email', $user->email);
                });
            })
            ->whereIn('status', ['sent_to_patient', 'sent_to_both'])
            ->with(['preEmploymentRecord', 'patient'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get all annual physical examinations for this patient
        // Show only examinations that have been sent to the patient
        $annualPhysicalResults = AnnualPhysicalExamination::where(function($query) use ($user, $userFullName) {
                // Match by patient email
                $query->whereHas('patient', function($q) use ($user) {
                    $q->where('email', $user->email);
                })
                // OR match by patient name (for cases where email might not be set)
                ->orWhere(function($q) use ($userFullName) {
                    $q->where('name', $userFullName);
                })
                // OR match by user_id directly (if it references users table)
                ->orWhere('user_id', $user->id);
            })
            ->whereIn('status', ['sent_to_patient', 'sent_to_both'])
            ->with(['patient'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Debug logging - Check what examinations exist for this user
        $allAnnualPhysicalExams = AnnualPhysicalExamination::with(['patient'])
            ->whereIn('status', ['sent_to_patient', 'sent_to_both'])
            ->get();
        
        $debugInfo = [
            'user_email' => $user->email,
            'user_name' => $userFullName,
            'user_id' => $user->id,
            'pre_employment_count' => $preEmploymentResults->count(),
            'annual_physical_count' => $annualPhysicalResults->count(),
            'total_sent_annual_exams' => $allAnnualPhysicalExams->count(),
            'all_sent_exams' => $allAnnualPhysicalExams->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'status' => $exam->status,
                    'user_id' => $exam->user_id,
                    'patient_id' => $exam->patient_id,
                    'patient_email' => $exam->patient ? $exam->patient->email : null,
                    'patient_name' => $exam->patient ? ($exam->patient->first_name . ' ' . $exam->patient->last_name) : null,
                ];
            })->toArray()
        ];
        
        \Log::info('Patient Medical Results Query - Detailed Debug', $debugInfo);
        
        return view('patient.medical-results', compact('preEmploymentResults', 'annualPhysicalResults'));
    }

    /**
     * View specific pre-employment examination result
     */
    public function viewPreEmploymentResult($id)
    {
        $user = Auth::user();
        
        $examination = PreEmploymentExamination::where('id', $id)
            ->where(function($query) use ($user) {
                // Check patient relationship (patient_id -> users table)
                $query->whereHas('patient', function($q) use ($user) {
                    $q->where('email', $user->email);
                })
                // OR check preEmploymentRecord relationship
                ->orWhereHas('preEmploymentRecord', function($q) use ($user) {
                    $q->where('email', $user->email);
                });
            })
            ->whereIn('status', ['sent_to_patient', 'sent_to_both'])
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
            ->whereHas('patient', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->whereIn('status', ['sent_to_patient', 'sent_to_both'])
            ->with(['patient', 'drugTestResults'])
            ->firstOrFail();
        
        return view('patient.view-annual-physical-result', compact('examination'));
    }
}
