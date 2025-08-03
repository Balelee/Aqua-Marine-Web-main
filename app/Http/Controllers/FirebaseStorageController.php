<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;

class FirebaseStorageController extends Controller
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->hasFile('file') || !$request->has('type')) {
            return response()->json(['error' => 'Fichier ou type manquant'], 400);
        }

        $file = $request->file('file');
        $type = strtolower($request->input('type'));


        $allowedTypes = ['image', 'video', 'audio', 'voice'];
        if (!in_array($type, $allowedTypes)) {
            return response()->json(['error' => 'Type invalide'], 422);
        }

        // Lire le fichier de credentials Firebase
        $credentialsFilePath = 'firebase/aquaAdminSdk.json';
        $credentialsContent = Storage::disk('public')->get($credentialsFilePath);

        $firebase = (new Factory)
            ->withServiceAccount($credentialsContent)
            ->createStorage();

        $bucket = $firebase->getBucket();

        $filename = $type . 's/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        $bucket->upload(
            fopen($file->getRealPath(), 'r'),
            ['name' => $filename]
        );

        $url = "https://firebasestorage.googleapis.com/v0/b/{$bucket->name()}/o/" . urlencode($filename) . "?alt=media";

        return response()->json([
            'url' => $url,
            'type' => $type,
            'name' => $file->getClientOriginalName(),
            'path' => $filename,
        ]);
    }
}
