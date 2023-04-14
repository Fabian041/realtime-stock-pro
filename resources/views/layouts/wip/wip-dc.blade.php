@extends('layouts.root.main')

@section('content')
<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">WIP Stock</a>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0);" class="active">DC Area</a>
            </li>
        </ol>
    </nav>
</div>        
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="accordion mt-1 accordion-without-arrow" id="accordionWithIcon">
            <div class="card accordion-item active p-3">
                <div class="accordion-header d-flex align-items-center row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-lg btn-label-warning px-5 py-4" data-bs-toggle="collapse" data-bs-target="#accordionIcon-1" aria-controls="accordionIcon-1"">TCC</button>
                    </div>
                    
                    <div class="col-md-6 text-end mt-2">
                        <span class="mb-1">Total Stock</span>
                        <h3 class="card-title text-nowrap mt-2"><strong id="tcc" class="quantity">{{ $tcc }}</strong> Pcs</h3>
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
    <div class="col-lg-6 mb-4">
        <div class="accordion mt-1 accordion-without-arrow" id="accordionWithIcon">
            <div class="card accordion-item active p-3">
                <div class="accordion-header d-flex align-items-center row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-lg btn-label-danger px-5 py-4" data-bs-toggle="collapse" data-bs-target="#accordionIcon-2" aria-controls="accordionIcon-2"">OPN</button>
                    </div>
                    
                    <div class="col-md-6 text-end mt-2">
                        <span class="mb-1">Total Stock</span>
                        <h3 class="card-title text-nowrap mt-2"><strong id="opn" class="quantity">{{ $opn }}</strong> Pcs</h3>
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
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                    <div class="card-body">
                        <div id="tccChart"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                    <div class="card-body">
                        <div id="opnChart"></div>     
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@vite('resources/js/app.js')
<script src="https://cdn.jsdelivr.net/npm/countup.js@1.9.3/dist/countUp.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script> 
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $( document ).ready(function() {

        let tccCounter = new CountUp('tcc', 0);
        let opnCounter = new CountUp('opn', 0);

        var pusher = new Pusher('31df202f78fc0dace852', {
                cluster: 'ap1',
                forceTLS: true
        });

        pusher.subscribe('stock-wip').bind('StockDataUpdated', function(data) {
            
            let dataTcc = data[0] ? data[0] : document.querySelector('#tcc').innerText
            let dataOpn = data[1] ? data[1] : document.querySelector('#opn').innerText

            document.querySelector('#tcc').innerText = dataTcc;
            document.querySelector('#opn').innerText = dataOpn;

            tccCounter.update(dataTcc);
            opnCounter.update(dataOpn);

        });

        getTcc();
        setInterval(() => {
            getTcc();
        }, 5000);

        getOpn();
        setInterval(() => {
            getOpn();
        }, 5000);

        $('.quantity').each(function () {
            var $this = $(this);
            console.log($this.text());
            jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
                duration: 1000,
                easing: 'swing',
                step: function () {
                $this.text(Math.ceil(this.Counter));
                }
            });
        });

        var options = {
            chart: {
                height: 300,
                type: 'bar',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    from: 'bottom',
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: '#696CFF',
            noData: {
                text: 'No Data'
            },
            legend: {
                show: true,
                showForSingleSeries: true,
                customLegendItems: ['Actual', 'Minimum Stock'],
                markers: {
                    fillColors: ['#696CFF', '#00E396']
                }
            },
            series: [{
                name: 'Quantity',
                data: []
            }],
        }

        var options2 = {
            chart: {
                height: 300,
                type: 'bar',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    from: 'bottom',
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: '#696CFF',
            noData: {
                text: 'No Data'
            },
            legend: {
                show: true,
                showForSingleSeries: true,
                customLegendItems: ['Actual', 'Minimum Stock'],
                markers: {
                    fillColors: ['#696CFF', '#00E396']
                }
            },
            series: [{
                name: 'Quantity',
                data: []
            }],
        }

        var chartTcc = new ApexCharts(document.querySelector("#tccChart"), options);
        var chartOpn = new ApexCharts(document.querySelector("#opnChart"), options2);

        chartTcc.render(); 
        chartOpn.render(); 

        function getTcc() {
            $.ajax({
                url: '/dashboard/getWipPart/dc',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data.tccStock)
                    chartTcc.updateSeries([{
                        name: 'Total Part',
                        data: data.tccStock.map(function(item){
                            return {
                                x: `${item.part_name} - ${item.back_number}`,
                                y: item.current_stock,
                                goals: [
                                    {
                                        name: 'Minimal Stock',
                                        value: item.qty_limit,
                                        strokeHeight: 5,
                                        strokeColor: '#00E396'
                                    }
                                ]
                            }
                        })
                    }]);

                }
            });
        };

        function getOpn() {
            $.ajax({
                url: '/dashboard/getWipPart/dc',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    chartOpn.updateSeries([{
                        name: 'Total Part',
                        data: data.opnStock.map(function(item){
                            return {
                                x: `${item.part_name} - ${item.back_number}`,
                                y: item.current_stock,
                                goals: [
                                    {
                                        name: 'Minimal Stock',
                                        value: item.qty_limit,
                                        strokeHeight: 5,
                                        strokeColor: '#00E396'
                                    }
                                ]
                            }
                        })
                    }]);
                }
            });
        };

    });
</script>
@endsection