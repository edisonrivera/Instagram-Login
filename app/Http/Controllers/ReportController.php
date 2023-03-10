<?php

namespace App\Http\Controllers;

use App\Models\Informe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ReportController extends Controller
{
    //
    public function index (Request $request)
    {
        //Busqueda de informacion
        $reportes = Informe::with('user')->get();
        return view ('report.index')->with('reportes',$reportes);
    }

    public function create()
    {
        //
        return view ('report.create');
    }
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id=Auth::id();
        //Campos a validar
        $campos=[
            'title'=>'required|string|max:50',
            'information'=>'required|string|min:200',
            'image'=>'required|max:1000|mimes:jpeg,png,svg,jpg',
        ];

        //mensajes de error
        $mensaje=[
            'title.required' => 'Te falta el título :(',
            'information.required' => 'Recuerda la información',
            'image.required' => 'Pon una imagen para mejor visibilidad',
        ];

        $this->validate($request, $campos, $mensaje);
        //Conseguir datos
        $datosReporte = $request-> except('_token');
        if($request -> hasFile('image')){
            $datosReporte['image'] = $request -> file('image')->store('uploads','public');
        }
        $datosReporte['user_id'] = $user_id;

        Informe::insert($datosReporte);
        /*Route::get('/notification', function(){
            $user_id=Auth::id();
            $users = User::all()-> except($user_id);
            Notification::send($users, new \App\Notifications\ReportUser);
            return('Notificación enviada');
        
        })->name('notification');*/
        $users = User::all()-> except($user_id);
        Notification::send($users, new \App\Notifications\ReportUser);
        return to_route('report')->with('mensaje','Registro Creado');
    }

}
