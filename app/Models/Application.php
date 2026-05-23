<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
      use HasFactory,HasPublicId;

            protected $fillable = [
        'public_id',
        'student_id',
        'application_code',
        'university_major_id',
        'user_id',
        'status',
        'is_active'
    ];
        protected $hidden = ['id'];


    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
    // Status constants
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELED = 'canceled';

    // Allowed transitions mapping
    protected static array $allowedTransitions = [
        self::STATUS_PROCESSING => [self::STATUS_ACCEPTED, self::STATUS_REJECTED],
        self::STATUS_ACCEPTED => [self::STATUS_REGISTERED],
        self::STATUS_REGISTERED => [],
        self::STATUS_REJECTED => [],
        self::STATUS_CANCELED => [],
    ];

        public function student()
    {
        return $this->belongsTo(Student::class);
    }

        public function universityMajor()
    {
        return $this->belongsTo(UniversityMajor::class);
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

       /**
     * Check whether the status can be changed to the given value.
     */
    public function canChangeStatusTo(string $newStatus): bool
    {
        // If record is not active it cannot change state
        if (! $this->is_active) {
            return false;
        }

        $current = $this->status;

        $allowed = static::$allowedTransitions[$current] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    /**
     * Change status if allowed; returns true on success.
     */
    public function changeStatus(string $newStatus): bool
    {
        if (! $this->canChangeStatusTo($newStatus)) {
            return false;
        }

        // Apply side-effects according to rules
        if (in_array($newStatus, [self::STATUS_REJECTED, self::STATUS_CANCELED], true)) {
            $this->is_active = false; // closed on rejected/canceled
        }

        // registered does not change is_active (stays true)

        $this->status = $newStatus;

        return $this->save();
    }

    protected static function booted(): void
    {
        static::saving(function (Application $model) {
            // If status is being changed, validate transition
            if ($model->isDirty('status')) {
                $new = $model->status;

                // if current is null (new model) allow default 'processing' via DB; otherwise validate
                $original = $model->getOriginal('status');

                // if model is newly created, skip validation here
                if (! is_null($original)) {
                    $allowed = static::$allowedTransitions[$original] ?? [];

                    if (! in_array($new, $allowed, true)) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'الانتقال إلى هذه الحالة غير مسموح.']);
                    }
                }

                // If transitioning to rejected/canceled ensure is_active will be false
                if (in_array($new, [self::STATUS_REJECTED, self::STATUS_CANCELED], true)) {
                    $model->is_active = false;
                }

                // Prevent setting is_active = false for states other than rejected/canceled
                if ($model->isDirty('is_active') && $model->is_active === false) {
                    $state = $model->status;
                    if (! in_array($state, [self::STATUS_REJECTED, self::STATUS_CANCELED], true)) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['is_active' => 'لا يمكن تعطيل الطلب إلا إذا كانت الحالة rejected أو canceled.']);
                    }
                }
            }
        });
    }
}
