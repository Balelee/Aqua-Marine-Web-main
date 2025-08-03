<?php

namespace App\Http\Controllers;
use App\Models\Story;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiStoryController extends Controller
{

    public function show($id)
{
    try {
        $story = Story::findOrFail($id);
        $story->content = $story->content ? asset('storage/' . $story->content) : null;

        return response()->json([
            'status' => '1',
            'message' => 'Story fetched successfully.',
            'data' => $story
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => '0',
            'message' => 'Story not found: ' . $e->getMessage(),
        ], 404);
    }
}

    public function destroy($id)
    {
        try {
            $story = Story::findOrFail($id);

            // Supprimer le fichier associé s'il existe
            if ($story->content && Storage::disk('public')->exists($story->content)) {
                Storage::disk('public')->delete($story->content);
            }

            // Supprimer la story
            $story->delete();

            return response()->json([
                'status' => '1',
                'message' => 'Story deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => '0',
                'message' => 'Error deleting story: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_stories()
    {
        try {
            // Récupérer uniquement les stories créées aujourd'hui
            $stories = Story::select('id', 'type', 'text', 'content', 'thumbnail', 'name', 'userId', 'background_color', 'text_color', 'created_at', 'is_seen')
                ->whereDate('created_at', Carbon::today())
                ->orderBy('created_at', 'desc')
                ->get();

            // Ajouter l'URL complète pour chaque story
            $stories->map(function ($story) {
                // Ajouter l'URL complète pour le contenu vidéo ou image
                $story->content = $story->content ? asset('storage/' . $story->content) : null;
                return $story;
            });

            // Retourner la liste des stories
            return response()->json([
                'status' => '1',
                'message' => 'Stories fetched successfully',
                'data' => $stories,
            ], 200);
        } catch (\Exception $e) {
            // Gérer les erreurs
            return response()->json([
                'status' => '0',
                'message' => 'Error fetching stories: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markAsSeen($id)
    {
        try {
            Log::info('Story id', ['story' => $id]);

            $story = Story::findOrFail($id);
            $story->is_seen = 1;
            $story->save();

            return response()->json([
                'status' => '1',
                'message' => 'Story marked as seen successfully.',
                'data' => $story
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => '0',
                'message' => 'Error marking story as seen: ' . $e->getMessage(),
            ], 500);
        }
    }

}
