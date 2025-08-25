<?php

namespace App\Models;

use App\ValueObjects\FileSize;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
     * The attributes that should be guarded from mass assignment.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'email_verified_at',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'profile_photo_path',
        'upload_size_total',
        'role',
        'created_at',
        'updated_at',
        'deleted_at',
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
     * @return FileSize
     */
    public function getTotalUploadSize(): FileSize
    {
        // once caches the result of the closure, so it will only be calculated once per request
        // https://laravel.com/docs/12.x/helpers#method-once
        return once(
            function () {
                return FileSize::fromFiles(
                    $this->files
                );
            }
        );
    }

    /**
     * Update the upload_size_total column in the database
     * @return void
     */
    public function updateUploadSizeTotal(FileSize $totalUploadedSize): void
    {
        $this->upload_size_total = $this->getTotalUploadSize()->add($totalUploadedSize)->toBytes();
        $this->save();
    }

    /**
     * Returns the upload limit.
     *
     * @return FileSize
     */
    public function getUploadLimit(): FileSize
    {
        return new FileSize(UPLOAD_LIMIT);
    }

    /**
     * Check if the user can upload a file based on the upload limit and total upload size.
     * @param int $fileSize
     * @return bool
     */
    public function canUpload(int $fileSize): bool
    {
        return !$this->getTotalUploadSize()
            ->add(FileSize::fromBytes($fileSize))
            ->exceedsLimit($this->getUploadLimit());
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
