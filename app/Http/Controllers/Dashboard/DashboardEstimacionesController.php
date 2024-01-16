<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardEstimacionesController extends Controller
{

    public function getQuotesStatus(Request $request)
    {
        $quotes = QuoteVersion::has('quote')->with('quoteTypeShips', 'quote')->get()->filter(function ($quote) use ($request) {
            $quote->get_status = $quote->get_status == 'Entregada' ? 'Proceso' : $quote->get_status;
            return Carbon::parse($quote->status_date)->format('Y-m-d') >= Carbon::now()->subDays(6)->format('Y-m-d') && Carbon::parse($quote->status_date)->format('Y-m-d') <= Carbon::now()->format('Y-m-d')  && $quote->get_status  == $request->status;
        })->map(function ($quote) {
            return [
                'name' => $quote['quote']['name'],
                'get_status' => $quote['get_status'],
                'estimador' => $quote['estimador_name'],
                'consecutive' => $quote['consecutive'],
                'total_cost' => collect($quote['quoteTypeShips'])->sum('price_before_iva_original'),
            ];
        });

        return response()->json([
            'quotes' => $quotes,
            'status' => true
        ], 200);
    }

    public function getQuotesManurity()
    {
        $maturities =  QuoteTypeShip::whereNotNull('maturity')->select(DB::raw('count(id) as value'), DB::raw('UPPER(maturity) as name'))
            ->groupBy('maturity')
            ->orderBy('maturity')
            ->get();

        return response()->json([
            'maturities' => $maturities,
            'status' => true
        ], 200);
    }
    public function getStatusWeek()
    {
        $quotes = QuoteVersion::get()->filter(function ($quote) {
            return Carbon::parse($quote->status_date)->format('Y-m-d') >= Carbon::now()->subDays(6)->format('Y-m-d') && Carbon::parse($quote->status_date)->format('Y-m-d') <= Carbon::now()->format('Y-m-d');
        })->groupBy('status')->map(function ($status) {
            return [
                'value' => count($status),
                'status' => $status[0]['get_status']
            ];
        });
        return $quotes;
    }

    public function getAvgManurities()
    {
        $promedioPorDificultad = QuoteVersion::join('quote_type_ships', 'quote_versions.id', '=', 'quote_type_ships.quote_version_id')
            ->select('quote_type_ships.maturity', DB::raw('AVG(DATEDIFF(day, quote_versions.created_at, quote_versions.estimador_anaswer_date)) AS promedio'))
            ->groupBy('quote_type_ships.maturity')
            ->get();

        return [
            'values' => $promedioPorDificultad->map(function ($value) {
                return $value['promedio'];
            }),
            'maturities' => $promedioPorDificultad->map(function ($value) {
                return $value['maturity'];
            }),
            'status' => true
        ];
    }
}
