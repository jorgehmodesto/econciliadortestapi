<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Owner
 * @package App
 */
class Owner extends Model
{
    /**
     * @var string
     */
    protected $table = 'owners';

    /**
     * @var array
     */
    protected $fillable = ['username'];

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repositories()
    {
        return $this->hasMany(Repository::class);
    }
}