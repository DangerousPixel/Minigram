<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

class ProfilesController extends Controller
{
    public function index(User $user){

        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id): false;
        return view('profiles.index', compact('user', 'follows' ));


    }

    public function edit(User $user){
        $this->authorize('update', $user->profile);

        return view('profiles.edit' , compact('user'));

    }

    public function update(User $user) {
       // dd($user,\request('image'));
        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
            ]);


        if (request('image')){
            $imagePath = request('image')->store('profile','public');
            $image = Image::make(public_path("storage/{$imagePath}"));
            $image->save();
            $imageArray = ['image' =>$imagePath];
        }

        $user->profile->update(array_merge(
            $data ,
            $imageArray ?? []
        ));

        return redirect()->route('profile.show',compact($user));
        }
}
