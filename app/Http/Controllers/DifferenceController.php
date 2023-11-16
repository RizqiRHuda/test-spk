<?php

namespace App\Http\Controllers;

use App\Models\CriteriaWeight;
use App\Models\Alternative;
use Illuminate\Http\Request;

class DifferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alternatives = Alternative::get();

        // Mengambil semua bobot kriteria
        $criteriaweights = CriteriaWeight::get();

        $weightedMatrix = session('weightedMatrix');
        $estimationMatrix = session('estimationMatrix');

        // Inisialisasi matriks selisih (C)
        $differenceMatrix = [];

        // Menghitung selisih setiap elemen pada setiap kolom/kriteria
        foreach ($criteriaweights as $icw => $cw) {
            // Inisialisasi kolom matriks selisih
            $differenceColumn = [];

            // Looping setiap alternatif
            foreach ($alternatives as $a) {
                // Mengambil nilai matriks tertimbang untuk setiap alternatif dan kriteria
                $weightedElement = $weightedMatrix[$a->id - 1][$icw]; // Note: $a->id - 1 to adjust for array indexing

                // Mengambil nilai estimasi untuk setiap kriteria
                // $estimationElement = $estimationMatrix[$icw];
                $estimationElement = isset($estimationMatrix[$icw]) ? $estimationMatrix[$icw] : null;

                // Menghitung selisih nilai
                $differenceValue = $weightedElement - $estimationElement;

                // Menambahkan nilai selisih ke dalam kolom matriks selisih
                $differenceColumn[] = $differenceValue;
            }

            // Menambahkan kolom matriks selisih ke dalam matriks selisih
            $differenceMatrix[] = $differenceColumn;
        }

        return view('difference_matrix', compact('differenceMatrix', 'criteriaweights', 'alternatives'))->with('i', 0);
    }
}
