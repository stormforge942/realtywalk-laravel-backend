<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreatePropertyStatusRequest;
use App\Http\Requests\UpdatePropertyStatusRequest;
use App\Models\PropertyStatus;
use Laracasts\Flash\Flash;

class PropertyStatusController extends AppBaseController
{
    /**
     * List property status as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        $data = PropertyStatus::orderBy('name', 'asc')->get();

        $data = $data->map(function ($item) {
            $item->show_view_btn = false;
            return $item;
        });

        return $this->respond($data);
    }

    /**
     * List status as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getList()
    {
        return PropertyStatus::orderBy('name')->get();
    }

    /**
     * Add new properties status
     */
    public function create()
    {
        return view('propertiesstatus.create');
    }

    /**
     * @param PropertyStatus $propertiesstatus
     * @return view
     */
    public function edit(PropertyStatus $propertiesstatus)
    {
        return view('propertiesstatus.edit', compact('propertiesstatus'));
    }

    /**
    * Display a listing of the properties status.
    *
    * @return view
    */
    public function index()
    {
        $propertiesstatus = PropertyStatus::orderBy('name', 'asc')->get();

        return view('propertiesstatus.index')
            ->with('propertiesstatus', $propertiesstatus);
    }

    /**
     * create new instance of Properties status
     * @param CreatePropertyStatusRequest $request
     * @return Response
     */
    public function store(CreatePropertyStatusRequest $request)
    {
        try {
            $validated = $request->validated();
            PropertyStatus::newFromRequest($validated);
            Flash::success('New property status saved')->important();
            return redirect()->route('status.index');
        } catch (\Exception $e) {
            Flash::error('Sorry the server could not handle the process, try again later')->important();
            return back()->withInput();
        }
    }

    /**
     * update instance of Properties status
     * @param UpdatePropertyStatusRequest $request
     * @param PropertyStatus $propertiesstatus
     * @return Response
     */
    public function update(PropertyStatus $propertiesstatus, UpdatePropertyStatusRequest $request)
    {
        try {
            $validated = $request->validated();
            $propertiesstatus->updateFromRequest($validated);
            Flash::success('Property status updated')->important();
            return redirect()->route('status.index');
        } catch (\Exception $e) {
            Flash::error('Sorry the server could not handle the update process, try again later')->important();
            return back();
        }
    }

    /**
     * delete an instance of Properties status
     * @param PropertyStatus $propertiesstatus
     * @return Response
     */
    public function destroy(PropertyStatus $propertiesstatus)
    {
        try {
            $propertiesstatus->delete();
            return $this->sendSuccess('properties status deleted');
        } catch (\Exception $e) {
            return $this->sendError('properties status not deleted, please try again', 417);
        }
    }
}
