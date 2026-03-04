<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array{
        return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        ];
    }

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper method
    public function isAdmin(){
        return $this->role === 'admin';
    }
    
    // Helper method to check if email is verified
    public function hasVerifiedEmail(){
        return !is_null($this->email_verified_at);
    }
}