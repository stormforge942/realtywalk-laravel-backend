<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStatisticRequest;
use App\Http\Requests\UpdateStatisticRequest;
use App\Repositories\StatisticRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Statistic;
use App\Models\StatisticType;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Response;

class StatisticController extends AppBaseController
{
    protected $statisticRepository;

    public function __construct(StatisticRepository $statisticRepository)
    {
        $this->statisticRepository = $statisticRepository;
    }

    /**
     * List Styles as json resource for datatable
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        $data = Statistic::with('type')->get();

        return $data->map(function ($item) {
            $item->type_name = $item->type ? $item->type->name : '-';

            return $item;
        });
    }

    /**
     * Display a listing of the Statistic.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $statistics = $this->statisticRepository->all();

        return view('statistics.index')
            ->with('statistics', $statistics);
    }

    /**
     * Show the form for creating a new Statistic.
     *
     * @return Response
     */
    public function create()
    {
        $types = [];
        foreach (StatisticType::get() as $item) {
            $types[$item->id] = $item->name;
        }

        return view('statistics.create', compact('types'));
    }

    /**
     * Store a newly created Statistic in storage.
     *
     * @param CreateStatisticRequest $request
     *
     * @return Response
     */
    public function store(CreateStatisticRequest $request)
    {
        $validated = $request->validated();

        $input = $request->all();

        $statistic = $this->statisticRepository->create($input);

        Flash::success('Statistic saved successfully.')->important();

        //return redirect(route('statistics.index'));

        return response()->json(['status'=>'success']);
    }

    /**
     * Display the specified Statistic.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $statistic = $this->statisticRepository->find($id);

        if (empty($statistic)) {
            Flash::error('Statistic not found')->important();

            return redirect(route('statistics.index'));
        }

        return view('statistics.show')->with('statistic', $statistic);
    }

    /**
     * Show the form for editing the specified Statistic.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $statistic = $this->statisticRepository->find($id);
        $types = [];
        foreach(StatisticType::get() as $item) {
            $types[$item->id] = $item->name;
        }

        if (empty($statistic)) {
            Flash::error('Statistic not found')->important();

            return redirect(route('statistics.index'));
        }

        return view('statistics.edit', compact('statistic', 'types'));
    }

    /**
     * Update the specified Statistic in storage.
     *
     * @param int $id
     * @param UpdateStatisticRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStatisticRequest $request)
    {
        $validated = $request->validated();

        $statistic = $this->statisticRepository->find($id);

        if (empty($statistic)) {
            Flash::error('Statistic not found')->important();

            return redirect(route('statistics.index'));
        }

        $statistic = $this->statisticRepository->update($request->all(), $id);

        Flash::success('Statistic updated successfully.')->important();

        //return redirect(route('statistics.index'));

        return response()->json(['status'=>'success']);
    }

    /**
     * Remove the specified Statistic from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $statistic = $this->statisticRepository->find($id);

        if (empty($statistic)) {
            return $this->respondWithError('Statistic not found.');
        }

        $this->statisticRepository->delete($id);

        return $this->respond(['message' => '<strong>' . $statistic->name . '</strong> has been successfully deleted.']);
    }
}
