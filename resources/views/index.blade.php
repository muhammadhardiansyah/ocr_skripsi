@extends('layout.main')
@section('style')
    <link rel="stylesheet" href="{{ asset('/dist/assets/extensions/filepond/filepond.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/dist/assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/extensions/toastify-js/src/toastify.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('container')
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
                        <li class="breadcrumb-item active" aria-current="page">Optical Character Recognition</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Google Cloud Vision</h5>
                    </div>
                    <div class="card-body">
                        Google Cloud Vision merupakan layanan komputasi awan dari Google Cloud Platform yang menyediakan
                        layanan teknologi pengenalan gambar dan analisis visual yang canggih untuk mengidentifikasi dan
                        menganalisis gambar maupun video.
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tesseract</h5>
                    </div>
                    <div class="card-body">
                        Tesseract merupakan salah satu library Optical Character Recognition yang berfungsi untuk
                        mengkonversi citra atau gambar menjadi teks dan library ini berlisensi open source, sehingga dapat
                        terus dikembangkan oleh komunitas.
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Upload Gambar Hasil Scanner</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Anda dapat mengupload satu atau lebih gambar hasil scanner untuk dilakukan proses Optical
                            Character Recognition dengan dua library sekaligus, yaitu Tesseract dan Google Cloud Vision.
                            Maksimal gambar yang dapat diupload dalam satu kali pemrosesan yaitu 20 gambar, dan ukuran
                            maksimal setiap gambarnya adalah 5 MB.
                        </p>
                        <form action="/result" method="post" enctype="multipart/form-data" id="uploadForm">
                            <!-- File uploader with validation -->
                            @csrf
                            <input type="file" name="images[]" class="inputFile" required multiple
                                data-max-file-size="5MB" data-max-files="20">
                            <button type="submit" class="btn btn-success" id="startRecognition">
                                <i class="bi bi-plus-square mr-1"></i>
                                <span>Submit</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script
        src="{{ asset('/dist/assets/extensions/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
    </script>
    <script
        src="{{ asset('/dist/assets/extensions/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}">
    </script>
    <script src="{{ asset('/dist/assets/extensions/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js') }}">
    </script>
    <script
        src="{{ asset('/dist/assets/extensions/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
    </script>
    <script src="{{ asset('/dist/assets/extensions/filepond-plugin-image-filter/filepond-plugin-image-filter.min.js') }}">
    </script>
    <script src="{{ asset('/dist/assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}">
    </script>
    <script src="{{ asset('/dist/assets/extensions/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js') }}">
    </script>
    <script src="{{ asset('/dist/assets/extensions/filepond/filepond.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/toastify-js/src/toastify.js') }}"></script>
    <script src="{{ asset('/dist/assets/static/js/pages/filepond.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        // Initialize FilePond
        const inputElement = document.querySelector('.inputFile');
        const pond = FilePond.create(inputElement, {
            credits: null,
            allowImagePreview: false,
            allowMultiple: true,
            allowFileEncode: false,
            required: true,
            acceptedFileTypes: [
                "image/png",
                "image/jpeg",
                "image/jpg"
            ],
            fileValidateTypeDetectType: (source, type) =>
                new Promise((resolve, reject) => {
                    // Do custom type detection here and return with promise
                    resolve(type)
                }),
            storeAsFile: true,
        })

        const startRecognitionButton = document.getElementById('startRecognition');

        // Function to check validation errors and enable/disable button
        function checkValidation() {
            const hasError = pond.getFiles().some(file => file.status === 5);
            const noFiles = pond.getFiles().length === 0;
            if (noFiles || hasError) {
                startRecognitionButton.disabled = true;
            } else {
                startRecognitionButton.disabled = false;
            }
        }

        // Event listeners for FilePond
        pond.on('addfile', (error, file) => {
            checkValidation();
        });

        pond.on('removefile', () => {
            checkValidation();
        });

        pond.on('processfile', () => {
            checkValidation();
        });

        pond.on('updatefiles', () => {
            checkValidation();
        });

        // Initial check
        checkValidation();

        // Event listener for form submission
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            // Check if there are any validation errors
            const hasError = pond.getFiles().some(file => file.status === 5);
            const noFiles = pond.getFiles().length === 0;

            if (hasError || noFiles) {
                // Prevent form submission
                event.preventDefault();
                console.log('Validation error detected');
                return;
            }

            // No validation errors, show the loading SweetAlert
            Swal.fire({
                title: 'Processing',
                text: 'Please wait while the recognition is in progress',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // Update button state on load
        window.onload = function() {
            checkValidation();
        };

        // document.getElementById('startRecognition').addEventListener('click', function() {
        //     // Tampilkan SweetAlert loading
        //     Swal.fire({
        //         title: 'Processing',
        //         text: 'Please wait while the recognition is in progress',
        //         allowOutsideClick: false,
        //         didOpen: () => {
        //             Swal.showLoading()
        //         }
        //     });
        // });
    </script>
@endsection
