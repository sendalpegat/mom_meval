<?php

namespace App\Models\meeting;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionPlan extends Model
{
    use HasFactory;
    /** Constans status ON_PROGRESS */
    const STATUS_ON_PROGRESS = 0;
    /** Constans status DONE */
    const STATUS_DONE = 1;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mom_action_plan';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mom_id',
        'line_number',
        'point_discussed_index',
        'pic',
        'due_date',
        'status',
      ];

      /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date'=> 'datetime:Y-m-d',
      ];

    public static function getStatusName($status)
    {
      $nameStatus = "Unknown";
      switch ($status)
      {
        case self::STATUS_ON_PROGRESS :
          $nameStatus = "On Progress";
          break;
        case self::STATUS_DONE :
          $nameStatus = "Done";
          break;
      }

      return $nameStatus;
    }  
}
