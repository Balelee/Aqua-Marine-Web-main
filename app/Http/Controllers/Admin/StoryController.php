<?php


namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Services\FileService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Ramsey\Uuid\Uuid;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Validator;
use Throwable;

class StoryController extends Controller
{

    private string $storage_space;

    public function __construct(){

        $storage =  DB::table('image_space')->first();

        if($storage->aws == 1){
            $this->storage_space = "s3.aws";
        }
        else if($storage->digital_ocean == 1){
            $this->storage_space = "s3.digitalocean";
        }else{
            $this->storage_space ="same_server";
        }

    }

    public function store(Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'type' => 'required|in:text,image,video',
                    'text' => 'nullable|string|required_if:type,text',
                    'text_color' => 'required',
                    'background_color' => 'required',
                    'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
                    'video' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
                ],
                [
                    'type.required' => 'Enter story title.',
                    'text.required' => 'Enter story text.',
                ]
            );

            $admin_email = Auth::guard('admin')->user()->email;
            $admin = DB::table('admin')->first();
            $admin_id = $admin->id;
            $admin_name = $admin->name;

            $content = null;
            $thumbnail = null;

            Log::info('Request received for storing a story', ['request' => $admin]);
            Log::info('Request received for storing a story', ['request' => $admin_email]);
            Log::info('Request received for storing a story', ['request' => $request->all()]);

            // Gestion des fichiers image
            if ($request->hasFile('image')) {
                $imagePath = FileService::compressAndUpload($request->file('image'), 'stories');
                $content = $imagePath;
            }

            // Gestion des fichiers vidéo
            if ($request->hasFile('video')) {
                $videoPath = FileService::upload($request->file('video'), 'stories');
                $content = $videoPath;

                // Générer un thumbnail si c'est une vidéo
                $thumbnail = $this->generateVideoThumbnail($videoPath);
//                Log::info('Thumbnail', ['request' => $thumbnail]);
            }

            Log::info('Request $content', ['request' => $content]);

