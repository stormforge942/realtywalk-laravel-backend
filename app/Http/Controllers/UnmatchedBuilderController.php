<?php

namespace App\Http\Controllers;

use App\Models\Builder;
use App\Models\Property;
use App\Models\UnmatchedBuilder;
use Illuminate\Http\Request;

class UnmatchedBuilderController extends AppBaseController
{
    public function index()
    {
        return redirect()->route('builders.index');
    }

    public function edit(UnmatchedBuilder $unmatchedBuilder)
    {
        $similars = Builder::query()
            ->where('id', '!=', $unmatchedBuilder->builder_id)
            ->whereRaw('SOUNDEX(name) like ?', [$unmatchedBuilder->name]);

        if ($unmatchedBuilder->builder) {
            $similars->whereRaw('SOUNDEX(name) like ?', [$unmatchedBuilder->builder->name]);
        }

        return view('builders.unmatched_builders.edit', [
            'unmatchedBuilder' => $unmatchedBuilder,
            'similars' => $similars->get()
        ]);
    }

    public function update(Request $request, UnmatchedBuilder $unmatchedBuilder)
    {
        $request->validate(['builder_id' => 'nullable|exists:builders,id']);
        $builder_id = $request->input('builder_id');
        $unmatchedBuilder->update(['builder_id' => $builder_id]);

        Property::where('builder_name', $unmatchedBuilder->name)->update([
            'builder_id' => $builder_id ?: null
        ]);

        return $this->respond([
            'status' => 'success',
        ]);
    }
}
