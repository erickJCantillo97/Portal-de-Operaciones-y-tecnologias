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
        $status = $request->status == 'Entregada' || !$request->status ? 'Proceso' : $request->status;
        $quotes = QuoteVersion::has('quote')->with('quoteTypeShips', 'quote')->get()->filter(function ($quote) use ($status) {
            $hoy = Carbon::now();
            $ultimo_jueves = $hoy->previous(Carbon::THURSDAY);
            if (!Carbon::now()->isThursday()) {
                return Carbon::parse($quote['status_date']) >= $ultimo_jueves && $status == $quote['get_status'];
            } else {
                return Carbon::parse($quote['status_date']) >= $ultimo_jueves;
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
        $quotes = QuoteVersion::get()->groupBy('status')->map(function ($status) {
            return [
                'value' => count($status),
                'status' => $status[0]['get_status']
            ];
        });
        return $quotes;
    }

    public function getAvgManurities()
    {
        $promedioPorDificultad = QuoteVersion::whereDate('expeted_answer_date', '<>', '1970-01-01')->join('quote_type_ships', 'quote_versions.id', '=', 'quote_type_ships.quote_version_id')
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

    public function getQuotesCountry()
    {
        $countQuoteCountry = QuoteVersion::join('customers', 'quote_versions.customer_id', '=', 'customers.id')
            ->select('customers.country_en as country', DB::raw('COUNT(quote_versions.id) AS value'))
            ->groupBy('customers.country_en')
            ->get();

        return [
            'values' => $countQuoteCountry->map(function ($value) {
                return [$value['country'], $value['value']];
            }),

            'status' => true
        ];
    }
}
