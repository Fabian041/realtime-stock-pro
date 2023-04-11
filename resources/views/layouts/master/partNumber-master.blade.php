@extends('layouts.root.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Master</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">Part</a>
            </li>
        </ol>
    </nav>
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
                {{-- <div class="col-md-2 text-end pe-1">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"><i class="bx bx-import me-sm-2"></i>
                        <span class="d-none d-sm-inline-block">Import</span></button>
                </div> --}}
                <div class="col-md-2 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPart"><i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add Manual</span></button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-basics table border-top part-datatable">
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Back Number</th>
                            <th>Part Name</th>
                            <th>Minimum Quantity</th>
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
                    <h3>Part Information</h3>
                    <p>Mastering Detail Part Information</p>
                </div>
                <form method="POST" action="{{ route('part-number.master.insertData') }}" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="part_name">Part Name</label>
                        <input type="text" id="part_name" name="part_name" class="form-control @error('part_name') is-invalid @enderror" placeholder="Oil Pan" required/>

                        @error('part_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="back_number">Back Number</label>
                        <input type="text" id="back_number" name="back_number" class="form-control @error('back_number') is-invalid @enderror" placeholder="CI05" required/>

                        @error('back_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="part_number">Part Number</label>
                        <input type="text" id="part_number" name="part_number" class="form-control @error('part_number') is-invalid @enderror" placeholder="212130-21250" required/>

                        @error('part_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="qty_limit">Quantity Limit</label>
                        <input type="number" id="qty_limit" name="qty_limit" class="form-control @error('qty_limit') is-invalid @enderror" placeholder="1920" required/>

                        @error('qty_limit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <h5 class="card-header">Select Status</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md mb-md-0 mb-2">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="customRadioSvg1">
                                                <span class="custom-option-body">
                                                    <span class="custom-option-title"> Status 0 </span>
                                                    <small>Melalui satu proses produksi <br> (ex: only DC)</small>
                                                </span>
                                                <input name="status" class="form-check-input" type="radio" value="0" id="customRadioSvg1" checked />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md mb-md-0 mb-2">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="customRadioSvg2">
                                                <span class="custom-option-body">
                                                    <span class="custom-option-title"> Status 1 </span>
                                                    <small>Melalui dua proses produksi <br> (ex: DC & MA)</small>
                                                </span>
                                                <input name="status" class="form-check-input" type="radio" value="1" id="customRadioSvg2" />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="customRadioSvg3">
                                                <span class="custom-option-body">
                                                    <span class="custom-option-title"> Status 2 </span>
                                                    <small>Melalui semua proses produksi <br>(ex: DC, MA & ASSY)</small>
                                                </span>
                                                <input name="status" class="form-check-input" type="radio" value="2" id="customRadioSvg3" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
@foreach ($parts as $part)
    <div class="modal fade" id="edit-{{ $part->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Part Information</h3>
                    <p>Mastering Detail Part Information</p>
                </div>
                <form method="POST" action="/master/part-number-master/update/{{ $part->id }}" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="part_name">Part Name</label>
                        <input type="text" id="part_name" name="part_name" class="form-control @error('part_name') is-invalid @enderror" placeholder="Oil Pan" value="{{ $part->part_name }}" required/>

                        @error('part_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="back_number">Back Number</label>
                        <input type="text" id="back_number" name="back_number" class="form-control @error('back_number') is-invalid @enderror" placeholder="CI05" value="{{ $part->back_number }}" required/>

                        @error('back_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="part_number">Part Number</label>
                        <input type="text" id="part_number" name="part_number" class="form-control @error('part_number') is-invalid @enderror" placeholder="212130-21250" value="{{ $part->part_number }}"  required/>

                        @error('part_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="qty_limit">Minimum Quantity</label>
                        <input type="number" id="qty_limit" name="qty_limit" class="form-control @error('qty_limit') is-invalid @enderror" placeholder="1920" value="{{ $part->qty_limit }}"  required/>

                        @error('qty_limit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <h5 class="card-header">Select Status</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md mb-md-0 mb-2">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="customRadioSvg1">
                                                <span class="custom-option-body">
                                                    <span class="custom-option-title"> Status 0 </span>
                                                    <small>Melalui satu proses produksi <br> (ex: only DC)</small>
                                                </span>
                                                <input name="status" class="form-check-input" type="radio" value="0" id="customRadioSvg1" {{ $part->status == 0 ? 'checked' : '' }} />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md mb-md-0 mb-2">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="customRadioSvg2">
                                                <span class="custom-option-body">
                                                    <span class="custom-option-title"> Status 1 </span>
                                                    <small>Melalui dua proses produksi <br> (ex: DC & MA)</small>
                                                </span>
                                                <input name="status" class="form-check-input" type="radio" value="1" id="customRadioSvg2" {{ $part->status == 1 ? 'checked' : '' }}/>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-check custom-option custom-option-icon">
                                            <label class="form-check-label custom-option-content" for="customRadioSvg3">
                                                <span class="custom-option-body">
                                                    <span class="custom-option-title"> Status 2 </span>
                                                    <small>Melalui semua proses produksi <br>(ex: DC, MA & ASSY)</small>
                                                </span>
                                                <input name="status" class="form-check-input" type="radio" value="2" id="customRadioSvg3" {{ $part->status == 2 ? 'checked' : '' }} />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
@endforeach

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload Parts</h5>
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
{{-- end modal --}}

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    
    $(document).ready(function () {
        $('.part-datatable').DataTable({
            ajax: `{{ route('part-number.master.getData') }}`,
            columns: [
                { data: 'part_number' },
                { data: 'back_number' },
                { data: 'part_name' },
                { data: 'qty_limit' },
                { data: 'edit', orderable: false, searchable: false},
            ],
        });
    });
</script>

@endsection