<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::where('user_id', Auth::id())
        ->orderBy('reserved_datetime','desc')->paginate(15);

        return view('reservations.index',compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        return view('reservations.create',compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,Restaurant $restaurant)
    {
        $request->validate([
            'reservation_date' => 'required|date_format:"Y-m-d"',
            'reservation_time' => 'required|date_format:"H:i"',
            'number_of_people' => 'required|digits_between:1,50'
        ]);

        $reservations = new Reservation();
        $reservations->reserved_datetime = $request->input('reservation_date'). ' ' .$request->input('reservation_time');
        $reservations->number_of_people = $request->input('number_of_people');
        $reservations->restaurant_id = $restaurant->id;
        $reservations->user_id = Auth::user()->id;
        $reservations->save();

        return redirect()->route('reservations.index',$restaurant)->with('flash_message','予約が完了しました。');
    }

    /**

     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant,Reservation $reservation)
    {
        if($reservation->user_id !== Auth::id()) {
            return redirect()->route('reservations.index',$restaurant)->with('error_message','不正なアクセスです。');
        } else {
            $reservation->delete();
            return redirect()->route('reservations.index',$restaurant)->with('flash_message','予約をキャンセルしました。');
        }
    }
}
