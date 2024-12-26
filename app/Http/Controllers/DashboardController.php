<?php

namespace App\Http\Controllers;

use App\Models\Builder;
use App\Models\Category;
use App\Models\Polygon;
use App\Models\Property;
use App\Models\Style;
use App\Models\Zone;
use App\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total = $this->getTotalCount();
        $users = User::role('user')->latest()->limit(6)->get();
        return view('dashboard', compact('total', 'users'));
    }

    private function getTotalCount()
    {
        return [
            'properties' => Property::count(),
            'zones'      => Zone::count(),
            'builders'   => Builder::count(),
            'polygons'   => Polygon::count(),
            'users'      => [
                'total'   => User::count(),
                'agent'   => User::role('admin')->count(),
                'regular' => User::role('user')->count()
            ]
        ];
    }

    /**
     * Get properties count each category and its label
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getPropertiesPerCategory()
    {
        $categories = Category::withCount('properties')->get();
        $others     = Property::whereNull('category_id')->count();
        $data   = array_merge($categories->pluck('properties_count')->all(), [$others]);
        $labels = array_merge($categories->pluck('name')->all(), ['Others']);

        return $this->respond([
            'labels' => $labels,
            'data'   => $data
        ]);
    }

    /**
     * Get properties count each style and its label
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getPropertiesPerStyle()
    {
        $styles = Style::withCount('properties')->get();
        $data   = $styles->pluck('properties_count')->all();
        $labels = $styles->pluck('name')->all();

        return $this->respond([
            'labels' => $labels,
            'data'   => $data
        ]);
    }
}
