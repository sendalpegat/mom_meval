<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\meeting\Meeting;
use App\Models\meeting\ActionPlan;
use App\Models\User;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RootController;
use App\Http\Controllers\UserController;

class MeetingController extends RootController
{
    const OBJECT_NAME = "Meeting";

    const VIEW_MODE_ADD = 0;
    const VIEW_MODE_UPDATE = 1;
    /** show view list of meeting */
    public function index(Request $request)
    {

        //get the task that already done and still on progress
        $task = (new TaskController)->getTotalTaskComplateAndUncomplate();

        $departmentIds = (new UserController)->getDepartments();

        //get the list of meeting
        $query = Meeting::query();
        if ($request->seachTerm != '')
        {
            $query = $query->where('topic', 'like', '%'.$request->seachTerm.'%');
        }
        
        //cek if user as manager filter by devision,created by
        if (Auth::user()->role != User::ADMIN)
        {
            $query->whereIn('mom_id', function($query)
            {
                $filterParticipants = ["devision_id" =>Auth::user()->devision_id];
                $query->select("mom_participants.mom_id")
                      ->from('mom_participants');
                
                if (Auth::user()->role == User::MANAGER)
                    $query->leftjoin('core_user','core_user.email','=','mom_participants.email');
                else
                    $filterParticipants = ["mom_participants.email" =>Auth::user()->email];   
                    
                $query->where($filterParticipants);
            });
            if (Auth::user()->role == User::MANAGER)
            {
                $query->orWhereIn('updated_by', function($query)
                {
                    $query->select("email")
                      ->from('core_user')
                      ->where("devision_id",Auth::user()->devision_id);
                });
            }
            else
                $query->orWhere("updated_by",Auth::user()->email);
            
        }
        else
        {
            if ($request->seachDeparment != '')
            {
                $query = $query->where('devision_id',$request->seachDeparment);
            } 
        }
        $request->flash();

        $meetings = $query->select('mom_meeting.*','core_user.name', 'core_user.devision_id')
        ->leftjoin('core_user','core_user.email','=','mom_meeting.updated_by')
        ->sortable(['created_at' => 'desc'])->paginate(10);

        $data = array ("meetings"=>$meetings, "task"=>$task, "departmentIds"=>$departmentIds);
        return view('meeting.list_meeting', compact('data'))->render();
    }

