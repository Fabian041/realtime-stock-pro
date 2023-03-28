@extends('layouts.master.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Master</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">Transaction</a>
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
                <div class="col-md-9"></div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransaction"><i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add Transaction</span></button>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-basics table border-top part-datatable">
                    <thead>
                        <tr>
                            <th>Transaction Code</th>
                            <th>Transaction Name</th>
                            <th>Transaction Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="addTransaction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Transaction Information</h3>
                    <p>Mastering Detail Transaction Information</p>
                </div>
                <form method="POST" action="{{ route('transaction.master.insertData') }}" id="editUserForm" class="row g-3">
                    @method('POST')
                    @csrf
                    <div class="col-12 col-md-12">
                        <label class="form-label" for="name">Transaction Name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Pulling" required/>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-12">
                        <label class="form-label" for="code">Transaction Code</label>
                        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="411" required/>

                        @error('code')
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
            ajax: `{{ route('transaction.master.getData') }}`,
            columns: [
                { data: 'code' },
                { data: 'name' },
                { data: 'status' },
            ],
        });
    });
</script>

@endsection