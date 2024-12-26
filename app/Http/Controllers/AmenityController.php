<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAmenityRequest;
use App\Http\Requests\UpdateAmenityRequest;
use App\Repositories\AmenityRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Response;

class AmenityController extends AppBaseController
{
    protected $amenityRepository;

    public function __construct(AmenityRepository $amenityRepository)
    {
        $this->amenityRepository = $amenityRepository;
    }

    /**
     * List Amenity as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        $data = Amenity::orderBy('name', 'asc')->get();

        return $this->respond($data);
    }

    /**
     * Display a listing of the Amenity.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $amenities = $this->amenityRepository->all();

        return view('amenities.index')->with('amenities', $amenities);
    }

    /**
     * Show the form for creating a new Amenity.
     *
     * @return Response
     */
    public function create()
    {
        return view('amenities.create');
    }

    /**
     * Store a newly created Amenity in storage.
     *
     * @param CreateAmenityRequest $request
     *
     * @return Response
     */
    public function store(CreateAmenityRequest $request)
    {
        $validated = $request->validated();
        $input = $request->all();
        $this->amenityRepository->create($input);
        Flash::success('Amenity saved successfully.')->important();

        return response()->json(['status'=>'success']);
    }

    /**
     * Display the specified Amenity.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $amenity = $this->amenityRepository->find($id);

        if (empty($amenity)) {
            Flash::error('Amenity not found')->important();

            return redirect(route('amenities.index'));
        }

        return view('amenities.show')->with('amenity', $amenity);
    }

    /**
     * Show the form for editing the specified Amenity.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $amenity = $this->amenityRepository->find($id);

        if (empty($amenity)) {
            Flash::error('Amenity not found')->important();

            return redirect(route('amenities.index'));
        }

        return view('amenities.edit')->with('amenity', $amenity);

    }

    /**
     * Update the specified Amenity in storage.
     *
     * @param int $id
     * @param UpdateAmenityRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAmenityRequest $request)
    {
        $validated = $request->validated();
        $amenity = $this->amenityRepository->find($id);

        if (empty($amenity)) {
            Flash::error('Amenity not found')->important();

            return response()->json(['status'=>'error']);
        }

        $amenity = $this->amenityRepository->update($request->all(), $id);
        Flash::success('Amenity updated successfully.')->important();

        return response()->json(['status'=>'success']);
    }

    /**
     * Remove the specified Amenity from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $amenity = $this->amenityRepository->find($id);

        if (empty($amenity)) {
            return $this->respondWithError('Amenity not found.');
        }

        $this->amenityRepository->delete($id);

        return $this->respond([
            'message' => '<strong>' . $amenity->name . '</strong> has been successfully deleted.'
        ]);
    }
}
