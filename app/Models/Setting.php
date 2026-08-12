<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['key', 'value', 'group'])]
class Setting extends \Illuminate\Database\Eloquent\Model
{
}
