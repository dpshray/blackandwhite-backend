<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    //
    use InteractsWithMedia, HasEvents;
    use SoftDeletes;
    const MEDIA_NAME = 'product';
    const SIZE_DETAIL = 'SIZE_DETAIL';
    const MAIN_IMAGE = 'MAIN_IMAGE';
    protected $casts = [
        'price' => 'integer',
        'discount_price' => 'integer',
    ];
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'pattern',
        'fabric',
        'material',
        'product_code',
        'bestseller',
        'limited',
    ];
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'product_categories');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_NAME)
            // ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('image')->nonQueued();
            });
        $this->addMediaCollection(self::SIZE_DETAIL)->singleFile();
        $this->addMediaCollection(self::MAIN_IMAGE)->singleFile();
    }
    public function getDiscountPercentAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
        }
        return null;
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->product_code = self::generateUniqueCode();
        });
    }

    public static function generateUniqueCode()
    {
        do {
            // Generate a random 4-digit number
            $code = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('product_code', $code)->exists());
        return $code;
    }
}
