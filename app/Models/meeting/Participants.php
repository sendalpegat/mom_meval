<?php

namespace App\Models\meeting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participants extends Model
{
    use HasFactory;

    /** Constans for status ABSENCE */
    const STATUS_ABSENCE = 0;
    /** Constans for status ABSENCE */
    const STATUS_ATTEND = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mom_participans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mom_id',
        'email',
        'status',
      ];
}
