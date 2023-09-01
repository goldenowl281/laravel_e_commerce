@extends('admin/layouts/admin_view')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="categoryForm" name="categoryForm">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $category->name }}" placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input readonly type="text" name="slug" id="slug"
                                        class="form-control"value="{{ $category->slug }}" placeholder="Slug">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $category->status == 1 ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $category->status == 2 ? 'selected' : '' }} value="2">Block</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="show_home">Show on Home</label>
                                    <select name="show_home" id="show_home" class="form-control">
                                        <option {{ $category->show_home == 'Yes' ? 'selected' : '' }} value="Yes">Yes</option>
                                        <option {{ $category->show_home == 'No' ? 'selected' : '' }} value="No">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image">Current Image</label>
                                    <div id="currentImageContainer">
                                        @if ($category->image)
                                            <img src="{{ asset('upload-img/category/thumb/' . $category->image) }}"
                                                alt="Current Image" style="width:100px; height;30px">
                                        @else
                                            No image uploaded.
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" value="" name="image_id" id="image_id">
                                    <label for="image">Image</label>
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            <br>Drop files here/ click to upload . <br><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $(document).ready(function() {

            $("#categoryForm").submit(function(event) {
                event.preventDefault();
                var element = $(this);
                $("button[type=submit]").prop('disabled', true);

                $.ajax({
                    url: '{{ route('categories.update', $category->id) }}',
                    type: 'put',
                    data: element.serializeArray(),
                    dataType: 'json',

                    success: function(response) {

                        $("button[type=submit]").prop('disabled', false);
                        if (response["status"] == true) {

                            $("#name").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html("");
                            $("#slug").removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html("");

                            window.location.href =
                                "{{ route('categories.index') }}?success=Category+added+Successfully";

                        } else {

                            if (response['notFound'] == true) {
                                window.location.href = "{{ route('categories.index') }}";
                            }

                            var errors = response['errors'];
                            if (errors['name']) {
                                $("#name").addClass('is-invalid')
                                    .siblings('p')
                                    .addClass('invalid-feedback')
                                    .html(errors['name']);
                            } else {
                                $("#name").removeClass('is-invalid')
                                    .siblings('p')
                                    .removeClass('invalid-feedback')
                                    .html("");
                            }
                            if (errors['slug']) {
                                $("#slug").addClass('is-invalid')
                                    .siblings('p')
                                    .addClass('invalid-feedback')
                                    .html(errors['slug']);
                            } else {
                                $("#slug").removeClass('is-invalid')
                                    .siblings('p')
                                    .removeClass('invalid-feedback')
                                    .html("");
                            }
                        }
                    },
                    error: function(jqXHR, exception) {
                        console.log('Something went wrong');
                    }
                });
            });
        });

        $("#name").change(function() {
            element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('categories.getSlug') }}',
                type: 'get',
                data: {
                    title: element.val()
                },
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        $("#slug").val(response["slug"]);
                    }
                }
            });
        });

        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            init: function() {
                this.on('addedfile', function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                });
            },
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png, image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                $("#image_id").val(response.image_id);
            }
        })
    </script>
@endsection
