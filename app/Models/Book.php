<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    public function reviews(){
        return $this->hasMany(Review::class);
    }
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where("title","like","%". $title ."%");
    }
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount("reviews")
            ->orderByDesc("reviews_count");

    }
    public function scopeHighestRated(Builder $query): Builder
    {
        return $query->withAvg("reviews",'rating')
        ->orderByDesc('reviews_avg_rating');
    }
}
