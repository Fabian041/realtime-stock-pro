@extends('layouts.root.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Transaction</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">STO</a>
            </li>
        </ol>
    </nav>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: 2rem;">
            <form action="" method="post">
                <label class="form-label" for="material">Scan Barcode</label>
                <input type="text" id="material" name="material" class="form-control" placeholder="Scan Barcode..." required/>
            </form>
        </div>
    </div>
</div>
<div class="row mt-4">
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
            <form action="{{ route('wh.import') }}" method="POST" enctype="multipart/form-data">
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

        var table = $('.material-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: `{{ route('wh.getData') }}`,
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

        // $('#material').prop('readonly', true);
        $('#material').focus();
        let barcode = "";
        let barcodecomplete = "";

        $('#material').keypress(function(e) {
            e.preventDefault();
            let code = (e.keyCode ? e.keyCode : e.which);
            // key ente
            if (code == 13) {
                barcodecomplete = barcode;
                barcode = "";
                // end of isi dengan line
                if (barcodecomplete.length == 122) {
                    insertWh(barcodecomplete);
                    return;
                    
                } else {

                    showToast("error", "Kanban tidak dikenali");

                }
            } else {
                barcode = barcode + String.fromCharCode(e.which);
            }
        });

        function insertWh(barcode) {
            $.ajax({
                type: 'get',
                url: "{{ url('dashboard/material-transaction/wh/scan') }}",
                data: {
                    barcode : barcode,
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        table.draw();
                        showToast("success", `Part ${data.back_number} berhasil disimpan`);
                        return true;
                    } else if (data.code == "error") {
                        showToast("error", data.messege);
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