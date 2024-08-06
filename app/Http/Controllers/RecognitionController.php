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
            $validatedData['vision_memory'] = $vision['memory'];

            //tesseract ocr
            $tesseract = $this->tesseractProcess($validatedData['image']);
            $validatedData['tesseract_text'] = $tesseract['text'];
            $validatedData['tesseract_time'] = $tesseract['time'];
            $validatedData['tesseract_memory'] = $tesseract['memory'];

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
        set_time_limit(0);
        $imagePath  = public_path('storage/' . $path);
        $startTime1      = microtime(true);
        $startMemory1    = memory_get_usage();
        $text       = (new TesseractOCR($imagePath))->run();
        $endTime1        = microtime(true);
        $endMemory1      = memory_get_usage();
        $time1       = $endTime1 - $startTime1;
        $time1       = round($time1, 2);
        $memory     = ($endMemory1 - $startMemory1) / 1024;

        return [
            'text' => $text,
            'time' => $time1,
            'memory' => $memory
        ];
    }

    //Google Vision Recognition Process
    public function visionProcess($path)
    {
        set_time_limit(0);
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('/app/token.json'));
        $imagePath  = public_path('storage/' . $path);
        $imageContent = file_get_contents($imagePath);
        $startTime2      = microtime(true);
        $startMemory2    = memory_get_usage();
        $text       = (new ImageAnnotatorClient())->textDetection($imageContent);
        $endTime2        = microtime(true);
        $endMemory2      = memory_get_usage();
        $time2       = $endTime2 - $startTime2;
        $time2       = round($time2, 2);
        $memory     = ($endMemory2 - $startMemory2) / 1024;

        return [
            'text' => ($text->getTextAnnotations())[0]->getDescription(),
            'time' => $time2,
            'memory' => $memory
        ];
    }

    //Compare text
    private function compareText($tesseractText, $visionText, $notepadContent)
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

    //Download Tesseract Text
    public function downloadTesseract($id)
    {
        $recognition = Recognition::find($id);
        $fileName = $recognition->name . '_tesseract.txt';

        // Buat file teks sementara
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        file_put_contents($tempFile, $recognition->tesseract_text);

        // Kembalikan file sebagai response unduhan
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    //Download Vision Text
    public function downloadVision($id)
    {
        $recognition = Recognition::find($id);
        $fileName = $recognition->name . '_vision.txt';

        // Buat file teks sementara
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        file_put_contents($tempFile, $recognition->vision_text);

        // Kembalikan file sebagai response unduhan
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    //halaman resume
    public function resume()
    {
        $data = Recognition::whereNotNull('tesseract_percentage')->orWhereNotNull('vision_percentage')->latest()->get();


        $name = $data->pluck('name')->toArray();
        $t_percentage = $data->pluck('tesseract_percentage')->toArray();
        $v_percentage = $data->pluck('vision_percentage')->toArray();
        $t_percent_avg = $data->avg('tesseract_percentage');
        $v_percent_avg = $data->avg('vision_percentage');
        $t_time = $data->pluck('tesseract_time')->toArray();
        $v_time = $data->pluck('vision_time')->toArray();
        $t_time_avg = $data->avg('tesseract_time');
        $v_time_avg = $data->avg('vision_time');
        $t_memory = $data->pluck('tesseract_memory')->toArray();
        $v_memory = $data->pluck('vision_memory')->toArray();
        $t_memory_avg = $data->avg('tesseract_memory');
        $v_memory_avg = $data->avg('vision_memory');
        return view('resume.index', [
            'active'        => 'dash_resume',
            'name'          => compact('name'),
            't_percentage'  => compact('t_percentage'),
            'v_percentage'  => compact('v_percentage'),
            't_percent_avg' => round($t_percent_avg, 2),
            'v_percent_avg' => round($v_percent_avg, 2),
            't_time'        => compact('t_time'),
            'v_time'        => compact('v_time'),
            't_time_avg'    => round($t_time_avg, 2),
            'v_time_avg'    => round($v_time_avg, 2),
            't_memory'        => compact('t_memory'),
            'v_memory'        => compact('v_memory'),
            't_memory_avg'    => round($t_memory_avg, 2),
            'v_memory_avg'    => round($v_memory_avg, 2),
        ]);
    }
}
