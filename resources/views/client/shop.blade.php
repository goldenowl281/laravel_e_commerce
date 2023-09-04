@extends('client.layouts.client-view')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ route('client.shop') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Shop</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-6 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 sidebar">
                    {{-- Category --}}
                    <div class="sub-title">
                        <h2>Categories</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionExample">

                                @if ($categories->isNotEmpty())
                                    @foreach ($categories as $key => $category)
                                        @php
                                            $sub_categories = $category->subCategory;
                                        @endphp

                                        <div class="accordion-item">
                                            @if ($sub_categories->isNotEmpty())
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseOne-{{ $key }}"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        {{ $category->name }}
                                                    </button>
                                                </h2>
                                            @else
                                                <a href="{{ route('client.shop', $category->slug) }}"
                                                    class="nav-item nav-link {{ $category_selected == $category->id ? 'text-primary' : '' }}">
                                                    {{ $category->name }}
                                                </a>
                                            @endif

                                            @if ($sub_categories->isNotEmpty())
                                                <div id="collapseOne-{{ $key }}"
                                                    class="accordion-collapse collapse {{ $category_selected == $category->id ? 'show' : '' }}"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample"
                                                    style="">
                                                    <div class="accordion-body">
                                                        <div class="navbar-nav">

                                                            @foreach ($sub_categories as $sub_category)
                                                                <a href="{{ route('client.shop', [$category->slug, $sub_category->slug]) }}"
                                                                    class="nav-item nav-link
                                    {{ $sub_category_selected == $sub_category->id ? 'text-primary' : '' }} ">
                                                                    {{ $sub_category->name }}
                                                                </a>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- BRAND --}}
                    <div class="sub-title mt-5">
                        <h2>Brand</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @if ($brands->isNotEmpty())
                                @foreach ($brands as $brand)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input brand-label"
                                            {{ in_array($brand->id, $brands_array) ? 'checked' : '' }} type="checkbox"
                                            value="{{ $brand->id }}" name="brand[]" id="brand-{{ $brand->id }}">
                                        <label class="form-check-label" for="brand-{{ $brand->id }}">
                                            {{ $brand->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Price</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <input type="text" class="js-range-slider" name="my_range" value="" />
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row pb-3">
                        <div class="col-12 pb-1">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="ml-2">
                                    {{-- <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-light dropdown-toggle"
                          data-bs-toggle="dropdown">Sorting</button>
                      <div class="dropdown-menu dropdown-menu-right">
                          <a class="dropdown-item" href="#">Latest</a>
                          <a class="dropdown-item" href="#">Price High</a>
                          <a class="dropdown-item" href="#">Price Low</a>
                      </div>
                    </div> --}}
                                    <select name="sort" id="sort" class="form-control">
                                        <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>
                                            Latest
                                        </option>
                                        <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>
                                            Price High
                                        </option>
                                        <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>
                                            Price Low
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if ($products->isNotEmpty())
                            @foreach ($products as $product)
                                @php
                                    $product_img = $product->product_images->first();
                                @endphp
                                <div class="col-md-4">
                                    <div class="card product-card">
                                        <div class="product-image position-relative">
                                            <a href="" class="product-img"></a>

                                            @if (!empty($product_img->image))
                                                <img class="card-img-top"
                                                    src="{{ asset('upload-img/product/thumb/' . $product_img->image) }}"
                                                    alt="">
                                            @else
                                                <img src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                    class="card-img-top" alt="">
                                            @endif

                                            <a class="whishlist" href="222">
                                                <i class="far fa-heart"></i>
                                            </a>

                                            <div class="product-action">
                                                <a class="btn btn-dark" href="#">
                                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body text-center mt-3">
                                            <a class="h6 link" href="product.php">
                                                {{ $product->title }}
                                            </a>
                                            <div class="price mt-2">
                                                <span class="h5">
                                                    <strong>${{ $product->price }}</strong>
                                                </span>
                                                <span class="h6 text-underline">
                                                    @if ($product->compare_price > 0)
                                                        <del>${{ $product->compare_price }}</del>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="col-md-12 pt-5">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-end">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        //JS RANGE SLIDER
        rangeSlider = $(".js-range-slider").ionRangeSlider({
            type: "double",
            min: 0,
            max: 10000,
            from: {{ $price_min }},
            step: 100,
            to: {{ $price_max }},
            skin: "round",
            max_postfix: "+",
            prefix: "$ ",
            onFinish: function() {
                apply_filters()
            }
        });
        var slider = $(".js-range-slider").data("ionRangeSlider");

        $(".brand-label").change(function() {
            apply_filters();
        });

        $("#sort").change(function() {
            apply_filters();
        });

        function apply_filters() {
            //BRAND FILTER
            var brands = [];
            $(".brand-label").each(function() {
                if ($(this).is(":checked") == true) {
                    brands.push($(this).val());
                }
            });
            if (brands.length > 0) {
                url += '&brand=' + brands.toString();
            }

            //PRICE RANGE FILTER
            var url = '{{ url()->current() }}?';
            url += '&price_min=' + slider.result.from + '&price_max=' + slider.result.to;


            // SORTING FILTER
            url += '&sort=' + $("#sort").val();



            window.location.href = url;
        }
    </script>
@endsection
