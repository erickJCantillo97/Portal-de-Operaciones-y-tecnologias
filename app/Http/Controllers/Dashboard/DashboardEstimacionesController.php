<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LdapRecord\Models\OpenLDAP\User as OpenLDAPUser;

class DashboardEstimacionesController extends Controller
{

    public function getQuotesStatus(Request $request)
    {
        // $status = $request->status == 'Entregada' || !$request->status ? 'Proceso' : $request->status;
        $status = $request->status;
        $quotes = QuoteVersion::has('quote')->with('quoteTypeShips', 'quote')->get()->filter(function ($quote) use ($status) {
            $hoy = Carbon::now();
            $ultimo_jueves = $hoy->previous(Carbon::THURSDAY);
            if (!Carbon::now()->isThursday()) {
                return Carbon::parse($quote['status_date']) >= $ultimo_jueves && $status == $quote['get_status'];
            } else {
                return Carbon::parse($quote['status_date']) >= $ultimo_jueves && $status == $quote['get_status'];
            }
        })->map(function ($quote) {
            return [
                'id' => $quote['id'],
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
        // $year = $request->year ?? Carbon::now()->year;
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
        // $year = $request->year ?? Carbon::now()->year;
        $quotes = QuoteVersion::get()->filter(function ($quote) {
            $hoy = Carbon::now();
            $ultimo_jueves = $hoy->previous(Carbon::THURSDAY);
            if (!Carbon::now()->isThursday()) {
                return Carbon::parse($quote['status_date']) >= $ultimo_jueves;
            } else {
                return Carbon::parse($quote['status_date']) >= $ultimo_jueves;
            }
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
        $year = request('year') ?? Carbon::now()->year;
        $promedioPorDificultad = QuoteVersion::whereYear('expeted_answer_date', $year)->join('quote_type_ships', 'quote_versions.id', '=', 'quote_type_ships.quote_version_id')
            ->select('quote_type_ships.maturity', DB::raw('AVG(DATEDIFF(day, quote_versions.expeted_answer_date, quote_versions.estimador_anaswer_date)) AS promedio'))
            ->groupBy('quote_type_ships.maturity')
            ->whereNotNull('quote_type_ships.maturity')
            ->whereNotNull('quote_versions.estimador_anaswer_date')
            ->get();

        return [
            'values' => $promedioPorDificultad->map(function ($value) {
                return intval($value['promedio']);
            }),
            'maturities' => $promedioPorDificultad->map(function ($value) {
                return $value['maturity'];
            }),
            'status' => true
        ];
    }
    public function getEstimatorData()
    {
        $year = $request->year ?? Carbon::now()->year;
        $people = QuoteVersion::whereNotNull('estimador_anaswer_date')->whereYear('estimador_anaswer_date', '!=', '1970')->select(DB::raw('AVG(DATEDIFF(day, expeted_answer_date, estimador_anaswer_date)) AS promedio'), 'estimador_name')
            ->groupBy('estimador_name')
            ->get()->map(function ($quote) {
                $user = User::where('userprincipalname', trim($quote['estimador_name']) . '@cotecmar.com')->first();
                return [
                    'average' => $quote['promedio'],
                    'quotes' => QuoteVersion::where('estimador_name', $quote['estimador_name'])->count(),
                    'name' => $quote['estimador_name'],
                    'photo' => $user !=  null ?  $user->photo() : 'https://ui-avatars.com/api/?name=' . $quote['estimador_name'],
                ];
            })->sortBy('average', SORT_REGULAR);

        return response()->json(
            [
                'people' => $people,
                'status' => true
            ],
            200
        );
    }

    public function getQuotesCountry(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;
        $countQuoteCountry = QuoteVersion::join('customers', 'quote_versions.customer_id', '=', 'customers.id')
            ->select('customers.country_en as country', DB::raw('COUNT(quote_versions.id) AS value'))
            ->groupBy('customers.country_en')
            ->orderBy('value', 'desc')
            ->get();

        return [
            'values' => $countQuoteCountry->map(function ($value) {
                return [$value['country'], $value['value']];
            }),

            'status' => true
        ];
    }

    public function getQuotesCustomers(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;
        // AÑadir el filtro por año actual en la base de datos de estimaciones para el dashboard
        $countQuoteCustomers = QuoteVersion::whereYear('quote_versions.expeted_answer_date', '2024')->join('customers', 'quote_versions.customer_id', '=', 'customers.id')
            ->select('customers.name as customer', DB::raw('COUNT(customers.id) AS value'))
            ->groupBy('customers.name')
            ->orderBy('value', 'desc')
            ->take(10)
            ->get();

        return [
            'values' => $countQuoteCustomers->map(function ($value) {
                return ['cliente' => $value['customer'], 'estimaciones' => $value['value']];
            }),

            'status' => true
        ];
    }

    public function getValueTotalCostoContratadas(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;
        $quotesYear = QuoteVersion::get()->where('status', 5)->filter(function ($q) use ($year) {
            return Carbon::parse($q['status_date'])->year == $year;
        });
        $cost = 0;
        foreach ($quotesYear as $quote) {
            $cost += $quote->total_cost[0];
        }
        return $cost;
    }
}
