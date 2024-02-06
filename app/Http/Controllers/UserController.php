<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OdooController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RootController;


class UserController extends RootController
{
    /**
     * Synchron data user from odoo
     */
    public function syncOdoo()
    {
        DB::beginTransaction();
        try
        {
            //get list email id of user
            $emailUser = array();
            $results = DB::table('core_user')
            ->select('email')
            ->get()
            ->toArray();
            foreach($results as $result)
            {
                array_push($emailUser,$result->email);
            }

            //get data from odoo
            $columns = [
                'fields'=> ['active','work_email','name','department_id'],
            ];
            $dataUsers = (new OdooController)->getDataFromOdoo("hr.employee",array(),$columns);
            $emails = array();
            for ($i = 0; $i < count($dataUsers); $i ++)
            {
                $email = $dataUsers[$i]['work_email'];
                if (!is_null($email))
                {
                    if ($email != '')
                    {
                        if (!in_array($email, $emails))
                        {

                            $name = $dataUsers[$i]['name'];
                            
                            $status = $dataUsers[$i]['active'];
                            $departmentIds = $dataUsers[$i]['department_id'];
                            $departmentId = "-";
                            if (!empty($departmentIds))
                            {
                                $departmentId = $departmentIds[1];
                            }

                            $role = User::USER;
                            //if update failed than add
                            $update = [
                                'name' => $name, 
                                'status' => $status
                            ];
                            
                            $filter = [
                                'email' => $email
                            ];

                            if (in_array($email, $emailUser))
                                $res = DB::table('core_user')->where($filter)->update($update);
                            else
                            {
                                $default_password =  Hash::make("meval-user");
                                //add user
                                DB::table('core_user')->insert(
                                    array('email' => $email,
                                        'name' => $name,
                                        'password' => $default_password,
                                        'role' => $role,
                                        'devision_id' => $departmentId,
                                        'status' => $status)
                                ); 
                            }
                            array_push($emails,$email);
                        }
                    }
                }
            }


            DB::commit();
            return $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Synchron user successfull'
            ];
        }
        catch(Exception $e)
        {
            DB::rollback();
            Log::error($e->getMessage());
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Synchron user fail'
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
                'message'=>'Synchron user fail '
            ];
        }
    }

    public function index(Request $request)
    {
        $departmentIds = (new UserController)->getDepartments();
        $query = User::query();
        if ($request->seachTerm != '')
        {
            $query = $query->where('name', 'like', '%'.$request->seachTerm.'%');
        }

        if ($request->seachDeparment != '')
        {
            $query = $query->where('devision_id',$request->seachDeparment);
        } 

        $users = $query->select('name','email','devision_id')
        ->sortable('name','email')
        ->paginate(10);

        $data = array ("users"=>$users, "departmentIds"=>$departmentIds);
        return view('user.list_user', compact('data'))->render();
    }

    public function getDepartments()
    {
        $result = DB::table('core_user')
            ->select(DB::raw( 'DISTINCT devision_id'))
            ->where("devision_id", '<>', '')
            ->get();

        return $result;
    }
}