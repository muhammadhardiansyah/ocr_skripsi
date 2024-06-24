@extends('layout.main')

@section('style')
    <link rel="stylesheet" href="{{ asset('/dist/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/table-datatable-jquery.css') }}">
@endsection

@section('container')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show col-12 mx-auto" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('danger'))
        <div class="alert alert-danger alert-dismissible fade show col-12 mx-auto" role="alert">
            {{ session('danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show col-12 mx-auto" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Optical Character Recognition</h3>
                <p class="text-subtitle text-muted">Konversi gambar menjadi teks. (Dalam tahap penelitian)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Hasil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Hasil Optical Character Recognition</span>
                        <a href="/" class="btn btn-success icon icon-left"><i data-feather="user"></i>
                            <span>Add OCR</span></a>
                    </div>
                    <div class="card-body">
                        <table class="table" id="table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Gambar</th>
                                    <th class="text-center">Tesseract</th>
                                    <th class="text-center">Google Vision</th>
                                    {{-- <th class="text-center">Grafik</th> --}}
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recognitions as $item)
                                    <tr>
                                        <td class="text-center col-1">{{ $loop->iteration }}</td>
                                        <td class="text-center col-3">
                                            <img src="{{ asset('storage/' . $item->image) }}" class="img-fluid p-1"
                                                alt="" onerror="this.src='https://placehold.co/300?text=Image Not Found';">
                                        </td>
                                        <td class="col-3">
                                            <p>{{ \Illuminate\Support\Str::words($item->tesseract_text, 10, '...') }}</p>
                                            <p>{{ 'Waktu proses: ' . $item->tesseract_time . ' detik' }}</p>
                                            @if ($item->tesseract_percentage)
                                                <p>{{ 'Tingkat kebenaran: ' . $item->tesseract_percentage . ' %' }}</p>
                                            @endif
                                        </td>
                                        <td class="col-3">
                                            <p>{{ \Illuminate\Support\Str::words($item->vision_text, 10, '...') }}</p>
                                            <p>{{ 'Waktu proses: ' . $item->vision_time . ' detik' }}</p>
                                            @if ($item->vision_percentage)
                                                <p>{{ 'Tingkat kebenaran: ' . $item->vision_percentage . ' %' }}</p>
                                            @endif
                                        </td>
                                        {{-- <td></td> --}}
                                        <td class="text-center">
                                            <a class="btn btn-outline-info" href="/result/{{ $item->id }}"><i
                                                    class="bi bi-eye-fill"></i></a>
                                            <form action="/result/{{ $item->id }}" method="POST"
                                                class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button class="btn btn-outline-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script src="{{ asset('/dist/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                retrieve: true,
                responsive: true
            });
        });
    </script>
@endsection
