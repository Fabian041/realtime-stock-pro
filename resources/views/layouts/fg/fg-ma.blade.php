@extends('layouts.master.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">F/G Stock</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">MA Area</a>
            </li>
        </ol>
    </nav>
</div>        
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="accordion mt-1 accordion-without-arrow" id="accordionWithIcon">
            <div class="card accordion-item active p-3">
                <div class="accordion-header d-flex align-items-center row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-lg btn-label-warning px-5 py-4" data-bs-toggle="collapse" data-bs-target="#accordionIcon-1" aria-controls="accordionIcon-1"">TCC</button>
                    </div>
                    
                    <div class="col-md-6 text-end mt-2">
                        <span class="mb-1">Total F/G</span>
                        <h3 class="card-title text-nowrap mt-2"><strong class="quantity">87</strong> Pcs</h3>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <small class="text-muted">{{ Carbon\Carbon::now()->toDayDateTimeString() }}</small>
                    </div>
                </div>
                
                <div id="accordionIcon-1" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                    <hr>
                    <div class="accordion-body">
                        Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing marzipan gummi
                        bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping soufflé. Wafer gummi bears
                        marshmallow pastry pie.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="accordion mt-1 accordion-without-arrow" id="accordionWithIcon">
            <div class="card accordion-item active p-3">
                <div class="accordion-header d-flex align-items-center row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-lg btn-label-danger px-5 py-4" data-bs-toggle="collapse" data-bs-target="#accordionIcon-2" aria-controls="accordionIcon-2"">OPN</button>
                    </div>
                    
                    <div class="col-md-6 text-end mt-2">
                        <span class="mb-1">Total F/G</span>
                        <h3 class="card-title text-nowrap mt-2"><strong class="quantity">76</strong> Pcs</h3>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <small class="text-muted">{{ Carbon\Carbon::now()->toDayDateTimeString() }}</small>
                    </div>
                </div>
                
                <div id="accordionIcon-2" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                    <hr>
                    <div class="accordion-body">
                        Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing marzipan gummi
                        bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping soufflé. Wafer gummi bears
                        marshmallow pastry pie.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="accordion mt-1 accordion-without-arrow" id="accordionWithIcon">
            <div class="card accordion-item active p-3">
                <div class="accordion-header d-flex align-items-center row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-lg btn-label-info px-5 py-4" data-bs-toggle="collapse" data-bs-target="#accordionIcon-3" aria-controls="accordionIcon-3"">CSH</button>
                    </div>
                    
                    <div class="col-md-6 text-end mt-2">
                        <span class="mb-1">Total F/G</span>
                        <h3 class="card-title text-nowrap mt-2"><strong class="quantity">98</strong> Pcs</h3>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <small class="text-muted">{{ Carbon\Carbon::now()->toDayDateTimeString() }}</small>
                    </div>
                </div>
                
                <div id="accordionIcon-3" class="accordion-collapse collapse" data-bs-parent="#accordionIcon">
                    <hr>
                    <div class="accordion-body">
                        Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing marzipan gummi
                        bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping soufflé. Wafer gummi bears
                        marshmallow pastry pie.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-xl-12">
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">TCC</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile" aria-selected="false">OPN</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-messages" aria-controls="navs-pills-top-messages" aria-selected="false">CSH</button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                    <div class="card-body">
                        <div id="incomeChart"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                    <div class="card-body">
                        <div id="totalRevenueChart"></div>     
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-messages" role="tabpanel">
                    <div class="card-body">
                        <div id="profileReportChart"></div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script> 
<script>
    $( document ).ready(function() {

        $('.quantity').each(function () {
            var $this = $(this);
            console.log($this.text());
            jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
                duration: 1500,
                easing: 'swing',
                step: function () {
                $this.text(Math.ceil(this.Counter));
                }
            });
        });

    });
</script>
@endsection