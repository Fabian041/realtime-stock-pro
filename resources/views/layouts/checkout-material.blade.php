@extends('layouts.master.main')

@section('content')
<div class="row">
    <div class="col">
        <div class="row">
            <h4><strong>Checkout Material</strong></h4>
        </div>   
    </div>
</div>
<div class="row">
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
                <table class="datatables-basics table border-top part-datatable">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th>Part Name</th>
                            <th>Quantity</th>
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
                    <div class="col-12 col-md-12">
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
                        <label class="form-label" for="id_part">Material Name</label>
                        <select class="form-select" id="id_part" aria-label="Default select example" name="id_part">
                            <option value="null" selected>Pilih Material</option>
                            @foreach ($parts as $item)
                                <option value="{{ $item->id }}">{{ $item->part_name }}</option>
                            @endforeach
                        </select>

                        @error('id_part')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="qty_limit">Quantity</label>
                        <input type="number" id="qty" name="qty" class="form-control @error('qty') is-invalid @enderror" placeholder="1920" required/>

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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    
    $(document).ready(function () {
        $('.part-datatable').DataTable({
            ajax: `{{ route('checkout.getData') }}`,
            columns: [
                { data: 'name' },
                { data: 'part_name' },
                { data: 'qty' },
            ],
        });
    });
</script>

@endsection