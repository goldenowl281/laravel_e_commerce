@extends('client.layouts.client-view');

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ route('client.view') }}">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('client.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-9 pt-4">
        <div class="container">
            <div class="row">
                @if (Session::has('success'))
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissible
                            fade show"
                            role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible
                            fade show"
                            role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                    </div>
                @endif

                @if (Cart::count() > 0)
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table" id="cart">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($cart_content as $item)
                                        <tr>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    @if (!empty($item->options->product_img->image))
                                                        <img class="card-img-top"
                                                            src="{{ asset('upload-img/product/thumb/' . $item->options->product_img->image) }}"
                                                            alt="">
                                                    @endif
                                                    <h2>{{ $item->name }}</h2>
                                                </div>
                                            </td>
                                            <td>${{ $item->price }}</td>
                                            <td>
                                                <div class="input-group quantity mx-auto" style="width: 100px;">
                                                    <div class="input-group-btn">
                                                        <button
                                                            class="btn btn-sm
                                                        btn-dark btn-minus p-2 pt-1 pb-1 sub"
                                                            data-id="{{ $item->rowId }}">
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text"
                                                        class="form-control form-control-sm  border-0 text-center"
                                                        value="{{ $item->qty }}">
                                                    <div class="input-group-btn">
                                                        <button
                                                            class="btn btn-sm
                                                        btn-dark btn-plus p-2 pt-1 pb-1 add"
                                                            data-id="{{ $item->rowId }}">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                ${{ $item->price * $item->qty }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteItem('{{ $item->rowId }}')"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card cart-summery">
                            <div class="sub-title">
                                <h2 class="bg-white">Cart Summery</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    <div>Subtotal</div>
                                    <div>${{ Cart::subtotal() }}</div>
                                </div>
                                <div class="d-flex justify-content-between pb-2">
                                    <div>Shipping</div>
                                    <div>$0</div>
                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div>Total</div>
                                    <div>${{ Cart::subtotal() }}</div>
                                </div>
                                <div class="pt-5">
                                    <a href="{{ route('client.checkout') }}" class="btn-dark btn btn-block w-100">Proceed
                                        to Checkout</a>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="input-group apply-coupan mt-4">
                            <input type="text" placeholder="Coupon Code" class="form-control">
                            <button class="btn btn-dark" type="button" id="button-addon2">Apply Coupon</button>
                        </div> --}}
                    </div>
                @else
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>Your card is empty</h4>
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $('.add').click(function() {
            var qty_element = $(this).parent().prev(); // Qty Input
            var qty_value = parseInt(qty_element.val());
            if (qty_value < 10) {
                var row_id = $(this).data('id');
                qty_element.val(qty_value + 1);
                var new_qty = qty_element.val();
                updateCart(row_id, new_qty);

            }
        });

        $('.sub').click(function() {
            var qty_element = $(this).parent().next();
            var qty_value = parseInt(qty_element.val());
            if (qty_value > 1) {
                var row_id = $(this).data('id');
                qty_element.val(qty_value - 1);
                var new_qty = qty_element.val();
                updateCart(row_id, new_qty);
            }
        });

        function updateCart(row_id, qty) {
            $.ajax({
                url: '{{ route('client.updateCart') }}',
                type: 'post',
                data: {
                    row_id: row_id,
                    qty: qty
                },
                dataType: 'json',
                success: function(response) {
                    window.location.href = '{{ route('client.cart') }}';
                }
            });
        }

        function deleteItem(row_id) {

            if (confirm('Are you sure you want to remove')) {

                $.ajax({
                    url: '{{ route('client.deleteCart') }}',
                    type: 'post',
                    data: {
                        row_id: row_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        window.location.href = '{{ route('client.cart') }}';
                    }
                });
            }
        }
    </script>
@endsection
