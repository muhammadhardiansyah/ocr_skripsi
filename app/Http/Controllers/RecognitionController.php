<?php

namespace App\Http\Controllers;

use App\Models\Recognition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use GPBMetadata\Google\Cloud\Vision\V1\ImageAnnotator;

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
        // dd((new TesseractOCR())->version());
        // dd($request->file('images'));
        foreach ($request->file('images') as $image) {
            $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $validatedData['name'] = $name;
            if ($image) {
                $validatedData['image'] = $image->store('ocr-images');
            }

            //google vision ocr
            $vision = $this->visionProcess($validatedData['image']);
            $validatedData['vision_text'] = $vision['text'];
            $validatedData['vision_time'] = $vision['time'];

            //tesseract ocr
            $tesseract = $this->tesseractProcess($validatedData['image']);
            $validatedData['tesseract_text'] = $tesseract['text'];
            $validatedData['tesseract_time'] = $tesseract['time'];

            //compare test
            $notepadPath = storage_path('/app/annotations/' . $name . '.txt');
            if (file_exists($notepadPath)) {
                $notepadContent = file_get_contents($notepadPath);
                $compare = $this->compareText($validatedData['tesseract_text'], $validatedData['vision_text'], $notepadContent);
                $validatedData['vision_percentage'] = $compare['vision'];
                $validatedData['tesseract_percentage'] = $compare['tesseract'];
            }

            Recognition::create($validatedData);
        }
        session()->flash('success', 'The text has been successfully recognized!');
        return redirect('/result');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recognition = Recognition::find($id);
        return view('result.show', [
            'active' => 'dash_result',
            'recognition' => $recognition,
        ]);
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

    //Tesseract Recognition Process
    public function tesseractProcess($path)
    {
        $imagePath  = public_path('storage/' . $path);
        $start      = microtime(true);
        $text       = (new TesseractOCR($imagePath))->run();
        $end        = microtime(true);
        $time       = $end - $start;
        $time       = round($time, 2);

        return [
            'text' => $text,
            'time' => $time
        ];
    }

    //Google Vision Recognition Process
    public function visionProcess($path)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('/app/token.json'));
        $imagePath  = public_path('storage/' . $path);
        $imageContent = file_get_contents($imagePath);
        $start      = microtime(true);
        $text       = (new ImageAnnotatorClient())->textDetection($imageContent);
        $end        = microtime(true);
        $time       = $end - $start;
        $time       = round($time, 2);

        return [
            'text' => ($text->getTextAnnotations())[0]->getDescription(),
            'time' => $time
        ];
    }

    //Compare text
    private function compareText($tesseractText, $visionText ,$notepadContent)
    {
        //normalize
        $tesseractText = str_replace(["\r\n", "\r", "\n"], ' ', $tesseractText);
        $tesseractText = preg_replace('/\s+/', ' ', $tesseractText);
        $visionText = str_replace(["\r\n", "\r", "\n"], ' ', $visionText);
        $visionText = preg_replace('/\s+/', ' ', $visionText);
        $notepadContent = str_replace(["\r\n", "\r", "\n"], ' ', $notepadContent);
        $notepadContent = preg_replace('/\s+/', ' ', $notepadContent);
        //comparison with Sequence Matcher
        similar_text($tesseractText, $notepadContent, $tesseract);
        similar_text($visionText, $notepadContent, $vision);

        return [
            'tesseract' => round($tesseract, 2),
            'vision' => round($vision, 2),
        ];
    }
}
