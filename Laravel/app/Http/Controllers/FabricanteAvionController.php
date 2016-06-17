<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

// Necesita los dos modelos Fabricante y Avion
use App\Fabricante;
use App\Avion;

// Necesitamos la clase Response para crear la respuesta especial con la cabecera de localización en el método Store()
use Response;


class FabricanteAvionController extends Controller {
    // Configuramos en el constructor del controlador la autenticación usando el Middleware auth.basic,
    // pero solamente para los métodos de crear, actualizar y borrar.
    public function __construct()
    {
        $this->middleware('auth.basic',['only'=>['store','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($idFabricante)
    {
        // Devolverá todos los aviones.
        //return "Mostrando los aviones del fabricante con Id $idFabricante";
        $fabricante=Fabricante::find($idFabricante);

        if (! $fabricante)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un fabricante con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','data'=>$fabricante->aviones()->get()],200);
        //return response()->json(['status'=>'ok','data'=>$fabricante->aviones],200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request,$idFabricante)
    {
        /* Necesitaremos el fabricante_id que lo recibimos en la ruta
         #Serie (auto incremental)
        Modelo
        Longitud
        Capacidad
        Velocidad
        Alcance */

        // Primero comprobaremos si estamos recibiendo todos los campos.
        if ( !$request->input('modelo') || !$request->input('longitud') || !$request->input('capacidad') || !$request->input('velocidad') || !$request->input('alcance') )
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para el proceso de alta.'])],422);
        }

        // Buscamos el Fabricante.
        $fabricante= Fabricante::find($idFabricante);

        // Si no existe el fabricante que le hemos pasado mostramos otro código de error de no encontrado.
        if (!$fabricante)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un fabricante con ese código.'])],404);
        }

        // Si el fabricante existe entonces lo almacenamos.
        // Insertamos una fila en Aviones con create pasándole todos los datos recibidos.
        $nuevoAvion=$fabricante->aviones()->create($request->all());

        // Más información sobre respuestas en http://jsonapi.org/format/
        // Devolvemos el código HTTP 201 Created – [Creada] Respuesta a un POST que resulta en una creación. Debería ser combinado con un encabezado Location, apuntando a la ubicación del nuevo recurso.
        $response = Response::make(json_encode(['data'=>$nuevoAvion]), 201)->header('Location', 'http://www.dominio.local/aviones/'.$nuevoAvion->serie)->header('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($idFabricante,$idAvion)
    {
        //
        return "Se muestra avión $idAvion del fabricante $idFabricante";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($idFabricante,$idAvion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($idFabricante,$idAvion)
    {
        //
    }

}