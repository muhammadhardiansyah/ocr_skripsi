<?php

namespace App\Http\Controllers;

use App\Models\Recognition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecognitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('result.index', [
            'active' => 'dash_result',
            'recognitions' => Recognition::latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('index', [
            'active' => 'dash'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->file('images'));
        foreach ($request->file('images') as $image) {
            $name = $image->getClientOriginalName();
            $validatedData['name'] = $name;
            if ($image) {
                $validatedData['image'] = $image->storeAs('ocr-images', $name);
            }
            Recognition::create($validatedData);
        }

        return redirect('/result');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recognition $recognition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recognition $recognition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recognition $recognition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $recognition = Recognition::find($id);
        if ($recognition->image) {
            Storage::delete($recognition->image);
        }
        Recognition::destroy($recognition->id);

        return redirect('/result');
    }
}
