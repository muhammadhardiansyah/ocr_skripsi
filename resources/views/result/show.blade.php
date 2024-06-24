@extends('layout.main')

@section('style')
    <style>
        textarea {
            overflow: hidden;
            transition: height 0.2s;
        }
    </style>
@endsection

@section('container')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Analisis Hasil</h3>
                <p class="text-subtitle text-muted">Optical Character Recognition(Dalam tahap penelitian)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="/result">Hasil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $recognition->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-9">
                @if ($recognition->tesseract_percentage && $recognition->vision_percentage)
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Perbandingan Akurasi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="card-title text-center">Tesseract</h5>
                                            <div id="tesseract-percentage-graph"></div>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="card-title text-center">Google Vision</h5>
                                            <div id="vision-percentage-graph"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title text-center">Hasil Tesseract</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <textarea class="form-control" id="contentT" readonly>
                                        {{ $recognition->tesseract_text }}
                                    </textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title text-center">Hasil Google Vision</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <textarea class="form-control" id="contentV" readonly>
                                        {{ $recognition->vision_text }}
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title text-center">Perbandingan Waktu</h5>
                            </div>
                            <div class="card-body">
                                <div id="time-comparison"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title text-center">Gambar Awal</h5>
                            </div>
                            <div class="card-body">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#image">
                                    <img src="{{ asset('storage/' . $recognition->image) }}" class="img-fluid p-1"
                                        alt="" onerror="this.src='https://placehold.co/300?text=Image Not Found';">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="image" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"
            role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="{{ asset('storage/' . $recognition->image) }}" class="img-fluid p-1" alt=""
                        onerror="this.src='https://placehold.co/300?text=Image Not Found';">
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="t_percentage" value="{{ $recognition->tesseract_percentage }}">
    <input type="hidden" id="t_time" value="{{ $recognition->tesseract_time }}">
    <input type="hidden" id="v_percentage" value="{{ $recognition->vision_percentage }}">
    <input type="hidden" id="v_time" value="{{ $recognition->vision_time }}">
@endsection

@section('script')
    <script src="{{ asset('/dist/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function adjustTextareaHeight(textarea) {
                textarea.style.height = 'auto'; // Reset the height
                textarea.style.height = textarea.scrollHeight + 'px'; // Set the height to the scroll height
            }

            var textareaT = document.getElementById('contentT');
            var textareaV = document.getElementById('contentV');
            adjustTextareaHeight(textareaT); // Adjust height on page load
            adjustTextareaHeight(textareaV); // Adjust height on page load

            textarea.addEventListener('input', function() {
                adjustTextareaHeight(this);
            });
        });
    </script>

    <script>
        // Tesseract percenctage graph
        var t_percentage = parseFloat(document.querySelector('#t_percentage').value);
        let tesseract_percentage_graph = {
            series: [t_percentage, (100 - t_percentage)],
            labels: ["True", "False"],
            colors: ["#435ebe", "#FF2975"],
            chart: {
                type: "donut",
                width: "100%",
                height: "350px",
            },
            legend: {
                position: "bottom",
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "30%",
                    },
                },
            },
        }
        var tesseractPercentageGraph = new ApexCharts(
            document.getElementById("tesseract-percentage-graph"),
            tesseract_percentage_graph
        )

        tesseractPercentageGraph.render()

        // Google Vision Percentage Graph
        var v_percentage = parseFloat(document.querySelector('#v_percentage').value);
        let vision_percentage_graph = {
            series: [v_percentage, (100 - v_percentage)],
            labels: ["True", "False"],
            colors: ["#435ebe", "#FF2975"],
            chart: {
                type: "donut",
                width: "100%",
                height: "350px",
            },
            legend: {
                position: "bottom",
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "30%",
                    },
                },
            },
        }
        var visionPercentageGraph = new ApexCharts(
            document.getElementById("vision-percentage-graph"),
            vision_percentage_graph
        )

        visionPercentageGraph.render()

        // Time comparison
        var t_time = parseFloat(document.querySelector('#t_time').value);
        var v_time = parseFloat(document.querySelector('#v_time').value);
        var time_comparison = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: false,
            },
            chart: {
                type: "bar",
                height: 300,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {},
            series: [{
                name: "Time",
                data: [t_time, v_time],
            }, ],
            colors: "#435ebe",
            xaxis: {
                categories: [
                    "Tesseract",
                    "Google Vision",
                ],
            },
        }
        var timeComparison = new ApexCharts(
            document.querySelector("#time-comparison"),
            time_comparison
        )
        timeComparison.render()
    </script>
@endsection
