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
                <div class="col-md-8"></div>
                <div class="col-md-2 text-end pe-1">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"><i class="bx bx-import me-sm-2"></i>
                        <span class="d-none d-sm-inline-block">Import</span>
                    </button>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#addPart"><i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add Manual</span></button>
                </div>
            </div>
            
            <div class="card-datatable table-responsive">
                <table class="datatables-basics table border-top material-datatable">
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Part Name</th>
                            <th>Build Of Material</th>
                            <th>Area</th>
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
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('bom.master.import') }}" method="POST" enctype="multipart/form-data">
                @method('POST')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload BOM (Build Of Material)</h5>
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
<div class="modal fade" id="addPart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-5">
                    <h3>BOM Information</h3>
                    <p>Mastering Detail BOM Information</p>
                </div>
                <form method="POST" action="{{ route('bom.master.insertData') }}" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="id_part">Part Name</label>
                        <select class="form-select" id="id_part" aria-label="Default select example" name="id_part">
                            <option value="null" selected>Pilih Part</option>
                            @foreach ($parts as $item)
                                <option value="{{ $item->id }}">{{ $item->part_name }} (PN: {{ $item->part_number }})</option>
                            @endforeach
                        </select>

                        @error('id_part')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="id_material">Build Of Material</label>
                        <select class="form-select" id="id_material" aria-label="Default select example" name="id_material">
                            <option value="null" selected>Pilih Material</option>
                            @foreach ($materials as $item)
                                <option value="{{ $item->id }}">{{ $item->part_name }} (BN: {{ $item->back_number }})</option>
                            @endforeach
                        </select>

                        @error('id_material')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="id_area">Area</label>
                        <select class="form-select" id="id_area" aria-label="Default select example" name="id_area">
                            <option value="null" selected>Pilih Area</option>
                            @foreach ($areas as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>

                        @error('id_area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label class="form-label" for="qty_use">Quantity</label>
                        <input type="number" step="any" id="qty_use" name="qty_use" class="form-control @error('qty_use') is-invalid @enderror" placeholder="1920" min="1" required/>

                        @error('qty_use')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label class="form-label" for="uom">Unit</label>
                        <select class="form-select" id="uom" aria-label="Default select example" name="uom">
                            <option value="null" selected>Pilih Unit</option>
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                        </select>
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
{{-- end modal --}}

<!-- Modal -->
@foreach ($boms as $bom)   
<div class="modal fade" id="edit-{{ $bom->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-5">
                    <h3>Edit BOM Information</h3>
                    <p>Mastering Detail BOM Information</p>
                </div>
                <form method="POST" action="/master/bom-master/update/{{ $bom->id }}" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="id_part">Part Name</label>
                        <select class="form-select" id="id_part" aria-label="Default select example" name="id_part">
                            <option value="null" selected>Pilih Part</option>
                            @foreach ($parts as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $bom->id ? 'selected' : ''}}>
                                    {{ $item->part_name }} (BN: {{ $item->back_number }})
                                </option>
                            @endforeach
                        </select>

                        @error('id_part')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="id_material">Build Of Material</label>
                        <select class="form-select" id="id_material" aria-label="Default select example" name="id_material">
                            <option value="null" selected>Pilih Material</option>
                            @foreach ($materials as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $bom->id_material ? 'selected' : ''}}>
                                    {{ $item->part_name }} (BN: {{ $item->back_number }})
                                </option>
                            @endforeach
                        </select>

                        @error('id_material')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="id_area">Area</label>
                        <select class="form-select" id="id_area" aria-label="Default select example" name="id_area">
                            <option value="null" selected>Pilih Area</option>
                            @foreach ($areas as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $bom->id_area ? 'selected' : ''}}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('id_area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label class="form-label" for="qty_use">Quantity</label>
                        <input type="number" id="qty_use" name="qty_use" class="form-control @error('qty_use') is-invalid @enderror" placeholder="1920" min="1" value="{{ $bom->qty_use }}" required/>

                        @error('qty_use')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label class="form-label" for="uom">Unit</label>
                        <select class="form-select" id="uom" aria-label="Default select example" name="uom">
                            <option value="null" selected>Pilih Unit</option>
                            <option value="pcs" {{ $bom->uom == 'pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="kg" {{ $bom->uom == 'kg' ? 'selected' : '' }}>Kg</option>
                        </select>
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
            ajax: `{{ route('bom.master.getData') }}`,
            columns: [
                { data: 'part_number' },
                { data: 'part_name' },
                { data: 'material_name' },
                { data: 'name' },
                { data: 'qty_use' },
                { data: 'edit', orderable: false, searchable: false},
            ],
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
    });
</script>

@endsection