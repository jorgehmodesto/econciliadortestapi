<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Repositories
 * @package App
 */
class Repository extends Model
{

    /**
     * @var string
     */
    protected $table = 'repositories';

    /**
     * @var array
     */
    protected $fillable = [
        'owner_id', 'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commits()
    {
        return $this->hasMany(Commit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}