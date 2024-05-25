<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = 0;
        $message = [];
        $md5Password = md5($request->password);
        $users = DB::table('users')->where('email', $request->login)->where('password', $md5Password)->get();
        //var_dump($users[0]->name);
        if (isset($users[0])) {
            $status = 200;
            $date = date('Y-m-d H:i:s');
            $dateExpires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $token = substr(md5($date), 0, 8);

            $sql = "INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at)
                     values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $campos = array(
                1,
                1,
                $users[0]->name,
                $token,
                'NULL',
                $date,
                $dateExpires,
                $date,
                $date
            );
            $insert = DB::insert($sql, $campos);

            $message = [
                'status'        => 'logado',
                'login'         => $request->login,
                'profile_id'    => $users[0]->profile_id,
                'token'         => $token,
                'user_id'       => $users[0]->id
            ];

        } else {
            $status = 404;
            $message = [
                'usuario não encontrado'
            ];
        }

        return response()->json($message, $status);
    }
    public function getSession(Request $request)
    {
        $status = 0;
        $message = [];
        $personal_access_token = DB::table('personal_access_tokens')->where('token', $request->token)->get();
        //var_dump($users[0]->name);
        if (isset($personal_access_token[0])) {
            $date = date('Y-m-d H:i:s');
            if ($date > $personal_access_token) {
                //$this->logout();
                $status = 404;
                $message = [
                    'status'  => $status,
                    'message' => 'Usuário não logado!'
                ];
            } else {
                $status = 200;
                $message = [
                    'status'  => $status,
                    'message' => 'usuário logado',
                ];
            }

        }
        return response()->json($message, $status);
    }
    public function logout(Request $request)
    {
        $status = 0;
        $message = [];
        $personal_access_token = DB::table('personal_access_tokens')->where('token', $request->token)->get();
        if (isset($personal_access_token[0])) {
            $status = 200;
            $message = [
                'status'  => $status,
                'message' => 'usuário deslogado!',
            ];
            $personal_access_token = DB::table('personal_access_tokens')->where('token', $request->token)->delete();
        } else {
            $status = 404;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não encontrado!'
            ];
        }
        return response()->json($message, $status);
    }
    public function getUsers(Request $request)
    {

        $status = 0;
        $message = [];
        $profile = DB::table('profiles')->where('name', 'administrador')->get();
        if ($request->profile_id == $profile[0]->id) {
            $users = DB::table('users')->get();
            if (isset($users[0])) {
                $status = 200;
                $message = ['status' => $status, 'dados' => []];
                foreach ($users as $dado) {
                    array_push($message['dados'], ['id' => $dado->id, 'name' => $dado->name, 'email' => $dado->email, 'profile_id' => $dado->profile_id]);
                }
            } else {
                $status = 404;
                $message = [
                    'status'  => $status,
                    'message' => 'Perfis não encontrados!'
                ];
            }
        } else {
            $status = 401;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não autorizado!',
                'dados' => $request->profile_id. "===" .$profile[0]->id
            ];
        }


        return response()->json($message, $status);
    }
    public function getUser($token, $id)
    {

        $status = 0;
        $message = [];
        //pegar todos os perfis e ver se é perfil admin
        $personal_access_token = DB::table('personal_access_tokens')->where('token', $token)->get();
        //var_dump($users[0]->name);
        $dateExpires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        if (isset($personal_access_token[0]) && $dateExpires > $personal_access_token[0]->expires_at) {
            $users = DB::table('users')->where('id', $id)->get();
            if (isset($users[0])) {
                $status = 200;
                $message = [
                    'status' => $status, 
                    'dados' => ['id' => $users[0]->id, 'name' => $users[0]->name, 'email' => $users[0]->email, 'profile_id' => $users[0]->profile_id]
                ];
            } else {
                $status = 404;
                $message = [
                    'status'  => $status,
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
    public function insert(Request $request)
    {
        $status = 0;
        $message = [];
        $md5Password = md5($request->password);
        //pegar todos os perfis e ver se é perfil admin
        $profile = DB::table('profiles')->where('name', 'administrador')->get();
        if ((int)$request->profile_id === $profile[0]->id) {
            $users = DB::table('users')->get();
            if (isset($users[0])) {
                $status = 200;
                $message = ['status' => $status, 'dados' => []];
                $date = date('Y-m-d H:i:s');
                $token = substr(md5($date), 0, 8);

                $sql = "INSERT INTO users (name, email, email_verified_at, profile_id, password, remember_token, created_at, updated_at)
                     values(?, ?, ?, ?, ?, ?, ?, ?)";
                $campos = array(
                    $request->name,
                    $request->email,
                    $date,
                    2,
                    $md5Password,
                    $token,
                    $date,
                    $date
                );
                $insert = DB::insert($sql, $campos);

                $message = [
                    'status'  => $status,
                    'message' => 'Usuário cadastrado',
                ];
            } else {
                $status = 400;
                $message = [
                    'status'  => $status,
                    'message' => 'Usuário não cadastrado (faltam parâmetros)!'
                ];
            }
        } else {
            $status = 401;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não autorizado!'
            ];
        }


        return response()->json($message, $status);
    }
    public function update(Request $request)
    {
        $status = 0;
        $message = [];
        $date = date('Y-m-d H:i:s');
        $users = DB::table('users')->where('id', (int)$request->user_id)->update(
            array(
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => $date
            )
        );
        if ($users == 1) {
            $status = 201;
            $message = [
                'status' => $status, 
                'message' => 'Usuário atualizado!'
            ];
        } else {
            $status = 404;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não atualizado!'
            ];
        }
        return response()->json($message, $status);
    }
    public function delete($profile_id, $id)
    {
        $status = 0;
        $message = [];
        $profile = DB::table('profiles')->where('name', 'administrador')->get();
        if ((int)$profile_id === $profile[0]->id) {
            $users = DB::table('users')->where('id', (int)$id)->get();
            if (isset($users[0])) {
                $users = DB::table('users')->where('id', (int)$id)->delete();
                if ($users == 1) {
                    $status = 200;
                    $message = [
                        'status'  => $status,
                        'message' => 'usuário removido!',
                    ];
                } else {
                    $status = 404;
                    $message = [
                        'status'  => $status,
                        'message' => 'Usuário não removido!',
                    ];
                }
            } else {
                $status = 401;
                $message = [
                    'status'  => $status,
                    'message' => 'Usuário não encontrado!'
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
    public function updateProfileUser(Request $request)
    {
        $status = 0;
        $message = [];
        $date = date('Y-m-d H:i:s');
        //pegar todos os perfis e ver se é perfil admin
        $profile = DB::table('profiles')->where('name', 'administrador')->get();
        if ((int)$request->profile_id === $profile[0]->id && $request->user_id) {
            $users = DB::table('users')->where('id', (int)$request->user_id)->update(
                array(
                    'profile_id' => $request->profile_id_new,
                    'updated_at' => $date
                )
            );
            if ($users == 1) {
                $status = 201;
                $message = [
                    'status' => $status, 
                    'message' => 'Usuário atualizado!'
                ];
            } else {
                $status = 404;
                $message = [
                    'status'  => $status,
                    'message' => 'Usuário não atualizado!'
                ];
            }
        } else {
            $status = 401;
            $message = [
                'status'  => $status,
                'message' => 'Usuário não autorizado!'
            ];
        }
        return response()->json($message, $status);
    }
}