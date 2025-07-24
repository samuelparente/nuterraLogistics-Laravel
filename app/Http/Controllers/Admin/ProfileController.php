<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use Spatie\Permission\Models\Role;
use App\Models\Admin\Status; 
use Exception;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    // Mostrar perfil
    public function show(User $user)
    {
        try {
            // Apenas o próprio utilizador ou o super-admin pode ver o perfil
            if (Auth::id() !== $user->id && !Auth::user()->hasRole('super-admin')) {
                abort(403);
            }

            $roles = Role::all();
            $roleLabelsMap = User::roleLabelMap();

            return view('layouts.admin.profile.show', compact('user', 'roles', 'roleLabelsMap'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Editar perfil
    public function edit(User $user)
    {
        try {
            // Apenas o próprio utilizador ou o super-admin pode editar o perfil
            if (Auth::id() !== $user->id && !Auth::user()->hasRole('super-admin')) {
                abort(403);
            }

            $roles = Role::all();
            $statuses = Status::all();
            $roleLabelsMap = User::roleLabelMap();

            return view('layouts.admin.profile.edit', compact('user', 'roles', 'statuses', 'roleLabelsMap'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

    // Atualizar perfil
    public function update(Request $request, User $user)
    {
        try {
            // Apenas o próprio utilizador ou o super-admin pode atualizar
            if (Auth::id() !== $user->id && !Auth::user()->hasRole('super-admin')) {
                abort(403);
            }

            // Regras de validação
            $rules = [
                'name'           => 'nullable|string|max:255',
                'email'          => [
                    'nullable',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'status_id'      => 'nullable|exists:statuses,id', // 👈 Usa status_id em vez de is_active
                'role'           => 'nullable|exists:roles,name',
                'cropped_avatar' => 'nullable|string',
            ];

            // Validação condicional para password
            if ($request->filled('password') && $request->filled('password_confirmation')) {
                $rules['password'] = 'required|string|min:6|confirmed';
            }

            $validated = $request->validate($rules);

            $data = [];

            // Atualizar nome
            if ($request->filled('name') && $request->name !== $user->name) {
                $data['name'] = $request->name;
            }

            // Atualizar email
            if ($request->filled('email') && $request->email !== $user->email) {
                $data['email'] = $request->email;
            }

            // Atualizar password
            if ($request->filled('password') && !Hash::check($request->password, $user->password)) {
                $data['password'] = Hash::make($request->password);
            }

            // Atualizar estado (status_id)
            if ($request->has('status_id')) {
                $data['status_id'] = $request->status_id;
            }

            // Processar avatar base64
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

            // Atualizar perfil
            if (!empty($data)) {
                $user->update($data);
            }

            // Atualizar papel (role)
            if ($request->filled('role') && $user->getRoleNames()->first() !== $request->role) {
                $user->syncRoles([$request->role]);
            }

            return redirect()->route('profiles.profile.show', $user->id)
                            ->with('success', 'Dados atualizados com sucesso!');
        } catch (ValidationException $e) {
            throw $e; // Re-lança para o Laravel lidar
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }
}
