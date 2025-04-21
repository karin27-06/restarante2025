<?php

namespace App\Http\Controllers\Panel;

//use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
//use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
//use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);
        return Inertia::render('panel/user/indexUser');
    }

    public function listarUsers(Request $request){
        Gate::authorize('viewAny', User::class);
        try {
            $name = $request->get('name');
            $users = User::when($name, function ($query, $name) {
                return $query->whereLike('name', "%$name%");
            })->orderBy('id','asc')->paginate(12);
            return response()->json([
                'users' => UserResource::collection($users),
                'pagination' => [
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ]
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los usuarios',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('panel/user/components/formUser');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated = $request->safe()->except(['status']);
        $user = User::create(Arr::except($validated, ['status']));
        // // $validated['status'] = $validated['status'] === 'activo' ? true : false;
        return redirect()->route('panel.users.index')->with('message', 'Usuario creado correctamente');   
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);
        return response()->json([
            'status' => true,
            'message' => 'Usuario encontrado',
            'user' => new UserResource($user),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        $validated = $request->validated();
        $validated['status'] = ($validated['status'] ?? 'inactivo') === 'activo';
        $user->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'Usuario actualizado correctamente',
            'user' => new UserResource($user->refresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    // EXPORTAR A EXCEL
    /*
    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'usuarios.xlsx');
    }

    // IMPORTAR EXCEL
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new UserImport, $request->file('archivo'));
        return response()->json([
            'message' => 'Importación de usuarios realizado correctamente.'
        ]);
    }*/
}