    /** save meeting to database
     * @param request
     */
    public function store(Request $request)
    {
        $res = "failed";
        $msg = "Failed saving";
        DB::beginTransaction();
        try 
        {
            
            $request->validate([
                'topic' => 'required',
                'location' => 'required',
                'partisipans' => 'required',
                'listPointDiscusseds' => 'required',
                'listTask'=>'required'
            ]);
        
            // generate id
            $prefix = $year = date("Ymd");
            $momId = $this->generateId(self::OBJECT_NAME,$prefix);

            $meeting = array(
                            "mom_id" => $momId,
                            "topic"=> $request->get('topic',''),
                            "location" => $request->get('location',''),
                            "mom_date"=> $request->get('momDate'),
                            "start_time"=> $request->get('startTime'),
                            "end_time"=> $request->get('endTime'),
                            "duration"=> $request->get('duration'),
                            "updated_by" => Auth::user()->email);

            //save meeting to database                
            Meeting::create($meeting);

            //save point discussed
            foreach ($request->get('listPointDiscusseds') as $point) 
            {
                DB::table('mom_point_discussed')->insert(
                    array('mom_id' => $momId,
                          'line_number' => $point["lineNumber"],
                          'remark' => $point["remark"],
                          'rate' => $point["rate"])
                );
            }

            //save task
            foreach ($request->get('listTask') as $task) 
            {
                DB::table('mom_action_plan')->insert(
                    array('mom_id' => $momId,
                          'point_discussed_index' => $task["index"],
                          'line_number' => $task["lineNumber"],
                          'note' => $task["notes"],
                          'due_date' => $task["dueDate"],
                          'pic' => $task["pic"])
                );
            }

            //save participants
            $partisipants = $request->get('partisipans');
            $countPartisipants = count($request->get('partisipans'));
            for ($i = 0; $i < $countPartisipants; $i++) 
            {
                DB::table('mom_participants')->insert(
                    array('mom_id' => $momId,
                          'email' => $partisipants[$i])
                );
            }

            $res = "success";
            $msg = 'Meeting saved successfully.';

            DB::commit();

            redirect('meeting');
            return $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Saved successfull'
            ];

        }catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Saved fail'
            ];
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            DB::rollback();
            Log::error($ex->getMessage());
            $errorMessage = $this->getErrorMessage($ex->getCode());
            return $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Saved fail. ( '.$errorMessage.' )'
            ];
        }
    }

    private function generateId($objectName,$prefix='')
    {
        //get last counter
        $id = 1;
        $filter = ["object" =>$objectName, "prefix"=> $prefix];
        $result = DB::table('core_counter')
            ->select('counter')
            ->where($filter)
            ->get();
             
        if (count($result) == 0)
        {
            //add counter
            DB::table('core_counter')->insert(
                array('object' => $objectName,
                      'prefix' => $prefix,
                      'counter' => $id)
            );
        }
        else
        {
            $id = $result[0]->counter + 1;
            
            //update counter
            DB::table('core_counter')->where($filter)->update(['counter' =>DB::raw($id)]);
        }

        $formid = $prefix.''.$id;
        return $formid;
    }

     /**
     * Show the form for creating a new meeting.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $data = array("viewMode" => self::VIEW_MODE_ADD, "users"=>$users);
        
        return view('meeting.editor', compact('data'));
    }

    /** show meeting by id
     * @param request
     */
    public function show($id)
    {
        $users = User::all();
        $meeting = Meeting::find($id);

        //participants
        $participants = DB::table('mom_participants')
        ->select('email')
        ->where("mom_id", $id)
        ->get();

        //get point discuss
        $pointDiscuss = DB::table('mom_point_discussed')
            ->select('*')
            ->where("mom_id", $id)
            ->get();

        //get task
        $tasks = DB::table('mom_action_plan')
            ->leftJoin('core_user', 'mom_action_plan.pic', '=', 'core_user.email')
            ->where("mom_id", $id)
            ->get();
        $data = array("viewMode" => self::VIEW_MODE_UPDATE, "users"=>$users, 
                "meeting"=>$meeting, "pointDiscuss"=> $pointDiscuss,
                "participants"=>$participants,"tasks"=>$tasks);

        return view('meeting.editor', compact('data'));
    }

    /**
     * Update the meeting
     * @param request 
     */
    public function update(Request $request)
    {
        $id = $request->get('id','');
        $res = "failed";
        $msg = "Failed saving";
        DB::beginTransaction();
        try 
        {
            //save meeting to database
            $meeting = Meeting::find($id);
            $meeting->topic= $request->get('topic','');
            $meeting->location = $request->get('location','');
            $meeting->mom_date = $request->get('momDate');
            $meeting->start_time = $request->get('startTime');
            $meeting->end_time = $request->get('endTime');
            $meeting->duration = $request->get('duration');
            $meeting->updated_by = Auth::user()->email;
            $meeting->update();                

            //delete mom discussed first
            DB::table("mom_point_discussed")->where("mom_id",$id)->delete();

            //save point discussed
            foreach ($request->get('listPointDiscusseds') as $point) 
            {
                DB::table('mom_point_discussed')->insert(
                    array('mom_id' => $id,
                          'line_number' => $point["lineNumber"],
                          'remark' => $point["remark"],
                          'rate' => $point["rate"])
                );
            }

            //delete mom action plan first
            DB::table("mom_action_plan")->where("mom_id",$id)->delete();

            //save task
            foreach ($request->get('listTask') as $task) 
            {
                DB::table('mom_action_plan')->insert(
                    array('mom_id' => $id,
                          'point_discussed_index' => $task["index"],
                          'line_number' => $task["lineNumber"],
                          'note' => $task["notes"],
                          'due_date' => $task["dueDate"],
                          'pic' => $task["pic"])
                );
            }

            //save participants
            $partisipants = $request->get('partisipans');
            $countPartisipants = count($request->get('partisipans'));
            for ($i = 0; $i < $countPartisipants; $i++) 
            {
                DB::table('mom_participants')->insert(
                    array('mom_id' => $id,
                          'email' => $partisipants[$i])
                );
            }

            $res = "success";
            $msg = 'Meeting saved successfully.';

            DB::commit();

            return $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Saved successfull'
            ];

        }catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Saved fail'
            ];
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            DB::rollback();
            Log::error($ex->getMessage());
            $errorMessage = $this->getErrorMessage($ex->getCode());
            return $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Saved fail. ( '.$errorMessage.' )'
            ];
        }
    }

    /**
     * Delete the meeting 
     * @param id the id of meeting
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        DB::beginTransaction();
        try 
        {
            //delete mom discussed first
            DB::table("mom_point_discussed")->where("mom_id",$id)->delete();

            //delete mom action plan first
            DB::table("mom_action_plan")->where("mom_id",$id)->delete();

            $delete =  Meeting::destroy($id);
            if ($delete)
            {
                
                DB::commit();

                return $response = [
                    'status'=>'ok',
                    'success'=>true,
                    'message'=>'Deleted successfull'
                ];
            }
            else
            {
                DB::rollback();

                return $response = [
                    'status'=>'ok',
                    'success'=>false,
                    'message'=>'Deleted fail'
                ];
            }            

        }catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Deleted fail'
            ];
        }
        catch(\Illuminate\Database\QueryException $ex)
        {
            DB::rollback();
            Log::error($ex->getMessage());
            $errorMessage = $this->getErrorMessage($ex->getCode());
            return $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Deleted fail. ( '.$errorMessage.' )'
            ];
        }
    }
}
