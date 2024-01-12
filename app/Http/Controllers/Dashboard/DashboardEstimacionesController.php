<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteVersion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardEstimacionesController extends Controller
{

    public function getQuotesStatus(Request $request)
    {
        $quotes = QuoteVersion::with('quoteTypeShips', 'quote')->get()->filter(function ($quote) use ($request) {
            return $quote->status_date >= Carbon::now()->subDays(6)->format('Y-m-d') && $quote->status_date <= Carbon::now()->format('Y-m-d') && $quote->get_status == $request->status && $quote->id != 23;
        })->map(function ($quote) {
            return [
                'name' => $quote['quote']['name'],
                'get_status' => $quote['get_status'],
                'estimador' => $quote['estimador_name'],
                'consecutive' => $quote['consecutive'],
                'total_cost' => collect($quote['quoteTypeShips'])->sum('price_before_iva_original'),
            ];
        });

        // return   QuoteVersion::with('quoteTypeShips', 'quote')->whereIn('id', [
        //     22, 24, 25, 26, 34
        // ])->get();
        return  $quotes;
    }
}
