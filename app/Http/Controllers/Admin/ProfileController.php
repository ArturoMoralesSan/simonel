<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Update the user's profile data.
     *
     * @param  \App\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $user->avatar = $this->saveAvatar($user, $request->file('avatar'));
        }

        $user->name      = $request->name;
        $user->last_name = $request->last_name;
        $user->email     = $request->email;
        $user->save();

        alert('Se han actualizado los datos.');

        return response('', 204, [
            'Redirect-To' => url('admin/perfil/editar')
        ]);
    }

    /**
     * Replace the avatar image in storage and return the generated filename.
     *
     * @param  \App\Models\User $user
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string
     */
    protected function saveAvatar($user, $file)
    {
        $avatarFilename = $user->id . '-' . str_slug(str_random(20)) . '.jpg';

        Image::make($file)
            ->fit(68, 68)
            ->save(storage_path('app/public/avatars/') . $avatarFilename, 60);

        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        return $avatarFilename;
    }
}


