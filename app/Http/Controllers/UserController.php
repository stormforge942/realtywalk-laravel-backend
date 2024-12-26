<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Jobs\SendMagicLinkEmail;
use App\Mail\ActivatedAccountMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Laracasts\Flash\Flash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\UserRegistration;
use Illuminate\Support\Facades\Mail;

class UserController extends AppBaseController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * List User as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable(Request $request)
    {
        $limit = $request->get('limit', 10);
        $skip = $request->get('start', 0);
        $columnSearch = $request->get('column', []);
        $order = $request->get('order', null);
        $total = User::count();
        $role = null;
        $recount = false;
        $otherFilter = [];

        if ($columnSearch) {
            foreach ($columnSearch as $search) {
                if (!$search['search']) continue;
                if ($search['name'] == 'role') {
                    $role = $search['search'];
                    continue;
                }

                $otherFilter[$search['name']] = $search['search'];
            }

            $recount = true;
        }

        $users = User::whereHas('roles', function ($q) use ($role, $order) {

            if ($role) {
                $q->where('name', 'LIKE', '%' . addslashes($role) . '%');
            }

            if ($order && $order['column'] == 'role') {
                $q->orderBy('name', ($order['asc'] == 'true' ? 'ASC' : 'DESC'));
            }

        })->skip($skip)->limit($limit)->orderBy(
            (($order && $order['column'] != 'role') ? $order['column'] : 'users.created_at'),
            (($order && $order['column'] != 'role') ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'ASC')
        );

        foreach ($otherFilter as $key => $val) {
            $users->where($key, 'LIKE', '%' . addslashes($val) . '%');
        }

        if ($recount) {
            $users = $users->get();
            $total = $users->count();
        } else {
            $users = $users->get();
        }

        $data = $users->map(function ($user) {
            $user->role = count($user->roles) > 0? $user->roles->first()->name: "N/A";

            if ($user->id == 1) {
                $user->disable_edit_btn = true;
            }

            if ($user->id == 1 || $user->id == Auth::user()->id) {
                $user->disable_delete_btn = true;
            }

            return $user;
        });

       return $this->respond(compact('data', 'total'));
    }

    public function me(Request $request)
    {
        if (!($user = Auth::user())) {
            return null;
        }

        $user->role = $user->roles ? $user->roles->first()->name : null;
        $user->property_favorites = $user->getSimpleFavoritedProperties();

        return $user;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->all();
        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        $roles = json_encode($this->getRoleOptions());

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->except('role');

        $user = $this->userRepository->create($input);
        $user->assignRole($request->input('role'));

        Mail::to($request->email)->send(new ActivatedAccountMail($user, $input['password']));
        Flash::success('User saved successfully.')->important();

        //return redirect(route('users.index'));
        return response()->json(['status'=>'success']);
    }

    public function register(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable',
            'cpassword' => 'nullable|same:password',
            recaptchaFieldName() => recaptchaRuleName(),
        );

        $messages = array(
            'required' => 'All fields are required',
            'same' => 'Passwords do not match',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator-> fails()){
            $errorString = implode(",", $validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $request->merge(['activation_token' => Str::random(64)]);

        $user = $this->userRepository->create($request->all());
        $user->assignRole('user');

        $activationLink = route('activation_link', $user->activation_token);
        $mail = new UserRegistration($request->all(), $activationLink);
        Mail::to($request->email)->send($mail);

        return $this->respond("User account registered successfully!");
    }

    public function activateAccount(Request $request, string $token)
    {
        $user = User::where('activation_token', $token)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return $this->respondWithError('The activation link is not valid!');
        }

        $user->activation_token = null;
        $user->email_verified_at = now();

        if (is_null($user->password)) {
            $password = generate_random_password();
            $user->password = $password;
        }

        $user->save();

        $mail = new ActivatedAccountMail($user, $password ?? null);
        Mail::to($user->email)->send($mail);

        return $this->respond(['message' => 'Your account is now active. You can log in using your credentials.']);
    }

    public function userSignin(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            recaptchaFieldName() => recaptchaRuleName(),
        ];

        $messages = array(
            'required' => 'All fields are required',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator-> fails()){
            $errorString = implode(",",$validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $credentials = array (
            'email' => $request->email,
            'password' => $request->password
        );

        $user = User::where('email', $request->email)->first();

        if ($user && is_null($user->email_verified_at)) {
            return $this->respondWithError(__('auth.email_is_not_verified'));
        }

        if (Auth::attempt($credentials)){
            $user = Auth::user();
            $access_token =  $user->createToken('Realty WALK token')->accessToken;
            $response = [
                'user'  => $user,
                'token' => $access_token,
            ];
            $response['user']['role'] = $user->roles ? $user->roles->first()->name : null;
            $response['user']['property_favorites'] = $user->getSimpleFavoritedProperties();

            return $this->respond($response);
        }

        return $this->respondWithError("Invalid login details");
    }

    public function sendMagicLogin(Request $request)
    {
        $rules = array(
            'email' => 'required|email|exists:users,email',
            recaptchaFieldName() => recaptchaRuleName(),
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());

            return $this->respondWithError($errorString);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (is_null($user->email_verified_at)) {
            return $this->respondWithError(__('auth.email_is_not_verified'));
        }

        $token = Str::random(64);
        $user->login_token = $token;
        $user->login_token_created_at = now();
        $user->save();

        $magicLink = route('magic.login', $token);
        SendMagicLinkEmail::dispatch($user, $magicLink);

        return $this->respond([
            'message' => 'Your magic link has been sent to your email!'
        ]);
    }

    public function attemptMagicLogin(Request $request, string $token)
    {
        $user = User::where('login_token', $token)
            ->where('login_token_created_at', '>', now()->subHours(24))
            ->first();

        if (!$user) {
            return $this->respondWithError('Magic link is either not available or expired!');
        }

        Auth::login($user);

        $user->login_token = null;
        $user->login_token_created_at = null;
        $user->save();

        $access_token =  $user->createToken('RealtyWalk token')->accessToken;
        $response = [
            'user'  => $user,
            'token' => $access_token
        ];

        $response['user']['role'] = $user->roles ? $user->roles->first()->name : null;
        $response['user']['property_favorites'] = $user->getSimpleFavoritedProperties();

        return $this->respond($response);
    }

    /**
     * Display the specified User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->find($id);
        $roles = json_encode($this->getRoleOptions());

        if (empty($user)) {
            Flash::error('User not found')->important();

            return redirect(route('users.index'));
        }

        $userRole = (count($user->roles) > 0) ? ucwords($user->roles->first()->name) : 'N/A';
        $builderId = $user->builder_id ?? 'null';

        return view('users.show', compact('user', 'roles', 'userRole', 'builderId'));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);
        $roles = json_encode($this->getRoleOptions());

        if (empty($user)) {
            Flash::error('User not found')->important();

            return redirect(route('users.index'));
        }

        if (Auth::user()->id != 1 && $user->id == 1) {
            Flash::error('You\'re not authorized to edit this user.')->important();

            return redirect(route('users.index'));
        }

        $userRole = (count($user->roles) > 0) ? $user->roles->first()->id : 'null';
        $builderId = $user->builder_id ?? 'null';

        return view('users.edit', compact('user', 'roles', 'userRole', 'builderId'));
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->find($id);

        /*
        if (empty($user)) {
            Flash::error('User not found')->important();

            return redirect(route('users.index'));
        }
*/
        if (Auth::user()->id != 1 && $user->id == 1) {
            Flash::error('You\'re not authorized to edit this user.')->important();

            return redirect(route('users.index'));
        }

        $input =  $request->except('role');

        if (empty($input['password'])) {
            unset($input['password']);
        }

        $user = $this->userRepository->update($input, $id);
        $user->syncRoles($request->input('role'));

        Flash::success('User updated successfully.')->important();

        //return redirect(route('users.index'));
        return response()->json(['status'=>'success']);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        $rules = ['name' => 'required'];

        if ($request->picture) {
            $rules["picture"] = 'sometimes|nullable|file|image|max:1000';
        }

        $messages = array(
            'required' => 'All fields are required',
            'same' => 'Passwords do not match',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $user = $this->userRepository->find($user->id);

        $data = $request->only('name');

        if (empty($user)) {
            return $this->respondWithError("User not found");
        }

        if ($request->file('picture')) {
            $fileName = $this->saveFile($request);
            $data['picture'] = $fileName;
        }

        $user = $this->userRepository->update($data, $user->id);

        return $this->respond([
            'user' => User::with('roles')->find($user->id),
            'message' => "User updated successfully"
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        $rules = [
            'currentPassword' => 'required',
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:newPassword'
        ];

        $messages = [
            'required' => 'All fields are required',
            'same' => 'Passwords do not match',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $user = $this->userRepository->find($user->id);

        if ($request->currentPassword && !Hash::check($request->currentPassword, $user->password)) {
            return $this->respondWithError("Your current password is incorrect");
        }

        if (empty($user)) {
            return $this->respondWithError("User not found");
        }

        $user->forceFill(['password' => $request->newPassword])->save();

        return $this->respond([
            'user' => User::with('roles')->find($user->id),
            'message' => "User password updated successfully"
        ]);
    }

    /**
     * Upload the file
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function saveFile(Request $request)
    {
        $file        = $request->file('picture');
        $fileContent = $file->get();
        $extension   = $file->extension();
        $filename = (string) Str::orderedUuid() . '.' . $extension;

        $image = \Intervention\Image\Facades\Image::make($file); //->crop(150, 150);
        $path = Storage::disk('Wasabi')->put('/uploads/'.$filename, $image->stream());

        return $filename;
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (empty($user)) {
            return $this->respondWithError('User not found.');
        }

        if (Auth::user()->id == $user->id || $user->id == 1) {
            return $this->respondWithError('You\'re not authorized to delete this user.');
        }

        $this->userRepository->delete($id);

        return $this->respond(['message' => '<strong>'.$user->name.'</strong> has been successfully deleted.']);
    }

    private function getRoleOptions()
    {
        $roles_query = Role::get();
        $roles = [];

        foreach ($roles_query as $item) {
            $roles[] = [
                'id' => $item->id,
                'label' => ucwords($item->name)
            ];
        }

        return $roles;
    }
}
