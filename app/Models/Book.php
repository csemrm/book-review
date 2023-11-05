<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
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

    public function scopePopular(Builder $query, $from=null, $to=null): Builder| QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dataRangeFilter($q, $from, $to)
            ])
            ->orderByDesc("reviews_count");

    }

    public function scopeHighestRated(Builder $query, $from=null, $to=null): Builder| QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dataRangeFilter($q, $from, $to)
        ], 'rating')
        ->orderByDesc('reviews_avg_rating');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder{
        return $query->having('reviews_count','>=', $minReviews);
    }
    private function dataRangeFilter(Builder $query, $from =null, $to = null){
        if($from && !$to){
            $query->where('created_at', '>=' ,$from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

    }
}
