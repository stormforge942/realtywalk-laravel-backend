<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Polygon;
use App\Models\SEOUrl;
use App\User;
use Illuminate\Http\Request;

class RoutesController extends Controller
{
    public function neighborhoods($path = null)
    {
        if (!$path) {
            abort(404);
        }

        $path = rtrim($path, '/');
        if (SEOUrl::wherePath("/neighborhood/{$path}")->exists()) {
            return view('builders.frontend');
        }

        if (Polygon::where('slug', $path)->exists()) {
            return view('builders.frontend');
        }

        return abort(404);
    }

    public function properties($path = null)
    {
        if (is_a_number($path)) {
            $is_exists = Property::where('id', (int) $path)->exists();
        } else {
            $path = rtrim($path, '/');
            $is_exists = Property::where('path_url', "/property/{$path}")->exists();
        }

        if (!$is_exists) {
            abort(404);
        }

        return view('properties.frontend');
    }

    public function activateAccount(Request $request, string $token)
    {
        $user = User::query()
            ->where('activation_token', $token)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return redirect('/404');
        }

        return view('builders.frontend');
    }
}
