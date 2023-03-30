@extends('layouts.root.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Transaction</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">Entry Stock OH</a>
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
                            <th>PIC</th>
                            <th>Date</th>
                            <th>Quantity</th>
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
            <form action="{{ route('entry-oh.import') }}" method="POST" enctype="multipart/form-data">
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    
    $(document).ready(function () {
        $('.material-datatable').DataTable({
            ajax: `{{ route('entry-oh.getData') }}`,
            columns: [
            { data: 'part_number' },
            { data: 'part_name' },
            { data: 'supplier' },
            { data: 'source' },
            { data: 'pic' },
            { data: 'date' },
            { data: 'qty' },
            ],
        });
    });
</script>

@endsection