// Préparer les données de la story
            $storyData = [
                'type' => $request['type'],
                'background_color' => $request['background_color'],
                'text_color' => $request['text_color'],
                'text' => $request['text'],
                'userId' => $admin_id,
                'name' => "Aqua-Marine",
                'content' => $content,
                'thumbnail' => $thumbnail,
                'is_seen' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Log::info('Inserting new story', ['story_data' => $storyData]);

            $story = DB::table('stories')->insert($storyData);

            Log::info('Story stored successfully', ['story' => $story]);
            return redirect()->back()->withSuccess(trans('keywords.Updated Successfully'));

        } catch (Throwable $th) {
            Log::error('Error in storing story', ['exception' => $th]);
            return redirect()->back()->withErrors(trans('keywords.Something Went Wrong'));
        }
    }

    public function adminStorySend(Request $request)
    {
        $title = "To Users";

        $admin_email=Auth::guard('admin')->user()->email;

        $admin= DB::table('admin')
            ->leftJoin('roles','admin.role_id','=','roles.role_id')
            ->where('admin.email', $admin_email)
            ->first();

        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();

        if($this->storage_space != "same_server"){
            $url_aws =  rtrim(Storage::disk($this->storage_space)->url('/'),"/");
        } else{
            $url_aws=url('/');
        }

        $users = DB::table('users')
            ->join('city', 'users.user_city','=', 'city.city_id')
            ->join('society', 'users.user_area','=', 'society.society_id')
            ->join('store', 'city.city_name','=', 'store.city')
            ->select('users.name', 'users.id','city.city_name','society.society_name')
            ->groupBy('users.name', 'users.id','city.city_name','society.society_name')
            ->get();

        return view('admin.settings.storynotification',compact("title","admin", "logo", "admin","users", "url_aws"));
    }

    public function adminStoryList(Request $request)
    {
        $title = "To Users";

        $admin_email=Auth::guard('admin')->user()->email;

        $admin= DB::table('admin')
            ->leftJoin('roles','admin.role_id','=','roles.role_id')
            ->where('admin.email',$admin_email)
            ->first();
        $logo = DB::table('tbl_web_setting')
            ->where('set_id', '1')
            ->first();
        $notification = DB::table('driver_notification')
            ->join('delivery_boy', 'driver_notification.dboy_id','=', 'delivery_boy.dboy_id')
            ->select('delivery_boy.boy_name','driver_notification.*')
            ->paginate(20);
        if($this->storage_space != "same_server"){
            $url_aws =  rtrim(Storage::disk($this->storage_space)->url('/'),"/");
        }
        else{
            $url_aws=url('/');
        }

        $stories = Story::paginate($request->get('per_page', 10));


        return view('admin.settings.storylist',compact("title", "stories", "admin", "logo", "admin", "url_aws"));

    }

    public function getUserStories(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            // Récupérer toutes les histoires de l'utilisateur avec l'ID donné
            $stories = Story::where('user_id', $request->user_id)->get();
            Log::info('Request received for getting user stories', ['stories' => $stories]);

            return response()->json(['data' => $stories, 'status' => 200]);
        } catch (Throwable $th) {
            Log::error('Error in getUserStories', ['exception' => $th]);
            return response()->json(['message' => 'Something went wrong', 'error' => true], 500);
        }
    }

    public function delete_all_stories()
    {
        try {
            // Récupérer toutes les stories
            $stories = Story::all();

            foreach ($stories as $story) {
                // Supprimer les images associées
                if ($story->image && Storage::exists($story->image)) {
                    Storage::delete($story->image);
                }

                // Supprimer la story
                $story->delete();
            }

            // Rediriger avec un message de succès
            return redirect()->back()->with('success', __('All stories have been deleted successfully.'));
        } catch (\Exception $e) {
            // Gérer les erreurs
            return redirect()->back()->withErrors(__('Error deleting all stories: ') . $e->getMessage());
        }
    }

    public function delete_story($id)
    {
        try {
            // Trouver la story par ID
            $story = Story::findOrFail($id);

            // Supprimer l'image associée (si elle existe)
            if ($story->image && Storage::exists($story->image)) {
                Storage::delete($story->image);
            }

            // Supprimer la story
            $story->delete();

            // Rediriger avec un message de succès
            return redirect()->back()->with('success', __('Story deleted successfully.'));
        } catch (\Exception $e) {
            // Gérer les erreurs
            return redirect()->back()->withErrors(__('Error deleting story: ') . $e->getMessage());
        }
    }

    public function edit_story(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Récupérer la story existante
            $story = Story::findOrFail($id);

            // Mettre à jour les champs
            $story->title = $request->input('title');
            $story->content = $request->input('content');

            // Gérer l'upload de la nouvelle image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image (si elle existe)
                if ($story->image && Storage::exists($story->image)) {
                    Storage::delete($story->image);
                }

                // Sauvegarder la nouvelle image
                $story->image = $request->file('image')->store('stories');
            }

            // Sauvegarder les modifications
            $story->save();

            // Rediriger avec un message de succès
            return redirect()->back()->with('success', __('Story updated successfully.'));
        } catch (\Exception $e) {
            // Gérer les erreurs
            return redirect()->back()->withErrors(__('Error updating story: ') . $e->getMessage());
        }
    }

    public function getAllStories(): \Illuminate\Http\JsonResponse
    {
        try {
            Log::info('Request received for getting all stories');

            // Récupérer toutes les histoires
            $stories = Story::all();

            Log::info('Stories retrieved successfully', ['stories_count' => $stories->count()]);

            return response()->json(['data' => $stories, 'status' => 200]);
        } catch (Throwable $th) {
            Log::error('Error in getAllStories', ['exception' => $th]);
            return response()->json(['message' => 'Something went wrong all story', 'error' => true], 500);
        }
    }

    public function delete(Request $request)
    {
        Log::info('Request received for getting user $story', ['$story' => $request]);
        Log::info('Request received for getting user $story', ['$story' => $request->storyId]);
        try {
            // Récupérer la story à supprimer
            $story = Story::findOrFail($request->storyId);

            FileService::delete($story->getRawOriginal('image'));

            // Supprimer la story
            $story->delete();

            return response()->json(['message' => 'Story deleted successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete story'], 500);
        }
    }

    /**
     * Générer une miniature pour une vidéo.
     *
     * @param string $videoPath Chemin de la vidéo dans le stockage
     * @return string|null URL de la miniature ou null en cas d'échec
     */

    public function generateVideoThumbnail($videoPath)
    {
        try {
            $fullPath = storage_path('app/public/' . $videoPath);

            if (!file_exists($fullPath)) {
                return null;
            }

            $thumbnailName = Str::uuid() . '.png';
            $thumbnailRelativePath = 'thumbnails/' . $thumbnailName;
            $thumbnailStoragePath = 'public/' . $thumbnailRelativePath;
            $thumbnailAbsolutePath = storage_path('app/' . $thumbnailStoragePath);

            $thumbnailDirectory = dirname($thumbnailAbsolutePath);
            if (!file_exists($thumbnailDirectory)) {
                mkdir($thumbnailDirectory, 0777, true);
            }

            // Générer la miniature à la 2e seconde
            FFMpeg::fromDisk('public')
                ->open($videoPath)
                ->getFrameFromSeconds(2)
                ->export()
                ->toDisk('public')
                ->save($thumbnailRelativePath);

            if (file_exists($thumbnailAbsolutePath)) {
                // 🔁 Retourner le chemin relatif pour la base de données
                return 'thumbnails/' . $thumbnailName;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error generating thumbnail', ['error' => $e->getMessage()]);
            return null;
        }
    }


}
