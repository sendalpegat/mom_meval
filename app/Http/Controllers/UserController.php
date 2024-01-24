<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OdooController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Synchron data user from odoo
     */
    public function syncOdoo()
    {
        DB::beginTransaction();
        try
        {
            //get data from odoo
            $columns = [
                'fields'=> ['active','login','name'],
            ];
            $dataUsers = (new OdooController)->getDataFromOdoo("res.users",array(),$columns);
            for ($i = 0; $i < count($dataUsers); $i ++)
            {
                $name = $dataUsers[$i]['name'];
                $email = $dataUsers[$i]['login'];
                $status = $dataUsers[$i]['active'];
                //if update failed than add
                $update = [
                    'name' => $name, 
                    'status' => $status
                ];
                
                $filter = [
                    'email' => $email
                ];

                $res = DB::table('core_user')->where($filter)->update($update);
                if ($res == 0)
                {
                    $default_password =  Hash::make("meval-user");
                    //add user
                    DB::table('core_user')->insert(
                        array('email' => $email,
                              'name' => $name,
                              'password' => $default_password,
                              'status' => $status)
                    ); 
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
    }
}
