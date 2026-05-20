<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SalesPrediction;

class Book extends Model{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'title',
        'author',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
        'pages',
        'publisher',
        'language',
        'published_year',
        // AI columns
        'ai_audio_description',
        'ai_description_at',
    ];
    protected $casts = [
        'ai_description_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
        public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    // Accessor for average rating
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;

    }

    public function salesPrediction()
    {
        return $this->hasOne(SalesPrediction::class);
    }
}