<?php

namespace App\Http\Controllers;

use App\Models\CriteriaWeight;
use App\Models\Alternative;
use Illuminate\Http\Request;

class EstimationMatrixController extends Controller
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

        // Inisialisasi matriks estimasi (W')
        $estimationMatrix = [];

        // Pengalian elemen pada setiap kolom/kriteria
        foreach ($criteriaweights as $icw => $cw) {
            // Inisialisasi kolom matriks estimasi
            $estimationColumn = 1;

            // dd($weightedMatrix);

            foreach ($alternatives as $a) {
                $i = 0;
                // Mengambil nilai matriks tertimbang untuk setiap alternatif dan kriteria
                $weightedElement = $weightedMatrix[$i][$icw];

                // Mengalikan nilai matriks tertimbang pada setiap alternatif dalam satu kriteria
                $estimationColumn *= $weightedElement;

                $i++;
            }

            // Menghitung pangkat dari hasil pengalian kolom
            $numAlternatives = count($alternatives);
            $estimationColumn = pow($estimationColumn, 1 / $numAlternatives);

            // Menambahkan nilai hasil pengalian kolom ke dalam matriks estimasi
            $estimationMatrix[] = $estimationColumn;
        }

        return view('estimation_matrix', compact('estimationMatrix', 'criteriaweights'))->with('i', 0);
    }
}
