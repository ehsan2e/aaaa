<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeoConfig extends Model
{
    protected $fillable = ['og_title', 'og_description', 'og_site_name', 'og_type', 'og_image', 'twitter_site', 'twitter_title', 'twitter_description', 'twitter_creator', 'twitter_card', 'twitter_url', 'twitter_image',];
    protected $table = 'seo_configs';
}
