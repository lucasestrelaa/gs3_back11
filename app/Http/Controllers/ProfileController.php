<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProfileController extends Controller
{
    public function index(){
        $status = 0;
        $message = [];

        $profile = DB::table('profiles')->get();
        if (isset($profile[0])) {
            $status = 200;
            $message = ['status' => $status, 'dados' => []];
            foreach($profile as $dado){
                array_push($message['dados'], ['id' => $dado->id, 'name' => $dado->name]);
            }
        }else{
            $status = 404;
            $message = [
                'message' => 'Perfis não encontrados!'
            ];
        }
        return response()->json($message, $status);

    }
    public function profile(Request $request){
        $status = 0;
        $message = [];

        $profile = DB::table('profiles')->where('id', $request->profile_id)->get();
        if (isset($profile[0])) {
            $status = 200;
            $message = ['status' => $status, 'dados' => ['id' => $profile[0]->id, 'name' => $profile[0]->name]];
        }else{
            $status = 404;
            $message = [
                'message' => 'Perfil não encontrado!'
            ];
        }
        return response()->json($message, $status);
    }
    public function update(Request $request){

    }
    public function delete(Request $request){

    }
    public function insert(Request $request){

    }
}
