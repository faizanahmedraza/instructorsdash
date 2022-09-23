<?php

namespace Modules\Reports\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\Guest;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $coursesRegisterAndSales = Event::withCount('guests as registered')->withSum('guests as sales', 'ticket_price')->where('user_id', auth()->id())->where('created_at', '>=', Carbon::today()->subDays(30))->latest()->take(30)->get();
        $last30DaysProfit = Guest::where('user_id', auth()->id())->where('created_at', '>=', Carbon::today()->subDays(30))->sum('ticket_price');
        $coursesSalesMonthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(date('Y'), $month);

            $date_end = $date->copy()->endOfMonth();

            $guestPrice = Guest::where('user_id', auth()->id())->where('created_at', '>=', $date)
                ->where('created_at', '<=', $date_end)->sum('ticket_price');

            $coursesSalesMonthly[] = [
                'month' => Carbon::create()->month($month)->format('F'),
                'sale' => $guestPrice
            ];
        }
        return view('reports::index', compact('coursesRegisterAndSales', 'coursesSalesMonthly', 'last30DaysProfit'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('reports::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('reports::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('reports::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
