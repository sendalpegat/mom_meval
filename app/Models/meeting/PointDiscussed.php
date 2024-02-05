<?php

namespace App\Models\meeting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class PointDiscussed extends Model
{
    use HasFactory, Sortable;

    // sortable 
    public $sortable = ['remark'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mom_meeting';

    protected $primaryKey = 'mom_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mom_id',
        'topic',
        'agenda',
        'location',
        'mom_date',
        'start_time',
        'end_time',
        'duration',
        'updated_by',
        'note',
      ];

      /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mom_date'=> 'datetime:Y-m-d',
        'start_time' => 'datetime:H:i:s',  
        'end_time' => 'datetime:H:i:s',
      ];
}
