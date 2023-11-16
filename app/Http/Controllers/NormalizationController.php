<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;

class NormalizationController extends Controller
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

        // Normalisasi
        // Normalisasi
        dd([
            // 'Alternative' => collect($alternatives)->pluck('name', 'name')->toArray(),
            // 'cscores' => collect($cscores)->map(function ($cscore) {
            //     return [
            //         'id' => $cscore['id'],
            //         'ida' => $cscore['ida'],
            //         'idw' => $cscore['idw'],
            //         'idr' => $cscore['idr'],
            //         'name' => $cscore['name'],
            //         'criteria' => $cscore['criteria'],
            //         'rating' => $cscore['rating'],
            //         'description' => $cscore['description'],
            //     ];
            // })->toArray(),
        ]);



        foreach ($alternatives as $a) {
            // Mengambil semua skor untuk setiap id alternatif
            $afilter = $scores->where('ida', $a->id)->values()->all();
            // Looping setiap kriteria
            foreach ($criteriaweights as $icw => $cw) {
                // Mengambil semua nilai rating untuk setiap kriteria
                $rates = $cscores->map(function ($val) use ($cw) {
                    if ($cw->id == $val->idw) {
                        return $val->rating;
                    }
                })->toArray();

                // Menghapus nilai null yang dihasilkan oleh map,
                // Mengindeks ulang array
                $rates = array_values(array_filter($rates));

                // Menghitung total kuadrat
                $total = 0;
                foreach ($rates as $value) {
                    $total += pow($value, 2);
                }
                $sqrt = sqrt($total);

                // Memperoleh nilai x_i^+ dan x_i^-
                $xi_plus = max($rates);
                $xi_minus = min($rates);

                // Menghitung normalisasi berdasarkan formula MABAC
                $normalisasi = ($afilter[$icw]->rating - $xi_minus) / ($xi_plus - $xi_minus);
                $result = number_format($normalisasi, 2, '.', '');
                $afilter[$icw]->rating = $result;
            }
        }

        return view('normalization', compact('scores', 'alternatives', 'criteriaweights'))->with('i', 0);
    }
}
