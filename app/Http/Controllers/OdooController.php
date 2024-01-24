<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ripcord\Ripcord;
use Illuminate\Support\Facades\Log;

class OdooController extends Controller
{
    
    /**
     * Create a new controller instance.
     */
    public function __construct() 
    {
        //setting the odoo
        $this->url    = "https://meval.odooerp.id";
        $this->database     = "meval"; 
        $this->user   = "adhi.wiradh@meval.id"; 
        $this->password = "H8YKoqEl2rzr0a8ksXZrBvuj15bLd00K5qlhB1Ch"; 

        $this->uid    = $this->login();
    }

    //login to odoo
    private function login()
    {
        try{
            $common = ripcord::client($this->url. "/xmlrpc/2/common");
            return $common->authenticate($this->database, $this->user, $this->password, []);    
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * get data from odoo
     * @param tableName the name of table that would be read
     * @param filter the filter that use for getting data
     * @param selectedColumm the column that would be read
     */
    public function getDataFromOdoo($tableName='', $filter=array(), $selectedColumn=array()){
        set_time_limit(0);
        if(!empty($this->uid)){
            $models = ripcord::client($this->url . "/xmlrpc/2/object");
            $data = $models->execute_kw($this->database, $this->uid, $this->password, $tableName, 'search_read', [$filter], $selectedColumn);
            return $data;                                            
        }     
        
        return array();
    }    


}
