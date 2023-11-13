<?php

namespace App\Http\Controllers;

use App\Models\Municipios;
use App\Utilities\Constantes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MunicipiosController extends Controller
{
    private string $clazz = MunicipiosController::class;

    public function getMunicipios(Request $request):JsonResponse
    {
        Log::info($this->clazz.'->getMunicipios() => init');

        $option = $request->opcion;
        $estado = $request->estado;
        $idmunicipio = $request->idmunicipio;
        $iddepartamento = $request->iddepartamento;

        Log::info($this->clazz.'->getMunicipios => option: '.$option);
        Log::info($this->clazz.'->getMunicipios => estado: '.$estado);
        Log::info($this->clazz.'->getMunicipios => idmunicipio: '.$idmunicipio);
        Log::info($this->clazz.'->getMunicipios => iddepartamento: '.$iddepartamento);

        try{
            switch ($option)
            {
                case 1:
                    /** TRAE UN LISTADO DE MUNICIPIOS POR ID DEPARTAMENTO Y ESTADO */
                    Log::info($this->clazz.'->getMunicipios() => msg: TRAE UN LISTADO DE MUNICIPIOS POR ID DEPARTAMENTO Y ESTADO');

                    $result = Municipios::query(
                    )->join(
                        'departamentos',
                        'municipios.id_departamento',
                        '=',
                        'departamentos.id_departamento'
                    )->select(
                        'municipios.*',
                        'departamentos.departamento'
                    )->where(
                        'municipios.id_departamento',
                        '=',
                        $iddepartamento
                    )->where(
                        'municipios.estado',
                        '=',
                        $estado
                    )->get();

                    if($result->count() > 0){
                        return response()->json([
                            'code' => 200,
                            'data' => $result
                        ]);
                    }else{
                        return response()->json([
                            'code' => 200,
                            'data' => 'No Se Encontraron Registros!!!'
                        ]);
                    }
                    break;
                case 2:
                    /** TRAE UN OBJETO DE MUNICIPIOS POR ID */
                    Log::info($this->clazz.'->getMunicipios() => msg: TRAE UN OBJETO DE MUNICIPIOS POR ID: '.$idmunicipio);

                    $result = Municipios::query()->where(
                        'id_municipio',
                        '=',
                        $idmunicipio
                    )->first();

                    if($result){
                        return response()->json([
                            'code' => 200,
                            'data' => $result
                        ]);
                    }else{
                        return response()->json([
                            'code' => 200,
                            'data' => 'No Se Encontraron Registros!!!'
                        ]);
                    }
                    break;
                default:
                    /** TRAE UN LISTADO COMPLETO DE MUNICIPIOS SIN CONDICIONES */
                    Log::info($this->clazz.'->getMunicipios() => msg: TRAE UN LISTADO COMPLETO DE MUNICIPIOS SIN CONDICIONES');

                    $result = Municipios::all();

                    if($result->count() > 0){
                        return response()->json([
                            'code' => 200,
                            'data' => $result
                        ]);
                    }else{
                        return response()->json([
                            'code' => 200,
                            'data' => 'No Se Encontraron Registros!!!'
                        ]);
                    }
                    break;
            }
        } catch (\Throwable $throwable) {
            Log::error($this->clazz.'->getMunicipios() => error: '.$throwable->getMessage());

            return response()->json([
                'error' => $throwable->getMessage()
            ], 500);
        }
    }

    public function storeMunicipios(Request $request) : JsonResponse
    {
        Log::info($this->clazz.'->storeMunicipios() => init');
        try {
            $validation = Validator::make($request->all(), [
                'id_departamento' => 'required',
                'municipio' => 'required',
                'usuario_creacion' => 'required'
            ]);

            if($validation->fails()){
                return response()->json([
                    'success' => false,
                    'data' => $validation->messages()
                ]);
            }else{
                $result = Municipios::create($request->all());

                return response()->json([
                    'success' => true,
                    'data' => 'Registro Guardado Correctamente!!!',
                    'result' => $result
                ]);
            }
        } catch (\Throwable $throwable) {
            Log::error($this->clazz.'->storeMunicipios() => error: '.$throwable->getMessage());

            return response()->json([
                'error' => $throwable->getMessage()
            ], 500);
        }
    }

    public function updateMunicipios(Request $request, $id) : JsonResponse
    {
        Log::info($this->clazz.'->updateMunicipios() => init');
        try {
            $validation = Validator::make($request->all(), [
                'id_departamento' => 'required',
                'municipio' => 'required',
                'estado' => 'required',
                'usuario_modificacion' => 'required'
            ]);

            if($validation->fails()){
                return response()->json([
                    'success' => false,
                    'data' => $validation->messages()
                ]);
            }else{
                $municipio = Municipios::find($id);

                if($municipio){

                    $municipio->id_departamento = $request->departamento;
                    $municipio->municipio = $request->municipio;
                    $municipio->estado = $request->estado;
                    $municipio->usuario_modificacion = $request->usuario_modificacion;

                    $result = $municipio->save();

                    return response()->json([
                        'success' => true,
                        'data' => 'Registro Actualizado Correctamente!!!',
                        'result' => $result
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'data' => 'Registro #: '.$id.' No Encontrado!!!'
                    ]);
                }
            }
        } catch (\Throwable $throwable) {
            Log::error($this->clazz.'->updateMunicipios() => error: '.$throwable->getMessage());

            return response()->json([
                'error' => $throwable->getMessage()
            ], 500);
        }
    }

    public function deleteMunicipio($id) : JsonResponse
    {
        Log::info($this->clazz.'->deleteMunicipio() => init');
        try {
            $municipio = Municipios::find($id);

            if($municipio){
                $municipio->estado = Constantes::ESTADO_ELIMINADO;
                $result = $municipio->save();

                return response()->json([
                    'success' => true,
                    'data' => 'Registro Eliminado Correctamente!!!',
                    'result' => $result
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'data' => 'Registro #: '.$id.' No Encontrado!!!'
                ]);
            }
        } catch (\Throwable $throwable) {
            Log::error($this->clazz.'->deleteMunicipio() => error: '.$throwable->getMessage());

            return response()->json([
                'error' => $throwable->getMessage()
            ], 500);
        }
    }
}