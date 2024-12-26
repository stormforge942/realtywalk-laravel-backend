<?php

namespace App\Http\Controllers;

use App\Models\Builder;
use App\Models\Polygon;
use App\Models\Property;
use Illuminate\Http\Request;

class ProcessController extends AppBaseController
{
    public function checkUploadStatus(Request $request)
    {
        $model = $request->query('model');
        $id = $request->query('id');

        if (!in_array($model, ['polygon', 'property', 'builder'])) {
            return $this->respondWithError('Incorrect model name');
        }

        if ($model == 'polygon') {
            $record = Polygon::findOrFail($id);
        } else if ($model == 'property') {
            $record = Property::findOrFail($id);
        } else if ($model == 'builder') {
            $record = Builder::findOrFail($id);
        }

        if (!$record->is_uploading_files) {
            $session_key = "uploaded_files_{$model}_{$id}";
            session()->put($session_key, true);
        }

        return $this->respond([
            'status' => $record->is_uploading_files ? 'waiting' : 'done'
        ]);
    }
}
