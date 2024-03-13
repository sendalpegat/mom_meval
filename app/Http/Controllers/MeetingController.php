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
use Spatie\GoogleCalendar\Event;
use Illuminate\Support\Carbon;

class MeetingController extends RootController
{
    const OBJECT_NAME = "Meeting";

   
    /** show view list of meeting */
    public function index(Request $request)
    {
        $departmentIds = (new UserController)->getDepartments();

        //get the list of meeting
        $queryStatus = DB::table('mom_action_plan')
                   ->select('mom_id', DB::raw('SUM( case when STATUS = 1 then 1 else 0 END) AS done'),
                            DB::raw('COUNT(*) AS total'))
                   ->groupBy('mom_id');

        $query = Meeting::query();
        $query = $query->select('mom_meeting.*','user.name as updated_by_name'
                                ,'user2.name as created_by_name', 'user.devision_id',
                                DB::raw('case when action_plan.done = action_plan.total then 1 ELSE 0 end AS status'))
        ->leftjoin('core_user as user','user.id','=','mom_meeting.updated_by')
        ->leftjoin('core_user as user2','user2.id','=','mom_meeting.created_by')
        ->joinSub('select mom_id,SUM( case when STATUS = 1 then 1 else 0 END) done, COUNT(*) AS total from mom_action_plan group by mom_id', 
                'action_plan', 'action_plan.mom_id', '=', 'mom_meeting.mom_id', 'left');
        // ->joinSub($queryStatus, 'action_plan ', function ($join) {
        //     $join->on('mom_meeting.mom_id', '=', 'action_plan.mom_id');
        // });

        if ($request->seachTerm != '')
        {
            $query = $query->where('topic', 'like', '%'.$request->seachTerm.'%');
        }

        //cek if user as manager filter by devision,created by
        $listUsers = array();
        $userIds = array();
        if (Auth::user()->role != User::ADMIN)
        {
            $listUsers = array();
            $userIds = (new UserController)->getListUserByParent(Auth::user()->email);
            foreach($userIds as $userEmail)
            {
                array_push($listUsers, $userEmail->id);
            }
            $userId = $request->user;
            $query->where(function ($query) use ($listUsers) {
                $query->whereIn('mom_meeting.mom_id', function($query) use ($listUsers)
                {
                    $query->select("mom_id")
                          ->from('mom_participants')
                        ->whereIn("user_id", $listUsers);
                })
                ->orWhereIn('updated_by', $listUsers)
                ->orWhereIn('created_by', $listUsers);
            });
            if ($request->seachUser != '')
            {
                $userId = $request->seachUser;
                $query->where(function ($query) use ($userId) {
                    $query->where('updated_by', $userId)
                    ->orWhere('created_by', $userId);
                });
            } 
        }
        else
        {
            $userIds = (new UserController)->getAllListUsersThatActive();
            if ($request->seachDeparment != '')
            {
                $query = $query->where('user.devision_id',$request->seachDeparment);
            } 

            if ($request->seachUser != '')
            {
                $userId = $request->seachUser;
                $query->where(function ($query) use ($userId) {
                    $query->where('updated_by', $userId)
                    ->orWhere('created_by', $userId);
                });
            } 
        }
        $request->flash();

        $meetings = $query->sortable(['created_at' => 'desc'])->paginate(10);

        //get the task that already done and still on progress
        $task = (new TaskController)->getTotalTaskComplateAndUncomplateByUser($listUsers);

        $data = array ("meetings"=>$meetings, "task"=>$task, "departmentIds"=>$departmentIds,
            "users"=> $userIds);
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
                            "created_by" => Auth::user()->email,
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
            $idPartcipants = array();
            for ($i = 0; $i < $countPartisipants; $i++) 
            {
                DB::table('mom_participants')->insert(
                    array('mom_id' => $momId,
                          'user_id' => $partisipants[$i])
                );

                array_push($idPartcipants, $partisipants[$i]);
            }

            $res = "success";
            $msg = 'Meeting saved successfully.';

            //create google calendar
            //get the email of particpants
            $emailParticpants =  (new UserController)->getEmailUserbyListId($idPartcipants);
            $event = new Event();
            $event->name = $request->get('topic','');
            $startTime = $request->get('momDate')." ".$request->get('startTime');
            $endTime = $request->get('momDate')." ".$request->get('endTime');
            $event->startDateTime = Carbon::parse($startTime);
            $event->endDateTime = Carbon::parse($endTime);
            for ($i = 0; $i < $countPartisipants; $i++) 
            {
                if ($emailParticpants[$partisipants[$i]] != null)
                {
                    $email = $emailParticpants[$partisipants[$i]];
                    if ($email != '')
                        $event->addAttendee(['email' => $email]);
                }
                
            }
            $newEvent = $event->save();
            $idCalendar = $newEvent->id;

            //update id calendar
            DB::table('mom_meeting')->where(["mom_id"=>$momId])->update(['calendar_id' =>$idCalendar]);
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
        ->select('user_id')
        ->where("mom_id", $id)
        ->get();

        //get point discuss
        $pointDiscuss = DB::table('mom_point_discussed')
            ->select('*')
            ->where("mom_id", $id)
            ->get();

        //get task
        $tasks = DB::table('mom_action_plan')
            ->select('mom_action_plan.*','core_user.name')
            ->leftJoin('core_user', 'mom_action_plan.pic', '=', 'core_user.id')
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
            $meeting->updated_at = date("Y-m-d h:i:s");
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
                          'pic' => $task["pic"],
                          'status' => $task['status'],
                          'remark' => $task['remarkTask'])
                );
            }

            //save participants
            $partisipants = $request->get('partisipans');
            $countPartisipants = count($request->get('partisipans'));
            $idPartcipants = array();
            for ($i = 0; $i < $countPartisipants; $i++) 
            {
                DB::table('mom_participants')->insert(
                    array('mom_id' => $id,
                          'user_id' => $partisipants[$i])
                );

                array_push($idPartcipants, $partisipants[$i]);
            }

            $emailParticpants =  (new UserController)->getEmailUserbyListId($idPartcipants);
            //update google calendar
            $eventId = $request->get('idCalendar','');
            if ($eventId != '')
            {
                $event = Event::find($eventId);
                $event->name = $request->get('topic','');
                $startTime = $request->get('momDate')." ".$request->get('startTime');
                $endTime = $request->get('momDate')." ".$request->get('endTime');
                $event->startDateTime = Carbon::parse($startTime);
                $event->endDateTime = Carbon::parse($endTime);
                for ($i = 0; $i < $countPartisipants; $i++) 
                {
                    if ($emailParticpants[$partisipants[$i]] != null)
                    {
                        $email = $emailParticpants[$partisipants[$i]];
                        if ($email != '')
                            $event->addAttendee(['email' => $email]);
                    }
                }
                $event->save(); 
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

            //delete google calendar
            $eventId = $request->idCalendar;
            if ($eventId != '')
            {
                $event = Event::find($eventId);
                $event->delete();
            }
            
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

    /**
     * Delete the meeting 
     * @param id the id of meeting
     */
    public function calendar(Request $request)
    {
        $data = array();
        return view('meeting.calendar', compact('data'));
    }
}
