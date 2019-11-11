<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Movement;
use App\Category;

use App\Services\Months;
use App\Charts\AnualMovement;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $selected_year = $request->year ? $request->year : date('Y');
        $selected_month = $request->month ? $request->month  : date('n');

        $categories = Category::all();
        $movements = Movement::with('category')->orderBy('date')->get();
        $receitas = $movements->where('type', '=', 'R')->sum(function ($movement) {
            return $movement->value;
        });
        $despesas = $movements->where('type', '=', 'D')->sum(function ($movement) {
            return $movement->value;
        });
        $total = $receitas - $despesas;
        $months = Months::get(true);
        $month_name = Months::find($selected_month);

        // dd($request->year);
        $chart = new AnualMovement();
        $chart->labels(['One', 'Two', 'Three', 'Four']);
        $chart->dataset('My dataset', 'line', [1, 2, 3, 4]);
        $chart->dataset('My dataset 2', 'line', [4, 3, 2, 1]);

        return view('home')->with(compact([
            'movements', 'receitas', 'despesas', 'total', 'months', 'chart',
            'selected_month', 'selected_year', 'month_name', 'categories'
        ]));
    }
}
