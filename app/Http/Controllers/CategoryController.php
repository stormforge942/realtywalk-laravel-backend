<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Category;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Response;

class CategoryController extends AppBaseController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * List Category as json resource for datatable
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable()
    {
        $data = Category::orderBy('name', 'asc')->get();

        return $this->respond($data);
    }

    /**
     * List Category as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getList()
    {
        return Category::select(['*', 'name as label'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Display a listing of the Category.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->all();

        return view('categories.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new Category.
     *
     * @return Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param CreateCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $input = $request->all();
        $this->categoryRepository->create($input);
        Flash::success('Category saved successfully.')->important();

        return redirect(route('categories.index'));
    }

    /**
     * Display the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            Flash::error('Category not found')->important();

            return redirect(route('categories.index'));
        }

        return view('categories.show')->with('category', $category);
    }

    /**
     * Show the form for editing the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            Flash::error('Category not found')->important();

            return redirect(route('categories.index'));
        }

        return view('categories.edit')->with('category', $category);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            Flash::error('Category not found')->important();

            return redirect(route('categories.index'));
        }

        $this->categoryRepository->update($request->all(), $id);
        Flash::success('Category updated successfully.')->important();

        return redirect(route('categories.index'));
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            return $this->respondWithError('Category not found.');
        }

        if ($category->properties->count()) {
            return $this->respondWithError('Category can\'t be removed because it has related properties. Delete all related properties first.');
        }

        $this->categoryRepository->delete($id);

        return $this->respond(['message' => '<strong>' . $category->name . '</strong> has been successfully deleted.']);
    }
}
