<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laracasts\Flash\Flash;

class ProfileController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Show the application profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->userRepository->find(Auth::user()->id);

        return view('profile.index', compact('user'));
    }

    /**
     * Update current user profile
     *
     * @param  UpdateProfileRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $this->userRepository->find(Auth::user()->id);
        $oldFile = false;

        if ($hasFile = $request->hasFile('file')) {
            $oldFile = $user->picture;
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::orderedUuid() . '.' . $extension;
            $request->merge(['picture' => $filename]);
        }

        $input =  $request->except('file');

        if (empty($input['password'])) {
            unset($input['password']);
        }

        $user = $this->userRepository->update($input, Auth::user()->id);

        // if saved and has file, save it
        if ($user && $hasFile) {
            // fit the image
            $image = Image::make($file->getRealPath());
            $image->fit(250)->save(storage_path('app/public/avatar') . '/' .$filename);
        }

        // if saved and current user has profile picture,
        // remove it from storage
        if ($user && $oldFile) {
            Storage::disk('public')->delete('avatar/'.$oldFile);
        }

        Flash::success('Your profile has been successfully updated.')->important();

        return redirect(route('profile.index'));
    }
}
