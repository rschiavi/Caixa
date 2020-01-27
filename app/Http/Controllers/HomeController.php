<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Movement;
use App\Category;

use App\Services\Months;

use DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $selectedYear = $request->year ? $request->year : date('Y');
        $selectedMonth = $request->month ? $request->month  : date('n');

        $monthList = Months::get(true);
        $monthName = Months::find($selectedMonth);

        $categories = Category::all();

        /* movimentaçao mensal */
        $monthlyMovements = Movement::with('category')
        ->whereRaw('EXTRACT(year from date) = ' . $selectedYear)
        ->whereRaw('EXTRACT(month from date) = ' . $selectedMonth)
        ->orderBy('date')
        ->get();
        $monthlyIncomes = $monthlyMovements->where('type', '=', 'R')
        ->sum(function ($movement) {
            return $movement->value;
        });
        $monthlyExpenses = $monthlyMovements->where('type', '=', 'D')
        ->sum(function ($movement) {
            return $movement->value;
        });
        $monthlyTotal = $monthlyIncomes - $monthlyExpenses;

        /* movimentaçao anual */
        $annualIncomesByMonth = Movement::selectRaw('EXTRACT(month from date) as month, sum(value) as value')
        ->whereRaw('EXTRACT(year from date) = ' . $selectedYear)
        ->where('type', '=', 'R')
        ->groupBy(DB::raw('EXTRACT(month from date)'))
        ->orderBy(DB::raw('EXTRACT(month from date)'))
        ->get();

        $annualExpensesByMonth = Movement::selectRaw('EXTRACT(month from date) as month, sum(value) as value')
        ->whereRaw('EXTRACT(year from date) = ' . $selectedYear)
        ->where('type', '=', 'D')
        ->groupBy(DB::raw('EXTRACT(month from date)'))
        ->orderBy(DB::raw('EXTRACT(month from date)'))
        ->get();

        $annualIncomes = $annualIncomesByMonth->sum(function ($movement) {
            return $movement->value;
        });
        $annualExpenses = $annualExpensesByMonth->sum(function ($movement) {
            return $movement->value;
        });
        $annualTotal = $annualIncomes - $annualExpenses;

        $chart = [
            'labels' => collect(array_values($monthList)),
            'incomes' => collect(),
            'expenses' => collect()
        ];

        for ($i = 1; $i <= 12; $i++) {
            $info = $annualIncomesByMonth->firstWhere('month', $i);
            if ($info) {
                $chart['incomes']->push(\Money::real($info->value));
            } else {
                $chart['incomes']->push(0);
            }

            $info = $annualExpensesByMonth->firstWhere('month', $i);
            if ($info) {
                $chart['expenses']->push(\Money::real($info->value));
            } else {
                $chart['expenses']->push(0);
            }
        }

        return view('home')->with(compact([
            'selectedYear', 'selectedMonth', 'monthList', 'monthName', 'categories',
            'monthlyMovements', 'monthlyIncomes', 'monthlyExpenses', 'monthlyTotal',
            'annualIncomes', 'annualExpenses', 'annualTotal', 'chart'
        ]));
    }
}
