<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateZoneRequest;
use App\Http\Requests\UpdateZoneRequest;
use App\Repositories\ZoneRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Zone;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

use Response;

class ZoneController extends AppBaseController
{
    protected $zoneRepository;

    public function __construct(ZoneRepository $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * List Zones as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        // PREV > withDepth returns Exception
        // $data = Zone::withDepth()->get();

        //chnaged to use only get();
        $data = Zone::get();
        $data = $data->map(function ($item, $index) {
            $item->lat = $item->lat ?: '-';
            $item->lng = $item->lng ?: '-';

            return $item;
        });
        return $data;
    }

    /**
     * List Zones as json resource in tree data
     *
     * @param Request $request
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTree(Request $request)
    {
        if ($depth = $request->query('depth')) {
            // PREV > withDepth is marked with Exception
            // $nodes = Zone::withDepth()->having('depth', '<', $depth)->get()->toTree();

            //changed to only use get()
            $nodes= Zone::withDepth()->having("depth", "<", $depth)->get()->toTree();
        } else {
            $nodes = Zone::get()->toTree();
        }

        $data = $this->getNestedZones($nodes);
        return $data;
    }

    /**
     * List Cities as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getCity()
    {
        return Zone::withDepth()->having('depth', '=', '2')->get();
    }

    /**
     * Display a listing of the Zone.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $zones = $this->zoneRepository->all();

        return view('zones.index')
            ->with('zones', $zones);
    }

    /**
     * Show the form for creating a new Zone.
     *
     * @return Response
     */
    public function create()
    {
        return view('zones.create');
    }

    /**
     * Store a newly created Zone in storage.
     *
     * @param CreateZoneRequest $request
     *
     * @return Response
     */
    public function store(CreateZoneRequest $request)
    {
        $validated = $request->validated();

        $input = $request->all();

        $zone = $this->zoneRepository->create($input);

        if (empty($input['parent_id'])) {
            $zone->saveAsRoot();
        }

        Flash::success('Zone saved successfully.')->important();

        return response()->json(['status'=>'success']);

    }

    /**
     * Display the specified Zone.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $zone = $this->zoneRepository->find($id);

        if (empty($zone)) {
            Flash::error('Zone not found')->important();

            return redirect(route('zones.index'));
        }

        return view('zones.show')->with('zone', $zone);
    }

    /**
     * Show the form for editing the specified Zone.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $zone = Zone::withDepth()->findOrFail($id);

        if (empty($zone)) {
            Flash::error('Zone not found')->important();

            return redirect(route('zones.index'));
        }

        return view('zones.edit')->with('zone', $zone);
    }

    /**
     * Update the specified Zone in storage.
     *
     * @param int $id
     * @param UpdateZoneRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateZoneRequest $request)
    {
        $validated = $request->validated();

        $zone = $this->zoneRepository->find($id);

        if (empty($zone)) {
            Flash::error('Zone not found')->important();

            return redirect(route('zones.index'));
        }

        $zone = $this->zoneRepository->update($request->all(), $id);

        if (is_null($zone->parent_id)) {
            $zone->saveAsRoot();
        }

        Flash::success('Zone updated successfully.')->important();

        //return redirect(route('zones.index'));

        return response()->json(['status'=>'success']);
    }

    /**
     * Remove the specified Zone from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $zone = Zone::withDepth()->findOrFail($id);

        if (empty($zone)) {
            return $this->respondWithError('Zone not found.');
        }

        if ($zone->children) {
            return $this->respondWithError('Zone can\'t be removed because it has zone children.');
        }

        if ($zone->polygons) {
            return $this->respondWithError('Zone can\'t be removed because it has related polygons.');
        }

        $this->zoneRepository->delete($id);

        return $this->respond(['message' => '<strong>'.$zone->name.'</strong> has been successfully deleted.']);
    }

    public function getNestedZones($nodes)
    {
        $data = [];

        foreach ($nodes as $index => $node) {
            $data[$index]['id']    = $node->id;
            $data[$index]['label'] = $node->name;

            if ($node->children->count() > 0) {
                $data[$index]['children'] = $this->getNestedZones($node->children);
            }
        }

        return $data;
    }
}
