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
                <h3>Resume</h3>
                <p class="text-subtitle text-muted">Rangkuman seluruh pemrosesan OCR</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="/result">Hasil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Resume</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card-header mb-3">
                    <h3 class="card-title text-center">Perbandingan Akurasi</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Sebaran Data</h5>
                                </div>
                                <div class="card-body">
                                    <div id="percentage-area"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Rata-rata</h5>
                                </div>
                                <div class="card-body">
                                    <div id="percentage-comparison"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mt-5">
            <div class="col-12">
                <div class="card-header mb-3">
                    <h3 class="card-title text-center">Perbandingan Waktu</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Sebaran Data</h5>
                                </div>
                                <div class="card-body">
                                    <div id="time-line"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Rata-rata</h5>
                                </div>
                                <div class="card-body">
                                    <div id="time-comparison"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mt-5">
            <div class="col-12">
                <div class="card-header mb-3">
                    <h3 class="card-title text-center">Perbandingan Memori</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Sebaran Data</h5>
                                </div>
                                <div class="card-body">
                                    <div id="memory-area"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-center">Rata-rata</h5>
                                </div>
                                <div class="card-body">
                                    <div id="memory-comparison"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </section>

    <input type="hidden" id="t_percent_avg" value="{{ $t_percent_avg }}">
    <input type="hidden" id="v_percent_avg" value="{{ $v_percent_avg }}">
    <input type="hidden" id="t_time_avg" value="{{ $t_time_avg }}">
    <input type="hidden" id="v_time_avg" value="{{ $v_time_avg }}">
    <input type="hidden" id="t_memory_avg" value="{{ $t_memory_avg }}">
    <input type="hidden" id="v_memory_avg" value="{{ $v_memory_avg }}">
@endsection

@section('script')
    <script src="{{ asset('/dist/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        // Percentage Area
        var tesseractPercentageData = (@json($t_percentage)).t_percentage;
        var visionPercentageData = (@json($v_percentage)).v_percentage;
        var percentageDataOptions = {
            series: [{
                    name: 'Vision Percentage',
                    data: visionPercentageData
                },
                {
                    name: 'Tesseract Percentage',
                    data: tesseractPercentageData
                },
            ],
            chart: {
                height: 350,
                type: "area",
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
            },
            tooltip: {
                enabled: true,
                x: {
                    format: "",
                },
                y: {
                    formatter: function(val) {
                        return val.toFixed(2) + '%'; // Menambahkan persen dan membulatkan nilai
                    }
                }
            },
        };

        var percentageArea = new ApexCharts(document.querySelector("#percentage-area"), percentageDataOptions);
        percentageArea.render();

        // Percentage comparison
        var v_percentage = parseFloat(document.querySelector('#v_percent_avg').value);
        var t_percentage = parseFloat(document.querySelector('#t_percent_avg').value);
        console.log(v_percentage);
        var percentage_comparison = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: true, // Enable data labels
                formatter: function(val) {
                    return val + "%"; // Format the value to 2 decimal places
                },
                style: {
                    fontSize: '12px', // Adjust font size
                    colors: ["white"] // Adjust font color
                },
                offsetY: 0 // Adjust position of labels above the bars
            },
            chart: {
                type: "bar",
                height: 350,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {
                bar: {
                    distributed: true // Distribute colors to each bar
                }
            },
            series: [{
                name: "Accuracy",
                data: [v_percentage, t_percentage],
            }, ],
            colors: ["#008ffb", "#00e396"],
            xaxis: {
                categories: [
                    "Google Vision",
                    "Tesseract",
                ],
            },
        }
        var percentageComparison = new ApexCharts(
            document.querySelector("#percentage-comparison"),
            percentage_comparison
        )
        percentageComparison.render()

        //Time Line
        var tesseractTimeData = (@json($t_time)).t_time;
        var visionTimeData = (@json($v_time)).v_time;
        var TimeDataOptions = {
            series: [{
                    name: 'Vision Time',
                    data: visionTimeData
                },
                {
                    name: 'Tesseract Time',
                    data: tesseractTimeData
                },
            ],
            chart: {
                height: 350,
                type: "area",
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
            },
            tooltip: {
                enabled: true,
                x: {
                    format: "",
                },
                y: {
                    formatter: function(val) {
                        return val.toFixed(2) + ' s'; // Menambahkan persen dan membulatkan nilai
                    }
                }
            },
        };

        var timeLine = new ApexCharts(document.querySelector("#time-line"), TimeDataOptions);
        timeLine.render();

        // Time comparison
        var t_time = parseFloat(document.querySelector('#t_time_avg').value);
        var v_time = parseFloat(document.querySelector('#v_time_avg').value);
        var time_comparison = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: true, // Enable data labels
                formatter: function(val) {
                    return val.toFixed(2) + "s"; // Format the value to 2 decimal places
                },
                style: {
                    fontSize: '12px', // Adjust font size
                    colors: ["white"] // Adjust font color
                },
                offsetY: 0 // Adjust position of labels above the bars
            },
            chart: {
                type: "bar",
                height: 350,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {
                bar: {
                    distributed: true // Distribute colors to each bar
                }
            },
            series: [{
                name: "Time",
                data: [v_time, t_time],
            }, ],
            colors: ["#008ffb", "#00e396"],
            xaxis: {
                categories: [
                    "Google Vision",
                    "Tesseract",
                ],
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return val.toFixed(2); // Menampilkan nilai dengan 2 desimal
                    }
                }
            },
        }
        var timeComparison = new ApexCharts(
            document.querySelector("#time-comparison"),
            time_comparison
        )
        timeComparison.render()


        // Percentage Area
        var tesseractMemoryData = (@json($t_memory)).t_memory;
        var visionMemoryData = (@json($v_memory)).v_memory;
        var MemoryDataOptions = {
            series: [{
                    name: 'Vision Memory Usage',
                    data: visionMemoryData
                },
                {
                    name: 'Tesseract Memory Usage',
                    data: tesseractMemoryData
                },
            ],
            chart: {
                height: 350,
                type: "area",
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
            },
            tooltip: {
                enabled: true,
                x: {
                    format: "",
                },
                y: {
                    formatter: function(val) {
                        return val.toFixed(2) + ' KB'; // Menambahkan persen dan membulatkan nilai
                    }
                }
            },
        };

        var memoryArea = new ApexCharts(document.querySelector("#memory-area"), MemoryDataOptions);
        memoryArea.render();


        // Memory comparison
        var v_memory = parseFloat(document.querySelector('#v_memory_avg').value);
        var t_memory = parseFloat(document.querySelector('#t_memory_avg').value);
        var memory_comparison = {
            annotations: {
                position: "back",
            },
            dataLabels: {
                enabled: true, // Enable data labels
                formatter: function (val) {
                    return val + " KB"; // Format the value to 2 decimal places
                },
                style: {
                    fontSize: '12px', // Adjust font size
                    colors: ["white"] // Adjust font color
                },
                offsetY: 0 // Adjust position of labels above the bars
            },
            chart: {
                type: "bar",
                height: 350,
            },
            fill: {
                opacity: 1,
            },
            plotOptions: {
                bar: {
                    distributed: true // Distribute colors to each bar
                }
            },
            series: [{
                name: "Memory Usage",
                data: [v_memory, t_memory,],
            }, ],
            colors: ["#008ffb", "#00e396"],
            xaxis: {
                categories: [
                    "Google Vision",
                    "Tesseract",
                ],
            },
        }
        var memoryComparison = new ApexCharts(
            document.querySelector("#memory-comparison"),
            memory_comparison
        )
        memoryComparison.render()
    </script>
@endsection
