<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;

class KinerjaPemeliharaanController extends Controller
{
    public function index()
    {
        $unitMap = [
            'mysql_poasia' => 'ULPLTD POASIA',
            'mysql_bau_bau' => 'ULPLTD BAU BAU',
            'mysql_wua_wua' => 'ULPLTD WUA WUA',
            'mysql_kolaka' => 'ULPLTD KOLAKA',
        ];

        $pmClosed = WorkOrder::where('type', 'PM')->where('status', 'Closed')->get();
        $cmClosed = WorkOrder::where('type', 'CM')->where('status', 'Closed')->get();

        $pmByUnit = [];
        $cmByUnit = [];

        $pmPerUnit = [];
        $cmPerUnit = [];

        foreach ($unitMap as $key => $unitName) {
            $pm = $pmClosed->where('unit_source', $key);
            $cm = $cmClosed->where('unit_source', $key);

            $pmByUnit[$unitName] = $pm;
            $cmByUnit[$unitName] = $cm;

            $pmPerUnit[] = $pm->count();
            $cmPerUnit[] = $cm->count();
        }

        return view('kinerja.index', [
            'pmByUnit' => $pmByUnit,
            'cmByUnit' => $cmByUnit,
            'unitNames' => array_values($unitMap),
            'pmPerUnit' => $pmPerUnit,
            'cmPerUnit' => $cmPerUnit,
            'pmCount' => $pmClosed->count(),
            'cmCount' => $cmClosed->count(),
        ]);
    }
}
