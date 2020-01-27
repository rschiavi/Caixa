<?php

namespace App\Http\Controllers;

use App\Movement;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Money;

class MovementController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'required|exists:categories,id',
            'type' => 'required|in:R,D',
            'description' => 'required|min:4|max:1000',
            'date' => 'required|date_format:d/m/Y',
            'value' => 'required|money'
        ]);
        $movement = new Movement();
        $movement->category_id = $request->category;
        $movement->type = $request->type;
        $movement->description = $request->description;
        $movement->date = Carbon::createFromFormat('d/m/Y', $request->date);
        $movement->value = Money::cents($request->value);
        $movement->save();
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movement $movement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movement $movement)
    {
        //
    }
}
