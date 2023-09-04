@extends('admin/layouts/admin_view')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="" method="POST" name="productForm" id="productForm">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" value="{{ $product->title }}"
                                                id="title" class="form-control" placeholder="Title">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="slug">Slug</label>
                                            <input type="text" name="slug" value="{{ $product->title }}"
                                                id="slug" class="form-control" placeholder="Slug" readonly>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="short_description">Short</label>
                                            <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote"
                                                placeholder="Description">
                                                {{ $product->short_description }}
                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                                placeholder="Description">
                                                {{ $product->description }}
                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="shipping_return">
                                                Shipping Return
                                            </label>
                                            <textarea name="shipping_return"
                                                id="shipping_return" cols="30" rows="10" class="summernote"
                                                placeholder="shipping_return">
                                                {{ $product->shipping_return }}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Media</h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-gallery">
                            @if ($product_images->isNotEmpty())
                                @foreach ($product_images as $image)
                                    <div class="col-md-3"
                                        id="image-row-
                                    {{ $image->id }}">
                                        <div class="card">
                                            <input type="hidden" name="image_array[]" value="{{ $image->id }}">
                                            <img src="{{ asset('upload-img/product/thumb/' . $image->image) }}"
                                                class="card-img-top">
                                            <div class="card-body">
                                                <a href="javascript:void(0)" onclick="deleteImaged({{ $image->id }})"
                                                    class="btn btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="price">Price</label>
                                            <input type="text" name="price" value="{{ $product->price }}"
                                                id="price" class="form-control" placeholder="Price">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="compare_price">Compare at Price</label>
                                            <input type="text" value="{{ $product->compare_price }}" name="compare_price"
                                                id="compare_price" class="form-control" placeholder="Compare Price">
                                            <p class="text-muted mt-3">
                                                To show a reduced price, move the product’s original price into Compare at
                                                price. Enter a lower value into Price.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Inventory</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU (Stock Keeping Unit)</label>
                                            <input type="text" name="sku" id="sku"
                                                value="{{ $product->sku }}" class="form-control" placeholder="sku">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" name="barcode" value="{{ $product->barcode }}"
                                                id="barcode" class="form-control" placeholder="Barcode">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="track_qty" value="No">
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    name="track_qty" value="Yes"
                                                    {{ $product->track_qty == 'Yes' ? 'checked' : '' }}>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" min="0" name="qty" id="qty"
                                                value="{{ $product->qty }}" class="form-control" placeholder="Qty">
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $product->status == 1 ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $product->status == 0 ? 'selected' : '' }} value="0">Block
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4  mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select a category</option>
                                        @if ($categories->isNotEmpty())
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p class="error"></p>
                                </div>
                                <div class="mb-3">
                                    <label for="category">Sub category</label>
                                    <select name="sub_category" id="sub_category" class="form-control">
                                        <option value="">Select sub category</option>
                                        @if ($sub_categories->isNotEmpty())
                                            @foreach ($sub_categories as $sub_category)
                                                <option value="{{ $sub_category->id }}"
                                                    {{ $product->sub_category_id == $sub_category->id ? 'selected' : '' }}>
                                                    {{ $sub_category->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product brand</h2>
                                <div class="mb-3">
                                    <select name="product_brand" id="product_brand" class="form-control">
                                        <option value="">Select a brand</option>
                                        @if ($brands->isNotEmpty())
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Featured product</h2>
                                <div class="mb-3">
                                    <select name="is_featured" id="is_featured" class="form-control">
                                        <option value="No" {{ $product->is_featured == 'No' ? 'selected' : '' }}>
                                            No
                                        </option>
                                        <option value="Yes" {{ $product->is_featured == 'Yes' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                    </select>
                                    <p class="error"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>

            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $("#title").change(function() {
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

        $(document).ready(function() {
            $("#productForm").submit(function(event) {
                event.preventDefault();
                var element = $(this);
                $("button[type=submit]").prop('disabled', true);
                var formData = element.serializeArray();
                $("button[type='submit']").prop('disabled', true);
                // var trackQtyCheckbox = document.getElementById("track_qty");

                // if (trackQtyCheckbox.checked) {
                //     formData =  formData.filter(function(item) {
                //                     return item.name !== "track_qty";
                //                 });
                //     formData.push({
                //         name: "track_qty",
                //         value: "Yes"
                //     });
                // }

                $.ajax({
                    url: '{{ route('products.update', $product->id) }}',
                    type: 'put',
                    data: formData,
                    dataType: 'json',

                    success: function(response) {

                        $("button[type=submit]").prop('disabled', false);
                        if (response["status"] == true) {

                            window.location.href =
                                "{{ route('products.index') }}";

                        } else {
                            var errors = response['errors'];

                            $(".error").removeClass('invalid-feedback').html('');
                            $("input[type='text'], select, input[type='number']").removeClass(
                                'is-invalid');

                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid')
                                    .siblings('p')
                                    .addClass('invalid-feedback')
                                    .html(value);
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        console.log('Something went wrong');
                    }
                });
            });

            $("#category").change(function() {
                var category_id = $(this).val();
                $.ajax({
                    url: '{{ route('product-subcategories.index') }}',
                    type: 'get',
                    data: {
                        category_id: category_id
                    },
                    dataType: 'json',

                    success: function(response) {
                        $("#sub_category").find("option").not(":first").remove(),
                            $.each(response["subCategories"], function(key, item) {
                                $("#sub_category")
                                    .append(`<option value='${item.id}'>
                                        ${item.name}
                                    </option>`)
                            });
                    },
                    error: function() {
                        console.log("something went wrong");
                    }
                });
            });
        });
        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({

            url: "{{ route('product-images.update') }}",
            maxFiles: 10,
            paramName: 'image',
            params: {
                'product_id': "{{ $product->id }}"
            },
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png, image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                // $("#image_id").val(response.image_id);
                var img_html =
                    `<div class="col-md-3" id="image-row-${response.image_id}"><div class="card">
                        <input type="hidden" name="image_array[]" value="${response.image_id}">
                        <img src="${response.image_path}" class="card-img-top">
                        <div class="card-body">
                            <a href="javascript:void(0)"
                                    onclick="deleteImaged(${response.image_id})"
                                    class="btn btn-danger">Delete</a>
                        </div>
                    </div></div>`;
                $("#product-gallery").append(img_html);
            },
            complete: function(file) {
                this.removeFile(file);
            }
        });

        function deleteImaged(id) {
            $("#image-row-" + id).remove();
            if (confirm("Are you sure you want to delete image?")) {
                $.ajax({
                    url: "{{ route('product-images.delete') }}",
                    type: 'delete',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status == true) {
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }

        }
    </script>
@endsection
