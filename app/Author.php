<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Authors
 * @package App
 */
class Author extends Model
{
    protected $table = 'authors';

    protected $fillable = [
        'username', 'name', 'github_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commits()
    {
        return $this->hasMany(Commit::class);
    }
}