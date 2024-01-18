<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\meeting\Meeting;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MeetingController extends Controller
{
    const OBJECT_NAME = "Meeting";

    const VIEW_MODE_ADD = 0;
    const VIEW_MODE_UPDATE = 1;
    /** show view list of meeting */
    public function index()
    {
        return view('meeting.list_meeting');
    }

    /** save meeting to database
     * @param request
     */
    public function store(Request $request)
    {
        $res = "failed";
        $msg = "Failed saving";
        try 
        {
            
            $request->validate([
                'title' => 'required|max:255',
                'body' => 'required',
            ]);
        
            // generate id
            $prefix = $year = date("Ymd");
            $momId = $this->generateId(self::OBJECT_NAME,$prefix);

            $meeting = array("topic"=> $request->get('topic',''),
                            "agenda"=> $request->get('agenda',''),
                            "location" => $request->get('location',''),
                            "mom_date"=> $request->get('mom_date'),
                            "start_time"=> $request->get('start_time'),
                            "end_time"=> $request->get('end_time'),
                            "duration"=> $request->get('duration'),
                            "note"=>$request->get('note',''),
                            "updated_by" => $request->get('updated_by'));

            //save to database                
            Meeting::create($meeting);

            $res = "success";
            $msg = 'Meeting saved successfully.';

        }catch (Exception $e) {
            Log::error($e->getMessage());
        }
        
        return redirect()->route('meeting.index')
            ->with($res, $msg);
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

        if (!empty($result))
        {
            $id = $id + 1;
            //update counter
            DB::table('core_counter')->where($filter)
            ->update(['counter' =>DB::raw($id)]);
        }
        else
        {
            //update counter
            DB::table('core_counter')->insert(
                array('object' => $objectName,
                      'prefix' => 'john',
                      'counter' => 'doe')
            );

        }

        return $id;
    }

     /**
     * Show the form for creating a new meeting.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$users = User::where('status', 1)->orderBy('name')->lists('name', 'id');
        $data = array("viewMode" => self::VIEW_MODE_ADD);
        return view('meeting.editor', compact('data'));
    }

    /** show meeting by id
     * @param request
     */
    public function show($id)
    {
        $data = array("viewMode" => self::VIEW_MODE_UPDATE);
        return view('meeting.editor', compact('data'));
    }

    /**
     * Update the meeting
     * @param request 
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Delete the meeting 
     * @param id the id of meeting
     */
    public function destroy($id)
    {

    }

    public function showTasks()
    {
        return view('meeting.list_task');
    }
}
