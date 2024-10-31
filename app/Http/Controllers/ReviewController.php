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
        return view('reviews.create', compact('restaurant'));
    }
    
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = $review->user_id = Auth::id();;
        $review->save();

        return redirect()->route('restaurants.reviews.index',$restaurant)
                            ->with('flash_message', 'レビューを投稿しました。');
    }

    public function edit(Restaurant $restaurant, Review $review)
    {
        if($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('error_message', '不正なアクセスです。');
        }else{
            return view('reviews.edit', compact('restaurant','review'));
        }        
    }
    
    public function update(Request $request,Restaurant $restaurant,Review $review)
    {
        if($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('error_message','不正なアクセスです。');
        } else {
            $request->validate([
                'score' => ['required','numeric','digits_between:1,5'],
                'content' => ['required']
            ]);
    
            $review->score = $request->input('score');
            $review->content = $request->input('content');
            $review->restaurant_id = $restaurant->id;
            $review->user_id = Auth::user()->id;
            $review->save();

            return redirect()->route('restaurants.reviews.index',$restaurant)->with('flash_message','レビューを編集しました。');
        }
    }
    
    public function destroy(Restaurant $restaurant,Review $review)
    {
        if($review->user_id !== Auth::id()) {
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('error_message','不正なアクセスです。');
        }else{
            $review->delete();
            return redirect()->route('restaurants.reviews.index',$restaurant)->with('flash_message','レビューを削除しました。');    
        }
    }
}
