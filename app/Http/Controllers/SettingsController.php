<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    private const DEFAULTS = [
        'business_name' => 'CREAPIX',
        'business_description' => 'Estudio creativo',
        'business_logo' => null,
        'business_nit' => '',
        'business_phone' => '',
        'business_whatsapp' => '',
        'business_email' => '',
        'business_address' => '',
        'business_city' => '',
        'business_country' => 'Bolivia',
        'business_social' => '',
        'currency' => 'BOB',
        'currency_symbol' => 'Bs',
        'tax_percent' => null,
        'date_format' => 'd/m/Y',
        'order_prefix' => 'PED',
        'quote_prefix' => 'COT',
    ];

    public function index(): Response
    {
        $settings = array_merge(self::DEFAULTS, BusinessSetting::allJson());

        return Inertia::render('configuracion/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'business_name' => ['required', 'string', 'max:255'],
            'business_description' => ['nullable', 'string', 'max:255'],
            'business_nit' => ['nullable', 'string', 'max:30'],
            'business_phone' => ['nullable', 'string', 'max:30'],
            'business_whatsapp' => ['nullable', 'string', 'max:30'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_address' => ['nullable', 'string', 'max:255'],
            'business_city' => ['nullable', 'string', 'max:100'],
            'business_country' => ['nullable', 'string', 'max:100'],
            'business_social' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'date_format' => ['required', 'string', 'max:20'],
            'order_prefix' => ['required', 'string', 'max:10'],
            'quote_prefix' => ['required', 'string', 'max:10'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('business_logo')) {
            $logo = $request->file('business_logo');
            if (! in_array($logo->getClientOriginalExtension(), ['png', 'jpg', 'jpeg', 'svg', 'webp'])) {
                return back()->withErrors(['business_logo' => 'Formato de logo no válido.']);
            }
            $path = $logo->store('business', 'public');
            BusinessSetting::set('business_logo', $path);
        }

        foreach ($request->except(['business_logo', '_method']) as $key => $value) {
            if (array_key_exists($key, self::DEFAULTS) && $key !== 'business_logo') {
                BusinessSetting::set($key, $value);
            }
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Configuración guardada correctamente.']);

        return back();
    }

    public function destroyLogo(): RedirectResponse
    {
        $logo = BusinessSetting::get('business_logo');
        if ($logo) {
            Storage::disk('public')->delete($logo);
            BusinessSetting::set('business_logo', null);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Logo eliminado.']);

        return back();
    }
}
