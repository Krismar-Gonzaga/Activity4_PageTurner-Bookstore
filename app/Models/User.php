<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\PasswordResetNotification;
use App\Notifications\EmailVerificationNotification;

class User extends Authenticatable{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'two_factor_enabled',
        'two_factor_type',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_method',
        'two_factor_confirmed_at',
        'two_factor_email',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array{
        return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
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

    

    /**
    * Get the notifications for the user.
    */
    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get the read notifications for the user.
     */
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    /**
     * Get the unread notifications for the user.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    // Helper method
    public function isAdmin(){
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    // Check if user has purchased a specific book
    public function hasPurchasedBook($bookId)
    {
        return $this->orders()
            ->whereHas('items', function($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->where('status', 'delivered') // or 'delivered' depending on your order status
            ->exists();
    }
    
    public function purchasedBooks()
    {
        return Book::whereIn('id', function($query) {
            $query->select('book_id')
                ->from('order_items')
                ->whereIn('order_id', function($q) {
                    $q->select('id')
                        ->from('orders')
                        ->where('user_id', $this->id)
                        ->whereIn('status', ['delivered', 'completed']);
                });
        });
    }

    // Helper method to check if email is verified
    public function hasVerifiedEmail(){
        return !is_null($this->email_verified_at);
    }

    public function twoFactorAuthentication(){
        return $this->hasOne(TwoFactorAuthentication::class);

    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    // Password reset notification override
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    // 2FA Helpers
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled;
    }

    public function twoFactorType()
    {
        return $this->two_factor_type;
    }

    // Get the user's initials
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2); // Max 2 characters
    }

    // Get profile picture URL
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture && file_exists(public_path('storage/' . $this->profile_picture))) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar based on initials using UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=8B4513&background=D2691E';
    }

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Scope for admins
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Scope for customers
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    // Get total spent by user
    public function getTotalSpentAttribute()
    {
        return $this->orders()
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('total_amount');
    }

    // Get order count
    public function getOrderCountAttribute()
    {
        return $this->orders()->count();
    }

    // Check if user can review a book
    public function canReviewBook($bookId)
    {
        // User must have purchased the book and not already reviewed it
        return $this->hasPurchasedBook($bookId) && 
               !$this->reviews()->where('book_id', $bookId)->exists();
    }

    // Route notifications for mail channel
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    // Route notifications for database channel
    public function routeNotificationForDatabase($notification)
    {
        return $this->notifications();
    }

    public function getTwoFactorMethodDisplayAttribute(): string
    {
        return match($this->two_factor_method) {
            'app' => 'Authenticator App',
            'email' => 'Email OTP',
            default => 'Disabled',
        };
    }
}