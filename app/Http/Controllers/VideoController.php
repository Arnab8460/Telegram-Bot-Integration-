<?php

namespace App\Http\Controllers;

use App\Models\VideoLink;

class VideoController extends Controller
{
    public function show($slug)
{
    $video = VideoLink::where('slug', $slug)->firstOrFail();

    return view('video.show', compact('video'));
}

}
