<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RegistroPes;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\ConsoleOutput;


class PesController extends Controller
{
    protected $output;
   /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->output = new ConsoleOutput();
       $this->middleware('auth:api');
    }

    public function registroSemanal(Request $request)
    {
        $this->writeln($request);
        $request->validate([
            'pe_sp_esq' => 'image|mimes:jpeg,png,jpg|max:2048',
            'pe_sb_esq' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $pe_sp_esq = $this->salva($request->file('pe_sp_esq'));
        $pe_sb_esq = $this->salva($request->file('pe_sb_esq'));
        $pe_sp_dir = $this->salva($request->file('pe_sp_dir'));
        $pe_sb_dir = $this->salva($request->file('pe_sb_dir'));

        $registro = RegistroPes::where('users_id', auth()->user()->id)->orderBy('id', 'desc')->first();

        if($registro){
            $registro->pe_sp_esq = $pe_sp_esq && $this->delete($registro->pe_sp_esq, $pe_sp_esq)?$pe_sp_esq:$registro->pe_sp_esq;
            $registro->pe_sb_esq = $pe_sb_esq && $this->delete($registro->pe_sb_esq, $pe_sb_esq)?$pe_sb_esq:$registro->pe_sb_esq;
            $registro->pe_sp_dir = $pe_sp_dir && $this->delete($registro->pe_sp_dir, $pe_sp_dir)?$pe_sp_dir:$registro->pe_sp_dir;
            $registro->pe_sb_dir = $pe_sb_dir && $this->delete($registro->pe_sb_dir, $pe_sb_dir)?$pe_sb_dir:$registro->pe_sb_dir;
            $registro->update();
        }else{
            $registro = new RegistroPes();
            $registro->users_id = auth()->user()->id;
            $registro->pe_sp_esq = $this->valida($pe_sp_esq)?$pe_sp_esq:"";
            $registro->pe_sb_esq = $this->valida($pe_sb_esq)?$pe_sb_esq:"";
            $registro->pe_sp_dir = $this->valida($pe_sp_dir)?$pe_sp_dir:"";
            $registro->pe_sb_dir = $this->valida($pe_sp_dir)?$pe_sp_dir:"";
            $registro->save();
        }
        return response()->json($registro, 201);
    }

    protected function salva($file){

        if($file){
            $path = $file->storeAs(
                'imgs/pes', time().'.'.$file->getClientOriginalExtension()
            );
            return $path;
        }else{
            return false;
        }
    }

    protected function valida($pathImagem)
    {
        if(Storage::exists($pathImagem)){
            return true;
        }
        return false;
    }

    //Deleta Imagem de um local especificado
    protected function delete($nomeImagem, $file)
    {
        if(Storage::exists($nomeImagem) && $file){
            if(Storage::delete($nomeImagem)){
                return true;
            }
            return false;
        }
        return true;

    }
}
