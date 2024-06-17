<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
                array_push($message['dados'], ['id' => $dado->id, 'name' => $dado->name, 'description' => $dado->description]);
            }
        }else{
            $status = 404;
            $message = [
                'message' => 'Perfis não encontrados!'
            ];
        }
        return response()->json($message, $status);

    }
    public function profile($token, $id){
        $status = 0;
        $message = [];
        $personal_access_token = DB::table('personal_access_tokens')->where('token', $token)->get();
        //var_dump($users[0]->name);
        $dateExpires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        if (isset($personal_access_token[0]) && $dateExpires > $personal_access_token[0]->expires_at) {
            $profile = DB::table('profiles')->where('id', $id)->get();
            if (isset($profile[0])) {
                $status = 200;
                $message = ['status' => $status, 'dados' => ['id' => $profile[0]->id, 'name' => $profile[0]->name, 'description' => $profile[0]->description]];
            }else{
                $status = 404;
                $message = [
                    'message' => 'Perfil não encontrado!'
                ];
            }
        } else {
            $status = 401;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não atenticado!',
            ];
        }
        return response()->json($message, $status);
    }
    public function insert(Request $request){
        $status = 0;
        $message = [];
        $profile = DB::table('profiles')->where('name', 'administrador')->get();
        if ((int)$request->profile_id === $profile[0]->id) {
            $profile = DB::table('profiles')->get();
                $status = 201;
                $message = ['status' => $status, 'dados' => []];
                $date = date('Y-m-d H:i:s');

                $sql = "INSERT INTO profiles (name, description, created_at)
                     values(?, ?, ?)";
                $campos = array(
                    $request->name,
                    $request->description,
                    $date
                );
                $insert = DB::insert($sql, $campos);

                $message = [
                    'status'  => $status,
                    'message' => 'Perfil cadastrado',
                ];
        } else {
            $status = 401;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não autorizado!'
            ];
        }


        return response()->json($message, $status);
    }
    public function update(Request $request){
        $status = 0;
        $message = [];
        $date = date('Y-m-d H:i:s');
        $profile = DB::table('profiles')->where('id', (int)$request->profile_id)->update(
            array(
                'name' => $request->name,
                'description' => $request->description,
            )
        );
        if ($profile == 1) {
            $status = 201;
            $message = [
                'status' => $status, 
                'message' => 'Perfil atualizado!'
            ];
        } else {
            $status = 404;
            $message = [
                'status'  => $status,
                'message' => 'Perfil não atualizado!'
            ];
        }
        return response()->json($message, $status);
    }
    public function delete($profile_id, $id){
        $status = 0;
        $message = [];
        $profile = DB::table('profiles')->where('name', 'administrador')->get();
        if ((int)$profile_id === $profile[0]->id) {
            $profile = DB::table('profiles')->where('id', (int)$profile_id)->get();
            if (isset($profile[0])) {
                $profile = DB::table('profiles')->where('id', (int)$id)->delete();
                if ($profile == 1) {
                    $status = 200;
                    $message = [
                        'status'  => $status,
                        'message' => 'Perfil removido!',
                    ];
                } else {
                    $status = 404;
                    $message = [
                        'status'  => $status,
                        'message' => 'Perfil não removido!',
                    ];
                }
            } else {
                $status = 401;
                $message = [
                    'status'  => $status,
                    'message' => 'Perfil não encontrado!'
                ];
            }
        } else {
            $status = 400;
            $message = [
                'message' => 'Usuário não permitido!'
            ];
        }
        return response()->json($message, $status);
    }
}