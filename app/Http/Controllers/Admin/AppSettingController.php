<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\AppSetting;
use Exception;

class AppSettingController extends Controller
{
    public function edit()
    {
        $settings = AppSetting::firstOrCreate([]);

        // Garantir pelo menos 5 inputs em cada lista (com valores vazios se necessário)
        $settings->notification_to = array_pad(
            json_decode($settings->notification_to ?? '[]', true),
            5,
            null
        );

        $settings->notification_cc = array_pad(
            json_decode($settings->notification_cc ?? '[]', true),
            5,
            null
        );

        return view('layouts.admin.settings.edit', compact('settings'));
    }


    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'smtp_host' => 'nullable|string|max:255',
                'smtp_port' => 'nullable|integer',
                'smtp_user' => 'nullable|string|max:255',
                'smtp_password' => 'nullable|string|max:255',
                'smtp_encryption' => 'nullable|string|max:10',
                'smtp_from_address' => 'nullable|email',
                'smtp_from_name' => 'nullable|string|max:255',
                'notification_to' => 'required|array',
                'notification_to.0' => 'required|email',
                'notification_to.*' => 'nullable|email',
                'notification_cc' => 'nullable|array',
                'notification_cc.*' => 'nullable|email',
            ]);


            $settings = AppSetting::firstOrCreate([]);

            $settings->update([
                'smtp_host' => $validated['smtp_host'] ?? null,
                'smtp_port' => $validated['smtp_port'] ?? null,
                'smtp_user' => $validated['smtp_user'] ?? null,
                'smtp_password' => $validated['smtp_password'] ?? null,
                'smtp_encryption' => $validated['smtp_encryption'] ?? null,
                'smtp_from_address' => $validated['smtp_from_address'] ?? null,
                'smtp_from_name' => $validated['smtp_from_name'] ?? null,
                'notification_to' => json_encode(array_filter($validated['notification_to'] ?? [])),
                'notification_cc' => json_encode(array_filter($validated['notification_cc'] ?? [])),

            ]);

            return redirect()
                ->route('settings.edit')
                ->with('success', 'Registo atualizado com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->route('settings.edit')
                ->with('error', 'Ocorreu um erro inesperado. Contacte o suporte.');
        }
    }

}
