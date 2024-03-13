@extends('layouts.root.main')

@section('content')
    <div class="col-xl">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Adjust your Stock</h5> <small class="text-muted float-end">Default label</small>
            </div>
            <div class="card-body">
                <form action="{{ route('stockBalancing.adjust') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Pilih Area</label>
                        <select class="form-select" id="area" aria-label="Default select example" name="area">
                            <option value="null" selected disabled>-- Pilih Area --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->name }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Pilih Back Number</label>
                        <select class="form-select" id="back_number" aria-label="Default select example" name="back_number">
                            <option value="null" selected disabled>-- Pilih Back Number --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-email">Current Stock</label>
                        <div class="input-group input-group-merge">
                            <input type="number" id="current_stock" class="form-control" placeholder="0"
                                aria-label="john.doe" aria-describedby="basic-default-email2" name="current_stock">
                        </div>
                        <div class="form-text"> Your current stock </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-phone">Actual Stock</label>
                        <input type="number" id="actual_stock" class="form-control phone-mask" placeholder="-"
                            name="actual_stock">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var errorMessage = "{!! session('error') !!}";

        if (errorMessage) {
            showToast('error', errorMessage);
        }

        function showToast(type, message) {
            Toastify({
                text: message,
                duration: 5000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#2ecc71" : "#e74c3c",
                stopOnFocus: true
            }).showToast();
        }

        $('#area').change(function() {
            $.ajax({
                url: '/getBackNumber',
                type: 'GET',
                data: {
                    area: $(this).val()
                },
                success: function(data) {
                    console.log(data);
                    $('#back_number').empty();
                    $.each(data, function(key, value) {
                        $('#back_number').append(
                            `
                            <option value="null" selected disabled>-- Pilih Back Number --</option>
                            <option value='${value.id}'> ${value.back_number}</option>
                            `
                        );
                    });
                }
            });
        });

        $('#back_number').change(function() {
            $.ajax({
                url: '/getCurrentStock',
                type: 'GET',
                data: {
                    area: $('#area').val(),
                    backNumber: $(this).val()
                },
                success: function(data) {
                    console.log(data);
                    $('#current_stock').empty();
                    $('#current_stock').val(data.current_stock)
                    $('#current_stock').attr('disabled', true)
                }
            });
        });
    });
</script>
