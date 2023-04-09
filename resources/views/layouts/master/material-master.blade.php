@extends('layouts.root.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Master</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">Material</a>
            </li>
        </ol>
    </nav>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card" style="padding: 2rem;">
            <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"><i class="bx bx-import me-sm-2"></i> <span class="d-none d-sm-inline-block">Import</span></button>
                </div>
            </div>
            
            <div class="card-datatable table-responsive">
                <table class="datatables-basics table border-top material-datatable">
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Part Name</th>
                            <th>Supplier</th>
                            <th>Source</th>
                            <th>Minimum Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('material.master.import') }}" method="POST" enctype="multipart/form-data">
                @method('POST')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <input type="file" id="file" name="file" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
@foreach ($materials as $material)
<div class="modal fade" id="edit-{{ $material->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-5">
                    <h3>Edit Material</h3>
                    {{-- <p>Mastering Detail BOM Information</p> --}}
                </div>
                <form method="POST" action="" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="part_number">Part Number</label>
                        <input type="text" id="part_number" name="part_number" class="form-control @error('part_number') is-invalid @enderror" placeholder="11821-18182" min="1" required/>
                        
                        @error('part_number')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="part_name">Part Name</label>
                        <input type="text" id="part_name" name="part_name" class="form-control @error('part_name') is-invalid @enderror" placeholder="Screw" min="1" required/>
                        
                        @error('part_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="supplier">Supplier</label>
                        <input type="text" id="supplier" name="supplier" class="form-control @error('supplier') is-invalid @enderror" placeholder="D1920" min="1" required/>
                        
                        @error('supplier')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="source">Source</label>
                        <input type="text" id="source" name="part_name" class="form-control @error('source') is-invalid @enderror" placeholder="CKD" min="1" required/>
                        
                        @error('source')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="qty">Quantity</label>
                        <input type="number" id="qty" name="qty" class="form-control @error('qty') is-invalid @enderror" placeholder="1920" min="1" required/>
                        
                        @error('qty')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-12 text-end mt-5">
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
        var successMessage = "{!! session('success')!!}";

        if(errorMessage){
            showToast('error', errorMessage);
        }else if(successMessage){
            showToast('success', successMessage);
        }

        $('.material-datatable').DataTable({
            ajax: `{{ route('material.master.getData') }}`,
            columns: [
            { data: 'part_number' },
            { data: 'part_name' },
            { data: 'supplier' },
            { data: 'source' },
            { data: 'limit_qty' },
            { data: 'edit', orderable: false, searchable: false },
            ],
        });
    });

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
</script>

@endsection