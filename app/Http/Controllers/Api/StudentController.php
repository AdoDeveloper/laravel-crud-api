<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    //

    public function index(){

        $students = Student::all();


        if($students->isEmpty()){
            $data = [
                'message' => 'No se encontraron estudiantes',
                'status' => 200
            ];

            return response()->json($data, 200);
        } else {
            $filteredStudents = $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'nombres' => $student->nombres,
                    'apellidos' => $student->apellidos,
                    'correo' => $student->correo,
                    // No incluir la clave aquí
                ];
            });

            $data = [
                'students' => $filteredStudents,
                'status' => 200
            ];
            return response()->json($data, 200);
        }
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(),
        [
            'nombres' => 'required|max:30',
            'apellidos' => 'required',
            'correo' => 'required|email|unique:estudiantes',
            'clave' => 'required'
        ],
        [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'unique' => 'El correo :attribute ya está en uso.',
            'max'=> 'El maximo de caracteres es de 30'
        ]);

        if($validator->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return response()->json($data, 400);
        }else{
            $clavecifrada  = Hash::make($request->clave);

            $student = Student::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'clave' => $$clavecifrada
            ]);

            if(!$student){
                $data = [
                    'message' => 'Error al crear el estudiante',
                    'status' => 500
                ];

                return response()->json($data,500);
            }else{
                
                $filteredStudent = [
                    'id' => $student->id,
                    'nombres' => $student->nombres,
                    'apellidos' => $student->apellidos,
                    'correo' => $student->correo,
                ];
    
                $data = [
                    'student' => $filteredStudent,
                    'message' => 'Estudiante registrado exitosamente',
                    'status' => 201
                ];

                return response()->json($data,201);
            }
        }
    }

    public function show($id){
        $student = Student::find($id);

        if(!$student){
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];

            return response()->json($data, 404);
        }else{

            $filteredStudent = [
                'id' => $student->id,
                'nombres' => $student->nombres,
                'apellidos' => $student->apellidos,
                'correo' => $student->correo,
            ];

            $data = [
                'student' => $filteredStudent,
                'status' => 200
            ];

            return response()->json($data,200);
        }
    }

    public function destroy($id){
        $student = Student::find($id);

        if(!$student){
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];

            return response()->json($data,404);
        }else{
            $student->delete();
            $data = [
                'student' => $student,
                'message' => 'Estudiante eliminado',
                'status' => 200
            ];

            return response()->json($data, 200);
        }
    }

    public function update(Request $request, $id){
        $student = Student::find($id);

        if(!$student){
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];

            return response()->json($data,404);
        }else{
            $validator = Validator::make($request->all(), [
                'nombres' => 'required|max:30',
                'apellidos' => 'required',
                'correo' => 'required|email|unique:estudiantes,correo,' . $student->id,
                'clave' => 'required'
            ], [
                'required' => 'El campo :attribute es obligatorio.',
                'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
                'unique' => 'El correo :attribute ya está en uso.',
                'max' => 'El máximo de caracteres es de 30'
            ]);
    
            if($validator->fails()){
                $data = [
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors(),
                    'status' => 400
                ];
    
                return response()->json($data, 400);
            }else{
                $student->nombres = $request->nombres;
                $student->apellidos = $request->apellidos;
                $student->correo = $request->correo;

                $clavecifrada = Hash::make($request->clave);

                $student->clave = $clavecifrada;

                $student->save();

                $data = [
                    'student' => $student,
                    'message' => 'Estudiante actualizado',
                    'status' => 200
                ];

                return response()->json($data,200);
            }
        }
    }

    public function updatePartial(Request $request, $id){
        $student = Student::find($id);

        if(!$student){
            $data = [
                'message' => 'Estudiante no encontrado',
                'status' => 404
            ];

            return response()->json($data,404);
        }else{
            $validator = Validator::make($request->all(), [
                'nombres' => 'max:30',
                'apellidos',
                'correo' => 'email|unique:estudiantes,correo,' . $student->id,
                'clave'
            ], [
                'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
                'unique' => 'El correo :attribute ya está en uso.',
                'max' => 'El máximo de caracteres es de 30'
            ]);

            if($validator->fails()){
                $data = [
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors(),
                    'status' => 400
                ];

                return response()->json($data, 400);
            }else{

                if($request->has('nombres')){
                    $student->nombres = $request->nombres;
                }

                if($request->has('apellidos')){
                    $student->apellidos = $request->apellidos;
                }

                if($request->has('correo')){
                    $student->correo = $request->correo;
                }

                if($request->has('clave')){
                    $clavecifrada = Hash::make($request->clave);
                    $student->clave = $clavecifrada;
                }

                $student->save();

                $data = [
                    'student' => $student,
                    'message' => 'Estudiante actualizado',
                    'status' => 200
                ];

                return response()->json($data,200);
            }
        }
    }
}
