<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStyleRequest;
use App\Http\Requests\UpdateStyleRequest;
use App\Repositories\StyleRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Style;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Response;


class StyleController extends AppBaseController
{
    protected $styleRepository;

    public function __construct(StyleRepository $styleRepository)
    {
        $this->styleRepository = $styleRepository;
    }

    /**
     * List Style as json resource for datatable
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        $data = Style::orderBy('name', 'asc')->get();

        return $data;
    }

    /**
     * List Style as json resource for treeselect
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getList()
    {
        return Style::select(['*', 'name as label'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Display a listing of the Style.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $styles = $this->styleRepository->all();

        return view('styles.index')
            ->with('styles', $styles);
    }

    /**
     * Show the form for creating a new Style.
     *
     * @return Response
     */
    public function create()
    {
        return view('styles.create');
    }

    /**
     * Store a newly created Style in storage.
     *
     * @param CreateStyleRequest $request
     *
     * @return Response
     */
    public function store(CreateStyleRequest $request)
    {

        $validated = $request->validated();

        $input = $request->all();

        $style = $this->styleRepository->create($input);

        Flash::success('Style saved successfully.')->important();

        //return redirect(route('styles.index'));

        return response()->json(['status'=>'success']);
    }

    /**
     * Display the specified Style.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $style = $this->styleRepository->find($id);

        if (empty($style)) {
            Flash::error('Style not found')->important();

            return redirect(route('styles.index'));
        }

        return view('styles.show')->with('style', $style);
    }

    /**
     * Show the form for editing the specified Style.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $style = $this->styleRepository->find($id);

        if (empty($style)) {
            Flash::error('Style not found')->important();

            return redirect(route('styles.index'));
        }

        return view('styles.edit')->with('style', $style);
    }

    /**
     * Update the specified Style in storage.
     *
     * @param int $id
     * @param UpdateStyleRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStyleRequest $request)
    {

        $validated = $request->validated();

        $style = $this->styleRepository->find($id);

        if (empty($style)) {
            Flash::error('Style not found')->important();

            return redirect(route('styles.index'));
        }

        $style = $this->styleRepository->update($request->all(), $id);

        Flash::success('Style updated successfully.')->important();

        //return redirect(route('styles.index'));
        return response()->json(['status'=>'success']);
    }

    /**
     * Remove the specified Style from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $style = $this->styleRepository->find($id);

        if (empty($style)) {
            return $this->respondWithError('Style not found.');
        }

        $this->styleRepository->delete($id);

        return $this->respond(['message' => '<strong>' . $style->name . '</strong> has been successfully deleted.']);
    }
}
