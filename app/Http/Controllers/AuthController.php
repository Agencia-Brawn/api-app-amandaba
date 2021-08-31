<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\Cloner\Data;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('auth:api', ['except' => ['login', 'register', 'loginCpf']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function loginCpf(Request $request)
    {
        if(!$request->cpf && !$request->password ){
            return response()->json(['error' => 'Ausencia de dados'], 401);
        }
        $dados =  User::where('cpf', $request->cpf)->first();

        $credentials = ['email'=> $dados->email, 'password'=>$request->password];
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token); 
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Você saiu com sucesso']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'Nome é obrigatório.',
            'email.unique' => "Email já registrado",
            'string' => "Formato invalido para o campo :attribute",
            'name.min' => "Nome deve tem no minimo 3 caracteres",
            'password.min' => "Senha deve tem no minimo 8 caracteres",
            'password.confirmed' => "Senha não corresponde",
            'email.required' => 'Email é obrigatório.',
            'telefone.required'=> "Telefone Obrigatorio"
        ];

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'numeric', 'min:8', 'confirmed'],
            'telefone' => ['required', 'string'],
            'cpf' => ['required'],
            'altura'=> ['required'],
            'datanasc' => ['required']
        ], $messages);

        if($validator->fails()){
            return response($validator->messages(), 422);
        }else{
            $user = $this->create($request->all());

            $credentials = request(['email', 'password']);

            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);
        }
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'telefone' => $data['telefone'],
            'cpf'=> $data['cpf'],
            'altura'=> $data['altura'],
            'datanasc'=> $data['datanasc']
        ]);
    }

}
