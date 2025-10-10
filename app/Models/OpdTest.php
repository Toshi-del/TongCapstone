<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OpdTest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'opd_tests';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_name',
        'customer_email',
        'medical_test',
        'appointment_date',
        'appointment_time',
        'price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'appointment_date' => 'date',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to filter by customer email
     */
    public function scopeForCustomer($query, $email)
    {
        return $query->where('customer_email', $email);
    }

    /**
     * Scope to filter upcoming appointments
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString());
    }

    /**
     * Scope to filter past appointments
     */
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', now()->toDateString());
    }

    /**
     * Scope to filter by appointment date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    /**
     * Scope to filter by appointment time
     */
    public function scopeForTime($query, $time)
    {
        return $query->where('appointment_time', $time);
    }

    /**
     * Get formatted appointment date
     */
    public function getFormattedDateAttribute()
    {
        return $this->appointment_date ? $this->appointment_date->format('F j, Y') : 'Date not set';
    }

    /**
     * Get formatted appointment time
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->appointment_time) {
            return 'Time not set';
        }

        try {
            return Carbon::createFromFormat('H:i', $this->appointment_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->appointment_time;
        }
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Check if appointment is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date && $this->appointment_date >= now()->toDateString();
    }

    /**
     * Get price category based on amount
     */
    public function getPriceCategoryAttribute()
    {
        if ($this->price <= 500) {
            return 'Basic';
        } elseif ($this->price <= 1000) {
            return 'Standard';
        } else {
            return 'Premium';
        }
    }

    /**
     * Get price category badge class
     */
    public function getPriceCategoryClassAttribute()
    {
        switch ($this->price_category) {
            case 'Basic':
                return 'bg-green-100 text-green-800';
            case 'Standard':
                return 'bg-yellow-100 text-yellow-800';
            case 'Premium':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get appointment group key for grouping by date and time
     */
    public function getGroupKeyAttribute()
    {
        return $this->appointment_date . '_' . $this->appointment_time;
    }

    /**
     * Static method to get appointments grouped by date and time
     */
    public static function getGroupedAppointments($customerEmail)
    {
        return static::forCustomer($customerEmail)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('group_key');
    }

    /**
     * Static method to get appointment statistics
     */
    public static function getStatistics($customerEmail)
    {
        $appointments = static::forCustomer($customerEmail)->get();
        $groupedAppointments = $appointments->groupBy('group_key');

        return [
            'total_appointments' => $groupedAppointments->count(),
            'total_tests' => $appointments->count(),
            'total_amount' => $appointments->sum('price'),
            'upcoming_appointments' => $appointments->where('is_upcoming', true)->count(),
        ];
    }
}
