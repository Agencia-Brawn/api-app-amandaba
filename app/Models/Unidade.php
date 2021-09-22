<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reuniao;

class Unidade extends Model
{
    public function reunioes()
    {
        return $this->hasMany(Reuniao::class)->get();
    }
}
