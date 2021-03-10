<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Posts\Category;
use App\Models\Posts\File;
use App\Models\Posts\Level;
use App\Models\Posts\Post;
use App\Models\Posts\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('category')->orderBy('id', 'DESC')->paginate();
        $paginatedLinks = paginationLinks($posts);

        return Inertia::render('Dashboard/Posts/ListPosts', compact('posts', 'paginatedLinks'));
    }

    public function create(Request $request)
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $levels = [
            ['id' => 'AGENT', 'name' => 'نمایندگان',],
            ['id' => 'MARKETER', 'name' => 'بازاریابان'],
            ['id' => 'TECHNICAL', 'name' => 'کارشناسان فنی'],
            ['id' => 'OFFICE', 'name' => 'کارمندان دفتر'],
        ];
        return Inertia::render('Dashboard/Posts/CreatePost', compact('categories', 'levels'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('postForm', [
            'title' => 'required',
            'body' => 'required',
        ]);

        $post = Post::create($request->all());

        $levels = $request->get('levels');
        foreach ($levels as $level) {
            Level::create([
                'post_id' => $post->id,
                'level' => $level
            ]);
        }

        $uploadedFiles = $request->file('uploadFiles', []);
        if (!is_null($uploadedFiles) && count($uploadedFiles) > 0) {
            foreach ($uploadedFiles as $file) {
                $fileName = $file->getClientOriginalName();
                $file->storeAs('posts/' . $post->id . '/files', $fileName, 'public');
                File::create([
                    'post_id' => $post->id,
                    'name' => $fileName
                ]);
            }
        }

        $uploadedVideos = $request->file('uploadVideos', []);
        if (!is_null($uploadedVideos) && count($uploadedVideos) > 0) {
            foreach ($uploadedVideos as $video) {
                $fileName = Str::uuid() . '.' . $video->getClientOriginalExtension();
                $video->storeAs('posts/' . $post->id . '/videos', $fileName, 'public');
                Video::create([
                    'post_id' => $post->id,
                    'name' => $fileName
                ]);
            }
        }

        return redirect()->route('dashboard.posts.list');
    }

    public function view(Request $request)
    {
        $id = $request->route('postId');
        $post = Post::with('category')->with('files')->with('videos')->find($id);

        return Inertia::render('Dashboard/Posts/ViewPost', compact('post'));
    }

    public function edit(Request $request)
    {
        $id = $request->route('postId');
        $post = Post::with('levels')
            ->with('files')
            ->with('videos')
            ->find($id);

        $categories = Category::orderBy('name', 'ASC')->get();
        $levels = [
            ['id' => 'AGENT', 'name' => 'نمایندگان',],
            ['id' => 'MARKETER', 'name' => 'بازاریابان'],
            ['id' => 'TECHNICAL', 'name' => 'کارشناسان فنی'],
            ['id' => 'OFFICE', 'name' => 'کارمندان دفتر'],
        ];
        return Inertia::render('Dashboard/Posts/EditPost', compact('post', 'categories', 'levels'));
    }

    public function update(Request $request)
    {
        $id = $request->route('postId');
        $post = Post::with('levels')
            ->with('files')
            ->with('videos')
            ->find($id);
        $request->validateWithBag('postForm', [
            'title' => 'required',
            'body' => 'required',
        ]);

        $post->fill($request->all());
        $post->save();

        $levels = $request->get('levels');
        Level::where('post_id', $post->id)->delete();
        foreach ($levels as $level) {
            Level::create([
                'post_id' => $post->id,
                'level' => $level
            ]);
        }

        $deleteFiles = $request->get('deleteFiles', []);
        if (!is_null($deleteFiles) && count($deleteFiles) > 0) {
            foreach ($deleteFiles as $deleteFile) {
                $dFile = File::find($deleteFile);
                Storage::disk('public')->delete('posts/' . $post->id . '/files/' . $dFile->name);
                $dFile->delete();
            }
        }

        $uploadedFiles = $request->file('uploadFiles', []);
        if (!is_null($uploadedFiles) && count($uploadedFiles) > 0) {
            foreach ($uploadedFiles as $file) {
                $fileName = $file->getClientOriginalName();
                $file->storeAs('posts/' . $post->id . '/files', $fileName, 'public');
                File::create([
                    'post_id' => $post->id,
                    'name' => $fileName
                ]);
            }
        }

        $deleteVideos = $request->get('deleteVideos', []);
        if (!is_null($deleteVideos) && count($deleteVideos) > 0) {
            foreach ($deleteVideos as $deleteVideo) {
                $dVideo = Video::find($deleteVideo);
                Storage::disk('public')->delete('posts/' . $post->id . '/videos/' . $dVideo->name);
                $dVideo->delete();
            }
        }

        $uploadedVideos = $request->file('uploadVideos', []);
        if (!is_null($uploadedVideos) && count($uploadedVideos) > 0) {
            foreach ($uploadedVideos as $video) {
                $fileName = $video->getClientOriginalName();
                $video->storeAs('posts/' . $post->id . '/videos', $fileName, 'public');
                Video::create([
                    'post_id' => $post->id,
                    'name' => $fileName
                ]);
            }
        }

        return redirect()->route('dashboard.posts.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('postId');
        $post = Post::with('files')->with('videos')->find($id);

        foreach ($post->files as $file) {
            Storage::disk('public')->delete('posts/' . $post->id . '/files/' . $file->name);
        }

        foreach ($post->videos as $video) {
            Storage::disk('public')->delete('posts/' . $post->id . '/videos/' . $video->name);
        }

        $post->delete();

        return redirect()->route('dashboard.posts.list');
    }

    public function archive(Request $request)
    {
        $user = Auth::user();
        $categories = Category::withCount('posts')->orderBy('id', 'ASC')->get();

        $categoryId = (int)$request->query('category', null);
        $searchQuery = $request->query('query', null);
        $postsQuery = Post::with('category')->where(function ($query) use ($categoryId, $searchQuery) {
            if ($categoryId !== 0) {
                $query->where('post_category_id', $categoryId);
            }
            $query->where('title', 'LIKE', '%' . $searchQuery . '%');
        });

        if (!$user->isAdmin() && !$user->isSuperuser()) {
            $userPosts = Level::where('level', $user->level)->pluck('post_id');
            $postsQuery->whereIn('id', $userPosts);
        }
        $posts = $postsQuery->where(function ($query) use ($user) {
            $query->where('status', 1);
        })->orderBy('id', 'DESC')->paginate(12);
        $paginatedLinks = paginationLinks($posts);

        $posts->each(function ($post) {
            $post->body = substr($post->body, 0, 250) . '...';
        });

        return Inertia::render('Dashboard/Posts/Archive', compact('searchQuery', 'paginatedLinks', 'categoryId', 'posts', 'categories'));
    }
}




