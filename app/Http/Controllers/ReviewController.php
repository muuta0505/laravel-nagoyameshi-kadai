<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        if (Auth::user()->subscribed('premium_plan')) {
            $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->paginate(5);
        } else {
            $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->paginate(5)->take(3);
        }
        return view('reviews.index',compact('restaurant','reviews'));
    }
    public function create(){}
    public function store(){}
    public function edit(){}
    public function update(){}
    public function destroy(){}
}
