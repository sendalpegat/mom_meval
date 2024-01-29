<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\meeting\ActionPlan;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Show list of task
     */
    public function index(Request $request)
    {
        $query = ActionPlan::query();
        if ($request->seachTerm != '')
        {
            $query = $query->where('mom_point_discussed.remark', 'like', '%'.$request->seachTerm.'%');
        }
        
        $tasks = $query
        ->select('mom_action_plan.*','mom_point_discussed.remark','mom_point_discussed.rate','core_user.name')
        ->leftJoin('mom_point_discussed', function($join)
                         {
                             $join->on('mom_point_discussed.mom_id', '=', 'mom_action_plan.mom_id');
                             $join->on('mom_point_discussed.line_number','=','mom_action_plan.point_discussed_index');
                         })
        ->leftjoin('core_user','core_user.email','=','mom_action_plan.pic')
        ->paginate(5);
        return view('meeting.list_task', compact('tasks'))->render();
    }   


    /**
     * Update status task to DONE
     */
    public function updateStatus(Request $request)
    {
        $id = $request->id;
        $index = $request->index;
        $lineNumber = $request->lineNumber;
        DB::beginTransaction();
        try 
        {
            $filter = ["mom_id" =>$id, "point_discussed_index"=> $index, "line_number"=>$lineNumber];
            //update status of task
            DB::table('mom_action_plan')->where($filter)->update(['status' =>DB::raw(ActionPlan::STATUS_DONE)]);
           
            DB::commit();

            return $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Update status of task successfull'
            ];
                    

        }catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Update status of task fail'
            ];
        }
    }

    public function getTotalTaskComplateAndUncomplate()
    {
        $result = DB::table('mom_action_plan')
            ->select('status', DB::raw('count(*) as jml'))
            ->groupBy('status')
            ->get();

        $done = 0;
        $onProgress = 0;
        if (count($result) > 0)
        {
            for ($i = 0; $i < count($result); $i++)
            {
                if ($result[$i]->status == ActionPlan::STATUS_DONE)
                {
                    $done = $result[$i]->jml;
                }
                else
                {
                    $onProgress = $result[$i]->jml;
                }
            }
        }

        $totalTask = array(ActionPlan::STATUS_DONE=>$done, 
                        ActionPlan::STATUS_ON_PROGRESS =>$onProgress);
        return $totalTask;
    }
}
