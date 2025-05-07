<?php

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;

class DummyModel extends Model
{
    protected $table = 'dummy_models';
    public $timestamps = false;
}