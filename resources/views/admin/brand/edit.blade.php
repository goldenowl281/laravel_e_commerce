@extends('admin/layouts/admin_view')

@section('content')
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Brand</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('brand.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="POST" id="brandForm" name="brandForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text"
                                            autocomplete="off"
                                            name="name"
                                            id="name"
                                            class="form-control"
                                            placeholder="Name"
                                            value="{{ $brands->name }}" >
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input readonly
                                        autocomplete="off"
                                        type="text"
                                        name="slug"
                                        id="slug"
                                        class="form-control"
                                        placeholder="Slug"
                                        value="{{ $brands->name }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ ($brands->status == 1)
                                        ? 'selected':'' }} value="1">Active
                                        </option>
                                        <option {{ ($brands->status == 2)
                                            ? 'selected':'' }} value="2">Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <a href="{{ route('brand.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
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
            $("#brandForm").submit(function(event) {
                event.preventDefault();
                var element = $(this);
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: '{{ route('brand.update', $brands->id) }}',
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
                                "{{ route('brand.index') }}";

                        } else {
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
    </script>
@endsection
