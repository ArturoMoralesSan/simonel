<?php

namespace app\Traits;

use App\Project;
use App\User;
use Carbon\Carbon;

trait FolioGenerateClass {

    private function folioDocument($call_id)
    {
        $numero      = Project::where('call_id', $call_id)->get()->count() + 1;
        $numsiguente = str_pad($numero, 4, "0", STR_PAD_LEFT);
        $year        = Carbon::now()->format('y');
        $folio       = $call_id.$year.$numsiguente;

        return $folio;
    }

    private function folioUser($id)
    {
        $numsiguente = str_pad($id, 4, "0", STR_PAD_LEFT);
        $year        = Carbon::now()->format('y');
        $folio       = $year.$numsiguente;

        return $folio;
    }
}