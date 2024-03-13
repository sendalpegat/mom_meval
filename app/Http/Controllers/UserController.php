<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OdooController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RootController;
use Illuminate\Support\Facades\Auth;


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
            set_time_limit(0);
            $default_password =  Hash::make("meval-user");
            //get list email id of user
            $emailUser = array();
            $nameUsers = array();
            $results = DB::table('core_user')
            ->select('email','name')
            ->where('status', User::ACTIVE)
            ->get()
            ->toArray();
            foreach($results as $result)
            {
                $emailUser = $emailUser + array($result->email => $result->name);
                array_push($nameUsers, $result->name);
            }

            //get data from odoo
            $columns = [
                'fields'=> ['active','work_email','name','department_id','parent_id'],
            ];
            $dataUsers = (new OdooController)->getDataFromOdoo("hr.employee",array(),$columns);
            $emails = array();
            for ($i = 0; $i < count($dataUsers); $i ++)
            {
                $email = $dataUsers[$i]['work_email'];
                $name = $dataUsers[$i]['name'];
                if (!is_null($email))
                {
                    if ($email != '')
                    {
                        if (!in_array($email, $emails))
                        {
                            $status = $dataUsers[$i]['active'];
                            $departmentIds = $dataUsers[$i]['department_id'];
                            $departmentId = "-";
                            if (!empty($departmentIds))
                                $departmentId = $departmentIds[1];
                            
                            $parentIds = $dataUsers[$i]['parent_id'];
                            $parent = null;
                            if (!empty($parentIds))
                            {
                                if (isset($emailUser[$parentIds[1]]))
                                {
                                    $parent = $emailUser[$parentIds[1]];
                                }
                            }
                                
                            $role = User::USER;
                            //if update failed than add
                            $update = [
                                'name' => $name, 
                                'status' => $status,
                                'devision_id' => $departmentId,
                                'parent' => $parent
                            ];
                            
                            $filter = [
                                'email' => $email
                            ];

                            
                            if (array_key_exists($email,$emailUser))
                                $this->updateUser($update, $filter);
                            else
                            {
                                //add user
                                $this->addUser(
                                    array('email' => $email,
                                    'name' => $name,
                                    'password' => $default_password,
                                    'role' => $role,
                                    'devision_id' => $departmentId,
                                    'status' => $status,
                                    'parent' => $parent)
                                );
                            }
                            array_push($emails,$email);
                        }
                        else
                        {
                            $filter = [
                                'email' => $email
                            ];
                            $update = [
                                'name' => $name, 
                                'status' => $status,
                                'devision_id' => $departmentId,
                                'parent' => $parent
                            ];
                            $this->updateUser($update, $filter);
                        }
                    }
                    else
                    {
                        if (!in_array($name,$nameUsers))
                        {
                            //add user
                            $this->addUser(
                                array('email' => '',
                                'name' => $name,
                                'password' => $default_password,
                                'role' => $role,
                                'devision_id' => $departmentId,
                                'status' => $status,
                                'parent' => $parent)
                            );
                        }
                    }
                }
                else
                {
                    if (!in_array($name,$nameUsers))
                    {
                        //add user
                        $this->addUser(
                            array('email' => '',
                            'name' => $name,
                            'password' => $default_password,
                            'role' => $role,
                            'devision_id' => $departmentId,
                            'status' => $status,
                            'parent' => $parent)
                        );
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

    private function addUser(array $user)
    {
        DB::table('core_user')->insert($user); 
    }

    private function updateUser(array $updatedData, array $filter)
    {
        DB::table('core_user')->where($filter)->update($updatedData);
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

    /**
     * Get list of department id from user
     */
    public function getDepartments()
    {
        $result = DB::table('core_user')
            ->select(DB::raw( 'DISTINCT devision_id'))
            ->where("devision_id", '<>', '')
            ->get();

        return $result;
    }

    /**
     * Get list of user by parent
     */
    public function getListUserByParent(string $emailParent)
    {
        $rawSql = "WITH RECURSIVE cte AS (
            SELECT id, email, name, parent
            FROM core_user tl
            WHERE email = '".$emailParent."' 
            UNION ALL
            SELECT t2.id, t2.email, t2.name, t2.parent
            FROM core_user t2
            JOIN cte ON t2.parent= cte.email
        )
        SELECT id,email,name FROM cte";
        $userEmails = DB::select($rawSql);
        return $userEmails;
    }

    /**
     * Get list of user email by parent
     */
    public function getListUserIdByParent(string $emailParent)
    {
       
        $userEmails = $this->getListUserByParent($emailParent);
        $listUsers = array();
        foreach($userEmails as $userEmail)
        {
            array_push($listUsers, $userEmail->id);
        }

        return $listUsers;
    }

    /**
     * Get list of user that status is Active
     */
    public function getAllListUsersThatActive()
    {
        $result = DB::table('core_user')
        ->select('id','email','name')
        ->where("status", User::ACTIVE)
        ->get();

        return $result;
    }

    /**
     * Get list of email user by list of user id
     */
    public function getEmailUserbyListId(array $userIds)
    {
        $results = DB::table('core_user')
        ->select('id','email')
        ->whereIn("id", $userIds)
        ->get();

        $listUsers = array();
        foreach($results as $result)
        {
            $listUsers = $listUsers + array($result->id => $result->email);
        }

        return $listUsers;
    }

    public function profile()
    {
        $userId = Auth::user()->id;
        return $this->show($userId);
    }

    public function show($id)
    {
        
        $user = User::find($id);
        $data = array("viewMode" => self::VIEW_MODE_UPDATE, "user"=>$user);
        return view('user.editor', compact('data'));

    }

    /**
     * Update user
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try 
        {
            $id = $request->userId;
            $name = $request->name;
            $email = $request->email;
            $user = User::find($id);
            $user->name = $name;
            $user->email = $email;
            $user->update();  

            DB::commit();

            return $response = [
                'status'=>'ok',
                'success'=>true,
                'message'=>'Update profile successfull'
            ];

        }catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>'Update profile fail'
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
                'message'=>'Update profile fail. ( '.$errorMessage.' )'
            ];
        }

    }

    /**
     * Changed password
     */
    public function changePassword(Request $request)
    {
        #Match The Old Password
        if(!Hash::check($request->oldPassword, Auth::user()->password)){

            return $response = [
                'status'=>'ok',
                'success'=>false,
                'message'=>"Old Password does'n match"
            ];
        }


        #Update the new Password
        User::whereId(Auth::user()->id)->update([
            'password' => Hash::make($request->newPassword)
        ]);

        return $response = [
            'status'=>'ok',
            'success'=>true,
            'message'=>'Change password successfull'
        ];
    }
}