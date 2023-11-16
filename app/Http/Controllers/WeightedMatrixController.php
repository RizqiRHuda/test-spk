<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;

class WeightedMatrixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mengambil semua skor alternatif beserta informasi terkait
        $scores = AlternativeScore::select(
            'alternativescores.id as id',
            'alternatives.id as ida',
            'criteriaweights.id as idw',
            'criteriaratings.id as idr',
            'alternatives.name as name',
            'criteriaweights.name as criteria',
            'criteriaratings.rating as rating',
            'criteriaratings.description as description'
        )
            ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
            ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
            ->leftJoin('criteriaratings', 'criteriaratings.id', '=', 'alternativescores.rating_id')
            ->get();

        // Mengambil semua skor alternatif (versi kedua)    
        $cscores = AlternativeScore::select(
            'alternativescores.id as id',
            'alternatives.id as ida',
            'criteriaweights.id as idw',
            'criteriaratings.id as idr',
            'alternatives.name as name',
            'criteriaweights.name as criteria',
            'criteriaratings.rating as rating',
            'criteriaratings.description as description'
        )
            ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
            ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
            ->leftJoin('criteriaratings', 'criteriaratings.id', '=', 'alternativescores.rating_id')
            ->get();

        // Mengambil semua alternatif
        $alternatives = Alternative::get();

        // Mengambil semua bobot kriteria
        $criteriaweights = CriteriaWeight::get();

        // Inisialisasi matriks tertimbang (V)
        $weightedMatrix = [];

        // Normalisasi
        foreach ($alternatives as $a) {
            // Mengambil semua skor untuk setiap id alternatif
            $afilter = $scores->where('ida', $a->id)->values()->all();
            // Inisialisasi baris matriks tertimbang
            $weightedRow = [];
            // Looping setiap kriteria
            foreach ($criteriaweights as $icw => $cw) {
                // Mengambil nilai normalisasi untuk setiap kriteria
                $normalization = $afilter[$icw]->rating;

                // Menghitung elemen matriks tertimbang (V) berdasarkan rumus
                $wij = $cw->weight; // Bobot kriteria
                $tij = $normalization; // Nilai normalisasi

                $weightedElement = ($wij * $tij) + $wij;
                $result = number_format($weightedElement, 2, '.', '');

                // Menambahkan elemen matriks tertimbang ke dalam baris matriks tertimbang
                $weightedRow[] = $result;
            }

            // Menambahkan baris matriks tertimbang ke dalam matriks tertimbang
            $weightedMatrix[] = $weightedRow;
            session(['weightedMatrix' => $weightedMatrix]);

        }

        return view('weighted_matrix', compact('weightedMatrix', 'alternatives', 'criteriaweights'))->with('i', 0);
    }
}
