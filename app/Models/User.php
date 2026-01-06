<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'profie_rating_status',
        'height',
        'weight',
        'body_type',
        'hair_color',
        'eye_color',
        'nationality',
        'region',
        'city',
        'sexual_orientation',
        'education',
        'field_of_work',
        'relationship_status',
        'zodiac_sign',
        'smoking',
        'drinking',
        'tattoos',
        'piercings',
        'about_me',
        'about_match',
        'deleted_at',
        'deleted_by',
        'deletion_type',
        'is_fake',
        'is_active',
        'show_on_mobile',
        'children',
        'exercise',
        'pets',
        'income_level'
    ];


    public function images()
    {
        return $this->hasMany(UserImage::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(UserImage::class, 'user_id', 'id')
            ->where(function ($q) {
                $q->where(function ($x) {
                    $x->where('type', 0)
                        ->where('profile_image_approval', 1);
                })
                    ->orWhere(function ($x) {
                        $x->where('type', 3);
                    });
            })
            ->orderByRaw("
                CASE
                    WHEN type = 0 AND profile_image_approval = 1 THEN 1
                    WHEN type = 3 THEN 2
                END
            ");
    }


    public function bestImage()
    {
        return $this->hasOne(UserImage::class, 'user_id', 'id')
            ->where('type', 3);
    }




    public function receivedRatings()
    {

        return $this->hasMany(UserRate::class, 'reciever_id');
    }

    public function intrest()
    {

        return $this->hasOne(UserIntrest::class, 'user_id', 'id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAdminApprovedAttribute()
    {
        return (int) $this->getRawOriginal('admin_status') === 1;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'admin_status' => 'bool'
        ];
    }
}
