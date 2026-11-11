<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'article_category_id', 'title', 'slug', 'cover_image', 
        'content', 'is_published', 'focus_keyword', 'meta_title', 'meta_description'
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }
}