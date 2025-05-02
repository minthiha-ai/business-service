<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BusinessProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profiles = $user->businessProfiles()->with('categories')->latest()->get();

        return response()->json([
            'message' => 'Business profiles fetched successfully.',
            'data' => $profiles
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string',
            'website'     => 'nullable|url',
            'logo_path'   => 'nullable|string',
            'status'      => 'nullable|in:active,inactive,pending',
            'categories'  => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile = $user->businessProfiles()->create($validator->validated());

        if ($request->has('categories')) {
            $profile->categories()->sync($request->categories);
        }

        return response()->json([
            'message' => 'Business profile created successfully.',
            'data' => $profile->load('categories')
        ], 201);
    }

    public function show($id)
    {
        $profile = Auth::user()->businessProfiles()->with('categories')->findOrFail($id);

        return response()->json([
            'message' => 'Business profile fetched successfully.',
            'data' => $profile
        ]);
    }

    public function update(Request $request, $id)
    {
        $profile = Auth::user()->businessProfiles()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string',
            'website'     => 'nullable|url',
            'logo_path'   => 'nullable|string',
            'status'      => 'nullable|in:active,inactive,pending',
            'categories'  => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile->update($validator->validated());

        if ($request->has('categories')) {
            $profile->categories()->sync($request->categories);
        }

        return response()->json([
            'message' => 'Business profile updated successfully.',
            'data' => $profile->load('categories')
        ]);
    }

    public function destroy($id)
    {
        $profile = Auth::user()->businessProfiles()->findOrFail($id);
        $profile->delete();

        return response()->json([
            'message' => 'Business profile deleted successfully.'
        ]);
    }
}
