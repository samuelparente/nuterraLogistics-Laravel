<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use App\Models\Admin\Status;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\ValidationException;
class UserController extends Controller
{
    // Todos os users
    public function index(Request $request)
    {
        try {
            $roles = Role::all();
            $roleLabelsMap = User::roleLabelMap();
            $statuses = Status::all(); // ✅ Obter os estados

            $users = User::query()
                ->filterByRole($request->role)
                ->filterByStatus($request->status)
                ->search($request->search)
                ->paginate(paginationPerPage())
                ->appends($request->only(['role', 'status', 'search']));

            return view('layouts.admin.users.index', compact('users', 'roles', 'statuses', 'roleLabelsMap'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Mostrar form de criar novo user
    public function create()
    {
        try {
            $roles = Role::all();
            $roleLabelsMap = User::roleLabelMap();
            $statuses = Status::all(); // ✅ Carregar os estados

            return view('layouts.admin.users.create', compact('roles', 'roleLabelsMap', 'statuses'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Gravar novo user
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'              => 'required|string|max:255',
                'email'             => 'required|string|email|max:255|unique:users',
                'password'          => 'required|string|min:6|confirmed',
                'status_id'         => 'required|exists:statuses,id', // ✅ Novo campo
                'role'              => 'required|exists:roles,name',
                'cropped_avatar'    => 'nullable|string',
            ]);

            $avatarFilename = null;

            if ($request->filled('cropped_avatar')) {
                $imageData = $request->input('cropped_avatar');

                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]);

                    $imageData = base64_decode($imageData);
                    if ($imageData === false) {
                        return back()->with('error', 'Erro ao processar a imagem do avatar.');
                    }

                    $avatarFilename = Str::uuid() . '.' . $type;
                    Storage::disk('public')->put("images/general/avatars/{$avatarFilename}", $imageData);
                }
            }

            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'status_id'  => $validated['status_id'], // ✅ Novo campo
                'avatar'     => $avatarFilename,
            ]);

            $user->assignRole($validated['role']);

            return redirect()->route('users.index')->with('success', 'Dados inseridos com sucesso!');
        } catch (ValidationException $e) {
            throw $e; // Re-lança para o Laravel lidar
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Editar user
    public function edit(User $user)
    {
        try {
            $roles = Role::all();
            $roleLabelsMap = User::roleLabelMap();
            $statuses = Status::all(); // ✅ Carregar os estados

            return view('layouts.admin.users.edit', compact('user', 'roles', 'roleLabelsMap', 'statuses'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Atualizar user
    public function update(Request $request, User $user)
    {
        try {
            $rules = [
                'name'           => 'nullable|string|max:255',
                'email'          => [
                    'nullable',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'status_id'      => 'nullable|exists:statuses,id', // ✅ Novo campo
                'role'           => 'nullable|exists:roles,name',
                'cropped_avatar' => 'nullable|string',
            ];

            if ($request->filled('password') && $request->filled('password_confirmation')) {
                $rules['password'] = 'required|string|min:6|confirmed';
            }

            $validated = $request->validate($rules);

            $data = [];

            if ($request->filled('name') && $request->name !== $user->name) {
                $data['name'] = $request->name;
            }

            if ($request->filled('email') && $request->email !== $user->email) {
                $data['email'] = $request->email;
            }

            if ($request->filled('password') && !Hash::check($request->password, $user->password) && $request->filled('password_confirmation')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->has('status_id')) {
                $data['status_id'] = $request->status_id; // ✅ Novo campo
            }

            if ($request->filled('cropped_avatar')) {
                $imageData = $request->input('cropped_avatar');

                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]);

                    $imageData = base64_decode($imageData);
                    if ($imageData === false) {
                        return back()->with('error', 'Erro ao processar a imagem do avatar.');
                    }

                    $avatarFilename = Str::uuid() . '.' . $type;
                    Storage::disk('public')->put("images/general/avatars/{$avatarFilename}", $imageData);

                    $data['avatar'] = $avatarFilename;
                }
            }

            if (!empty($data)) {
                $user->update($data);
            }

            if ($request->filled('role') && $user->getRoleNames()->first() !== $request->role) {
                $user->syncRoles([$request->role]);
            }

            return redirect()->route('users.index')->with('success', 'Dados atualizados com sucesso!');
        } catch (ValidationException $e) {
            throw $e; // Re-lança para o Laravel lidar
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Eliminar utilizador
    public function destroy(User $user)
    {
        try {
            $currentUser = Auth::user();

            if ($currentUser->id === $user->id) {
                return redirect()->back()->with('error', 'Não pode eliminar o próprio registo!');
            }

            if (!$currentUser->hasAnyRole(['super-admin', 'admin'])) {
                return redirect()->back()->with('error', 'Não tem permissão para eliminar dados.');
            }

            if ($user->hasRole('super-admin') && !$currentUser->hasRole('super-admin')) {
                return redirect()->back()->with('error', 'Apenas um Super Administrador pode eliminar outro Super Administrador!');
            }

            if ($user->avatar && Storage::disk('public')->exists('images/general/avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('images/general/avatars/' . $user->avatar);
            }

            $user->delete();

            return redirect()->route('users.index')->with('success', 'Dados eliminados com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }
}
