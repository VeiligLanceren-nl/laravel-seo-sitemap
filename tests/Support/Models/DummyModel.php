<?php

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\Support\Models\Factories\DummyModelFactory;

class DummyModel extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'dummy_models';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    public $fillable = [
        'slug',
    ];

    /**
     * @return DummyModelFactory
     */
    protected static function newFactory(): DummyModelFactory
    {
        return DummyModelFactory::new();
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}