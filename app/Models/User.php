<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Appointment;
use App\Models\MedicalChecklist;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'mname',
        'email',
        'phone',
        'address',
        'birthday',
        'age',
        'company',
        'role',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'password',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->fname . ' ' . ($this->mname ? $this->mname . ' ' : '') . $this->lname);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is company
     */
    public function isCompany()
    {
        return $this->hasRole('company');
    }

    /**
     * Check if user is doctor
     */
    public function isDoctor()
    {
        return $this->hasRole('doctor');
    }

    /**
     * Get the medical checklist for OPD patients
     */
    public function medicalChecklist(): HasOne
    {
        return $this->hasOne(MedicalChecklist::class, 'user_id');
    }

    /**
     * Get all medical checklists for this user (for filtering)
     */
    public function medicalChecklists(): HasMany
    {
        return $this->hasMany(MedicalChecklist::class, 'user_id');
    }

    /**
     * Check if user is nurse
     */
    public function isNurse()
    {
        return $this->hasRole('nurse');
    }

    /**
     * Check if user is patient
     */
    public function isPatient()
    {
        return $this->hasRole('patient');
    }

    /**
     * Check if user is radtech
     */
    public function isRadTech()
    {
        return $this->hasRole('radtech');
    }

    /**
     * Check if user is radiologist
     */
    public function isRadiologist()
    {
        return $this->hasRole('radiologist');
    }

    /**
     * Check if user is ECG technician
     */
    public function isEcgTech()
    {
        return $this->hasRole('ecgtech');
    }

    /**
     * Check if user is phlebotomist
     */
    public function isPlebo()
    {
        return $this->hasRole('plebo');
    }

    /**
     * Check if user is pathologist
     */
    public function isPathologist()
    {
        return $this->hasRole('pathologist');
    }

    /**
     * Check if user is OPD
     */
    public function isOpd()
    {
        return $this->hasRole('opd');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }



    /**
     * Get the user's appointments
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    /**
     * Get appointments where the user is a patient
     */
    public function patientAppointments()
    {
        return Appointment::whereHas('patients', function($query) {
            $query->where('email', $this->email)
                  ->orWhere(function($q) {
                      $q->where('first_name', $this->fname)
                        ->where('last_name', $this->lname);
                  });
        });
    }

    /**
     * Get pre-employment records for the user by email
     */
    public function preEmploymentRecords()
    {
        return \App\Models\PreEmploymentRecord::where('email', $this->email);
    }

    /**
     * Get pre-employment examination results for the user
     */
    public function preEmploymentExaminations()
    {
        return \App\Models\PreEmploymentExamination::whereHas('preEmploymentRecord', function($query) {
            $query->where('email', $this->email);
        });
    }

    /**
     * Get annual physical examination results for the user
     */
    public function annualPhysicalExaminations()
    {
        return \App\Models\AnnualPhysicalExamination::whereHas('patient', function($query) {
            $query->where('email', $this->email);
        });
    }

    /**
     * Get OPD examination for the user
     */
    public function opdExamination()
    {
        return $this->hasOne(\App\Models\OpdExamination::class);
    }

    /**
     * Get drug test results as patient
     */
    public function drugTestResults(): HasMany
    {
        return $this->hasMany(\App\Models\DrugTestResult::class, 'user_id');
    }

    /**
     * Get drug test results conducted by this nurse
     */
    public function conductedDrugTests(): HasMany
    {
        return $this->hasMany(\App\Models\DrugTestResult::class, 'nurse_id');
    }
}
