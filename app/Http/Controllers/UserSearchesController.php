<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserSearchesRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserSearchesRepository;
use App\Http\Controllers\AppBaseController;
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
use App\Models\UserSearches;

class UserSearchesController extends AppBaseController
{
    protected $userSearchesRepository;

    public function __construct(UserSearchesRepository $userRepository)
    {
        $this->userSearchesRepository = $userRepository;
    }

     /**
     * Store a newly created UserSearches in storage.
     *
     * @param CreateUserSearchesRequest $request
     *
     * @return Response
     */
    public function store(CreateUserSearchesRequest $request)
    {
        $response = ['status' => 'success'];

        try {
            $validated = $request->validated();
            $input = $request->all();
            $user = $this->userSearchesRepository->create($input);
        } catch (\Exception $e) {
            $response['status'] = 'failed';
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }

     /**
     * Display the specified UserSearches.
     *
     * @param int $id
     *
     * @return Response
     */
    public function list($id)
    {
        $userSearches = UserSearches::where('user_id', '=', $id)->get();

        return response()->json($userSearches);
    }

    /**
     * Display the specified UserSearches.
     *
     * @param int $id
     *
     * @return Response
     */
    public function find($id)
    {
        $userSearches = UserSearches::where('id', '=', $id)->first();

        return response()->json($userSearches);
    }

    /**
     * Remove the specified UserSearches from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $response = ['status' => 'success'];
        try {
            $user = $this->userSearchesRepository->delete($id);
        } catch (\Exception $e) {
            $response['status'] = 'failed';
            $response['message'] = $e->getMessage();
        }

        return $this->respond($response);
    }
}
