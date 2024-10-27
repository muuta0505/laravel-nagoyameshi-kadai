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
   
    public function create(Restaurant $restaurant)
    {
        return view('restaurant.create', compact('restaurant'));
    }
    
    public function store(Request $request, Review $reviews, Restaurant $restaurant)
    {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = ($restaurant->id);
        $review->user_id = ($user->id !== Auth::id());
        $review->save();

        return redirect()->route('review.index')
                            ->with('flash_message', 'レビューを投稿しました。');
    }

    public function edit(Restaurant $restaurant, Review $reviews)
    {
        return redirect()->route('review.index')
                            ->with('error_message', '不正なアクセスです。');

        return view('reviews.edit', compact('restaurant','reviews'));
    }
    public function update(Request $request, Review $reviews)
    {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->save();

        return redirect()->route('review.index')
                            ->with('flash_message', 'レビューを編集しました。');
    }
    public function destroy(){}
}
