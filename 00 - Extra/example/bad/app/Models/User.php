<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

//will be replaced when implementing subscriptions
const UPLOAD_LIMIT = 419_430_400;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phonenumber',
        'company_name',
        'company_website',
        'job_title',
        'password',
        'organisation_id',
        'verified',
        'used_ip_addresses',
        'last_control_id',
        'subscription_id'
    ];

    protected $with = [
        'activeSubscription',
        'organisation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'profile_photo_url',
        'created_at',
        'updated_at',
        'two_factor_confirmed_at',
        'email_verified_at',
        'used_ip_addresses'
    ];

    /**
     * Return attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'used_ip_addresses' => 'array'
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get total file size uploaded by user
     *
     * @return integer
     */
    public function getTotalUploadSize(): int
    {
        return $this->files->reduce(function (int $carry, File $file) {
            return $carry + Storage::disk('local')->size($file->path);
        }, 0);
    }

    /**
     * Update the upload_size_total column in the database
     * @return void
     */
    public function updateUploadSizeTotal(int $totalUploadedSize): void
    {
        $this->upload_size_total = $this->getTotalUploadSize() + $totalUploadedSize;
        $this->save();
    }

    /**
     * Returns the upload limit.
     *
     * @return integer
     */
    public function getUploadLimit(): int
    {
        return UPLOAD_LIMIT;
    }

    public function webDomains(): HasMany
    {
        return $this->hasMany(WebDomain::class);
    }

    public function webscanEntries(): HasMany
    {
        return $this->hasMany(WebscanEntry::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class);
    }
}
