<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'message',
        'data',
        'priority',
        'is_read',
        'read_at',
        'triggered_by_type',
        'triggered_by_id',
        'triggered_by_name',
        'related_type',
        'related_id',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity (User, Admin, etc.)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the related record (Appointment, PreEmploymentRecord, etc.)
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific priority
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for notifications for specific user type (admin, nurse, etc.)
     */
    public function scopeForUserType($query, $userType)
    {
        return $query->where('notifiable_type', $userType);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get priority icon for UI
     */
    public function getPriorityIconAttribute()
    {
        return match($this->priority) {
            'high' => 'fa-exclamation-triangle',
            'medium' => 'fa-info-circle',
            'low' => 'fa-check-circle',
            default => 'fa-bell',
        };
    }

    /**
     * Get notification type icon
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'appointment_created' => 'fa-calendar-plus',
            'pre_employment_created' => 'fa-user-plus',
            'annual_physical_created' => 'fa-heartbeat',
            'new_examination' => 'fa-file-medical',
            'checklist_completed' => 'fa-check-square',
            'checklist_submitted' => 'fa-clipboard-check',
            'checklist_submitted_confirmation' => 'fa-check-circle',
            'results_updated' => 'fa-edit',
            'results_updated_confirmation' => 'fa-check-circle',
            'submitted_to_admin' => 'fa-paper-plane',
            'lab_exam_completed' => 'fa-flask',
            'xray_completed' => 'fa-x-ray',
            'ecg_completed' => 'fa-heartbeat',
            'pathologist_report_submitted' => 'fa-microscope',
            'patient_registered' => 'fa-user-check',
            'examination_updated' => 'fa-edit',
            'system_alert' => 'fa-exclamation-circle',
            // Company notifications
            'appointment_submitted' => 'fa-calendar-check',
            'pre_employment_submitted' => 'fa-briefcase',
            'opd_submitted' => 'fa-hospital',
            'results_ready' => 'fa-file-medical-alt',
            'examination_sent' => 'fa-paper-plane',
            default => 'fa-bell',
        };
    }

    /**
     * Create notification for admin users
     */
    public static function createForAdmin($type, $title, $message, $data = null, $priority = 'medium', $triggeredBy = null, $relatedModel = null)
    {
        // Get all admin users
        $adminUsers = User::where('role', 'admin')->get();
        
        foreach ($adminUsers as $admin) {
            static::create([
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'priority' => $priority,
                'triggered_by_type' => $triggeredBy ? get_class($triggeredBy) : null,
                'triggered_by_id' => $triggeredBy?->id,
                'triggered_by_name' => $triggeredBy?->name ?? $triggeredBy?->first_name . ' ' . $triggeredBy?->last_name ?? 'System',
                'related_type' => $relatedModel ? get_class($relatedModel) : null,
                'related_id' => $relatedModel?->id,
            ]);
        }
    }

    /**
     * Create notification for specific user
     */
    public static function createForUser($user, $type, $title, $message, $data = null, $priority = 'medium', $triggeredBy = null, $relatedModel = null)
    {
        return static::create([
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'triggered_by_type' => $triggeredBy ? get_class($triggeredBy) : null,
            'triggered_by_id' => $triggeredBy?->id,
            'triggered_by_name' => $triggeredBy?->name ?? $triggeredBy?->first_name . ' ' . $triggeredBy?->last_name ?? 'System',
            'related_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_id' => $relatedModel?->id,
        ]);
    }

    /**
     * Create notification for users with specific role
     */
    public static function createForRole($role, $type, $title, $message, $data = null, $priority = 'medium', $triggeredBy = null, $relatedModel = null)
    {
        $users = User::where('role', $role)->get();
        
        foreach ($users as $user) {
            static::createForUser($user, $type, $title, $message, $data, $priority, $triggeredBy, $relatedModel);
        }
    }

    /**
     * Get count of unread notifications for user
     */
    public static function getUnreadCountForUser($user)
    {
        return static::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecentForUser($user, $limit = 10)
    {
        return static::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
