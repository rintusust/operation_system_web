@extends('template.master')
@section('title','Upload Photo & Signature')
@section('breadcrumb')
    {!! Breadcrumbs::render('upload_photo_signature') !!}
@endsection
@section('content')
    <section class="content">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload photo</h3>
            </div>
            <div class="box-body">
                <div id="upload_photo">
                </div>
                <input type="file" name="photo" multiple id="photo" style="visibility: hidden;position: absolute">
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Upload signature</h3>
            </div>
            <div class="box-body">
                <div id="upload_signature">
                </div>
                <input type="file" multiple name="signature" id="signature"
                       style="visibility: hidden;position: absolute">
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function () {

            $("#upload_photo").on('click', function (e) {
                $("#photo").trigger('click');
            })
            $("#upload_signature").on('click', function (e) {
                $("#signature").trigger('click');
            })
            var files = [];
            var processQueue = false;
            var index = 0;
            var sfiles = [];
            var sprocessQueue = false;
            var sindex = 0;
            $("#photo").on('change', function () {
                if (files.length == 0) $("#upload_photo").html('')
                for (var i = 0; i < this.files.length; i++) {
                    files.push(this.files[i])
                    var html = '<div class="card">' +
                            '<div class="image-title">' + this.files[i].name + '<span class="status">Queuing...</span></div>' +
                            '<div class="progress-container">' +
                            '<div class="upload-progress"></div>' +
                            '</div>' +
                            '</div>'
                    $("#upload_photo").append(html)
                }
                if (processQueue == false) {
                    processQueue = true;
                    index = 0;
                    uploadPhotoFile();

                }
            })
            $("#signature").on('change', function () {
                if (sfiles.length == 0) $("#upload_signature").html('')
                for (var i = 0; i < this.files.length; i++) {
                    sfiles.push(this.files[i])
                    var html = '<div class="card">' +
                            '<div class="image-title">' + this.files[i].name + '<span class="status">Queuing...</span></div>' +
                            '<div class="progress-container">' +
                            '<div class="upload-progress"></div>' +
                            '</div>' +
                            '</div>'
                    $("#upload_signature").append(html)
                }
                if (sprocessQueue == false) {
                    sprocessQueue = true;
                    sindex = 0;
                    uploadSignatureFile();

                }
            })

            function uploadPhotoFile() {
                var file = files.shift();
                if (file == undefined) {
                    processQueue = false;
                    return;
                }
                var fd = new FormData();
                fd.append("file", file);

                $("#upload_photo").animate({
                    scrollTop:$("#upload_photo").scrollTop()+$("#upload_photo .card").eq(index).offset().top-$("#upload_photo").offset().top
                },100);
                $("#upload_photo .card").eq(index).find(".status").text("Processing...").addClass('text text-warning')
                $.ajax({
                    url: '/HRM/upload/photo/store',
                    data: fd,
                    type: 'post',
                    cache: false,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (evt) {
                            if (evt.lengthComputable) {
                                var percent = evt.loaded / evt.total;
                                $("#upload_photo .card").eq(index).find(".upload-progress").css({
                                    width: (percent * 100) + "%"
                                })
                            }
                        }, false)
                        return xhr;
                    },
                    success: function (response) {
                        $("#upload_photo .card").eq(index).find(".status").text("Complete").removeClass('text text-warning').addClass('text text-success')

                        index++;
                        setTimeout(function () {
                            uploadPhotoFile()
                        }, 2000);
                    },
                    error: function (response) {
                        console.log(response)
                        $("#upload_photo .card").eq(index).find(".status").text("Error").removeClass('text text-warning text-success').addClass('text text-danger')
                        index++;
                        setTimeout(function () {
                            uploadPhotoFile()
                        }, 2000);
                    }
                })
            }

            function uploadSignatureFile() {
                var file = sfiles.shift();
                if (file == undefined) {
                    sprocessQueue = false;
                    return;
                }
                var fd = new FormData();
                fd.append("file", file);
                $("#upload_signature .card").eq(sindex).find(".status").text("Processing...").addClass('text text-warning')
                $.ajax({
                    url: '/HRM/upload/signature/store',
                    data: fd,
                    type: 'post',
                    cache: false,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (evt) {
                            if (evt.lengthComputable) {
                                var percent = evt.loaded / evt.total;
                                $("#upload_signature .card").eq(sindex).find(".upload-progress").css({
                                    width: (percent * 100) + "%"
                                })
                            }
                        }, false)
                        return xhr;
                    },
                    success: function (response) {
                        $("#upload_signature .card").eq(sindex).find(".status").text("Complete").removeClass('text text-warning').addClass('text text-success')
                        sindex++;
                        setTimeout(function () {
                            uploadSignatureFile()
                        }, 2000);
                    },
                    error: function (response) {
                        console.log(response)
                        $("#upload_signature .card").eq(sindex).find(".status").text("Error").removeClass('text text-warning text-success').addClass('text text-danger')
                        sindex++;
                        setTimeout(function () {
                            uploadSignatureFile()
                        }, 2000);
                    }
                })
            }
        })
    </script>
@stop