<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStatisticTypeRequest;
use App\Http\Requests\UpdateStatisticTypeRequest;
use App\Repositories\StatisticTypeRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\StatisticType;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Response;

class StatisticTypeController extends AppBaseController
{
    protected $statisticTypeRepository;

    public function __construct(StatisticTypeRepository $statisticTypeRepository)
    {
        $this->statisticTypeRepository = $statisticTypeRepository;
    }

    /**
     * List Styles as json resource for datatable
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        $data = StatisticType::get();

        return $data->map(function ($item) {
            $item->name = $item->name .' ('.$item->format.')';

            return $item;
        });
    }

    /**
     * Display a listing of the Statistic Types.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return redirect(route('statistics.index'));
    }

    /**
     * Show the form for creating a new StatisticType.
     *
     * @return Response
     */
    public function create()
    {
        return view('statistic_types.create');
    }

    /**
     * Store a newly created StatisticType in storage.
     *
     * @param CreateStatisticTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateStatisticTypeRequest $request)
    {
        $validated = $request->validated();

        $input = $request->all();

        $statisticType = $this->statisticTypeRepository->create($input);

        Flash::success('Statistic type saved successfully.')->important();

        //return redirect(route('statistics.index'));

        return response()->json(['status'=>'success']);
    }

    /**
     * Display the specified StatisticType.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $statisticType = $this->statisticTypeRepository->find($id);

        if (empty($statisticType)) {
            Flash::error('Statistic type not found')->important();

            return redirect(route('statistics.index'));
        }

        return view('statistic_types.show')->with('statisticType', $statisticType);
    }

    /**
     * Show the form for editing the specified StatisticType.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $statisticType = $this->statisticTypeRepository->find($id);

        if (empty($statisticType)) {
            Flash::error('Statistic type not found')->important();

            return redirect(route('statistics.index'));
        }

        return view('statistic_types.edit')->with('statisticType', $statisticType);
    }

    /**
     * Update the specified StatisticType in storage.
     *
     * @param int $id
     * @param UpdateStatisticTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStatisticTypeRequest $request)
    {
        $validated = $request->validated();

        $statisticType = $this->statisticTypeRepository->find($id);

        if (empty($statisticType)) {
            Flash::error('Statistic type not found')->important();

            return redirect(route('statistics.index'));
        }

        $statisticType = $this->statisticTypeRepository->update($request->all(), $id);

        Flash::success('Statistic type updated successfully.')->important();

        //return redirect(route('statistics.index'));

        return response()->json(['status'=>'success']);
    }

    /**
     * Remove the specified StatisticType from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $statisticType = $this->statisticTypeRepository->find($id);

        if (empty($statisticType)) {
            return $this->respondWithError('Statistic type not found.');
        }

        if ($statisticType->statistics->count()) {
            return $this->respondWithError('This type can\'t be removed because it has related statistics.');
        }

        $this->statisticTypeRepository->delete($id);

        return $this->respond(['message' => '<strong>' . $statisticType->name . '</strong> has been successfully deleted.']);
    }
}
