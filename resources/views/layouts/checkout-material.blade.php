@extends('layouts.root.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Transaction</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">Checkout Material</a>
            </li>
        </ol>
    </nav>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: 2rem;">
            <form action="" method="post">
                <label class="form-label" for="material">Scan Barcode</label>
                <input type="text" id="material" name="material" class="form-control @error('material') is-invalid @enderror" placeholder="Scan Barcode..." required/>
            </form>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-lg-12">

        {{-- alert when registered --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
            </div>
        @endif
        {{-- end of alert --}}

        <div class="card" style="padding: 2rem;">
            <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPart"><i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Checkout</span></button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-basics table border-top material-datatable">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th>Part Name</th>
                            <th>Date</th>
                            <th>PIC</th>
                            <th>Quantity</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addPart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Checkout Information</h3>
                    {{-- <p>Mastering Detail Part Information</p> --}}
                </div>
                <form method="POST" action="{{ route('checkout.store') }}" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="id_material">Material Name</label>
                        <select class="form-select" id="id_material" aria-label="Default select example" name="id_material">
                            <option value="null" selected>Pilih Material</option>
                            @foreach ($materials as $item)
                                <option value="{{ $item->id }}">{{ $item->part_name }} (PN: {{ $item->part_number }})</option>
                            @endforeach
                        </select>

                        @error('id_material')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="pcs">Box Quantity</label>
                        <input type="number" id="qty" name="qty" class="form-control @error('qty') is-invalid @enderror" placeholder="20" min="1" required/>

                        @error('qty')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="reset" class="btn btn-label-secondary me-sm-3 me-1" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- end modal --}}

<!-- Modal -->
@foreach ($checkouts as $checkout)
<div class="modal fade" id="edit-{{ $checkout->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Edit Checkout Information</h3>
                    {{-- <p>Mastering Detail Part Information</p> --}}
                </div>
                <form method="POST" action="" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-12">
                        <label class="form-label" for="id_material">Material Name</label>
                        <select class="form-select" id="id_material" aria-label="Default select example" name="id_material">
                            <option value="null" selected>Pilih Material</option>
                            @foreach ($materials as $item)
                                <option value="{{ $item->id }}">{{ $item->part_name }} (PN: {{ $item->part_number }})</option>
                            @endforeach
                        </select>

                        @error('id_material')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="area">Area</label>
                        <select class="form-select" id="area" aria-label="Default select example" name="id_area">
                            <option selected>Pilih Area</option>
                            @foreach ($area as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                        @error('area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="qty_limit">Quantity</label>
                        <input type="number" id="qty" name="qty" value="{{ $checkout->qty }}" class="form-control @error('qty') is-invalid @enderror" placeholder="1920" min="1" required/>

                        @error('qty')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="reset" class="btn btn-label-secondary me-sm-3 me-1" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
{{-- end modal --}}

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    
    $(document).ready(function () {
        
        var errorMessage = "{!! session('error') !!}";
        
        if(errorMessage){
            showToast('error', errorMessage);
        }
        
        function showToast(type, message){
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
        
        var table = $('.material-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: `{{ route('checkout.getData') }}`,
            columns: [
            { data: 'name' },
            { data: 'part_name' },
            { data: 'date' },
            { data: 'pic' },
            { data: 'qty' },
            { data: 'edit', orderable: false, searchable: false },
            ],
        });
        
        $('#material').prop('readonly', true);
        $('#material').focus();
        let barcode = "";
        let barcodecomplete = "";
        
        $('#material').keypress(function(e) {
            e.preventDefault();
            let code = (e.keyCode ? e.keyCode : e.which);
            // key enter
            if (code == 13) {
                barcodecomplete = barcode;
                barcode = "";
                // end of isi dengan line
                if (barcodecomplete.length == 122) {
                    insertProd(barcodecomplete);
                    return;
                    
                } else {
                    
                    showToast("error", "Kanban tidak dikenali");
                    
                }
            } else {
                barcode = barcode + String.fromCharCode(e.which);
            }
        });
        
        function insertProd(barcode) {
            $.ajax({
                type: 'get',
                url: "{{ url('dashboard/material-transaction/checkout/scan') }}",
                data: {
                    barcode : barcode,
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        table.draw();
                        showToast("success", `Part ${data.back_number} siap dipulling`);
                        return true;
                    } else if (data.status == "error") {
                        showToast("error", data.message);
                        return false;
                    }
                },
                error: function (xhr) {
                    if (xhr.status == 0) {
                        showToast("error", "Data tidak terkirim");
                        return;
                    }
                    showToast("error", xhr.status);
                }
            });
        }
        
    });
</script>

@endsection