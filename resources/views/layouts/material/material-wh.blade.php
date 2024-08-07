@extends('layouts.root.main')

@section('content')
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Component Stock</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);" class="active">PPIC Area</a>
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
                            <button type="button" class="btn btn-lg btn-label-warning px-5 py-4" data-bs-toggle="collapse"
                                data-bs-target="#accordionIcon-1" aria-controls="accordionIcon-1"">CKD</button>
                        </div>

                        <div class="col-md-6 text-end mt-2">
                            <span class="mb-1">Total Stock</span>
                            <h3 class="card-title text-nowrap mt-2"><strong id="ckd"
                                    class="quantity">{{ $ckd }}</strong> Pcs</h3>
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
                            Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing
                            marzipan gummi
                            bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping soufflé. Wafer
                            gummi bears
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
                            <button type="button" class="btn btn-lg btn-label-danger px-5 py-4" data-bs-toggle="collapse"
                                data-bs-target="#accordionIcon-2" aria-controls="accordionIcon-2"">IMPORT</button>
                        </div>

                        <div class="col-md-6 text-end mt-2">
                            <span class="mb-1">Total Stock</span>
                            <h3 class="card-title text-nowrap mt-2"><strong id="import"
                                    class="quantity">{{ $import }}</strong> Pcs</h3>
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
                            Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing
                            marzipan gummi
                            bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping soufflé. Wafer
                            gummi bears
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
                            <button type="button" class="btn btn-lg btn-label-info px-5 py-4" data-bs-toggle="collapse"
                                data-bs-target="#accordionIcon-3" aria-controls="accordionIcon-3"">LOCAL</button>
                        </div>

                        <div class="col-md-6 text-end mt-2">
                            <span class="mb-1">Total Stock</span>
                            <h3 class="card-title text-nowrap mt-2"><strong id="local"
                                    class="quantity">{{ $local }}</strong> Pcs</h3>
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
                            Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing
                            marzipan gummi
                            bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping soufflé. Wafer
                            gummi bears
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
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home"
                            aria-selected="true">CKD</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile"
                            aria-selected="false">IMPORT</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-messages" aria-controls="navs-pills-top-messages"
                            aria-selected="false">LOCAL</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                        <div class="card-body">
                            <div id="ckdChart" style="width: 100%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                        <div class="card-body">
                            <div id="importChart" style="width: 100%;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-messages" role="tabpanel">
                        <div class="card-body">
                            <div id="localChart" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/countup.js@1.9.3/dist/countUp.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {

            let ckdCounter = new CountUp('ckd', 0);
            let importCounter = new CountUp('import', 0);
            let localCounter = new CountUp('local', 0);

            var pusher = new Pusher('31df202f78fc0dace852', {
                cluster: 'ap1',
                forceTLS: true
            });

            pusher.subscribe('stock-wh').bind('StockDataUpdated', function(data) {

                let dataCkd = data[0] ? data[0] : document.querySelector('#ckd').innerText
                let dataImport = data[1] ? data[1] : document.querySelector('#import').innerText
                let dataLocal = data[2] ? data[2] : document.querySelector('#local').innerText

                document.querySelector('#ckd').innerText = dataCkd;
                document.querySelector('#import').innerText = dataImport;
                document.querySelector('#local').innerText = dataLocal;

                ckdCounter.update(dataCkd);
                importCounter.update(dataImport);
                localCounter.update(dataLocal);

            });

            getCkd();
            setInterval(function() {
                getCkd();
            }, 5000);

            getImport();
            setInterval(function() {
                getImport();
            }, 5000);

            getLocal();
            setInterval(function() {
                getLocal();
            }, 5000);
            // ulang
            $('.quantity').each(function() {
                var $this = $(this);
                // console.log($this.text());
                jQuery({
                    Counter: 0
                }).animate({
                    Counter: $this.text()
                }, {
                    duration: 1000,
                    easing: 'swing',
                    step: function() {
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
                        fillColors: ['#696CFF', '#F35555']
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
                        fillColors: ['#696CFF', '#F35555']
                    }
                },
                series: [{
                    name: 'Quantity',
                    data: []
                }],
            }

            var options3 = {
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
                plotOptions: {
                    bar: {
                        horizontal: false,
                        barWidth: '2%'
                    }
                },
                xaxis: {
                    labels: {
                        rotate: -45
                    },
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
                        fillColors: ['#696CFF', '#F35555']
                    }
                },
                series: [{
                    name: 'Quantity',
                    data: []
                }],
            }

            var chartCkd = new ApexCharts(document.querySelector("#ckdChart"), options);
            var chartImport = new ApexCharts(document.querySelector("#importChart"), options2);
            var chartLocal = new ApexCharts(document.querySelector("#localChart"), options3);

            chartCkd.render();
            chartImport.render();
            chartLocal.render();

            function getCkd() {
                $.ajax({
                    url: '/dashboard/getWhMaterial',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        chartCkd.updateSeries([{
                            name: 'Total Material',
                            data: data.dataCkd.map(function(item) {
                                return {
                                    x: item.back_number,
                                    y: item.current_stock,
                                    goals: [{
                                        name: 'Minimum Stock',
                                        value: item.limit_qty,
                                        strokeHeight: 5,
                                        strokeColor: '#F35555'
                                    }]
                                }
                            })
                        }]);


                        // let totalCkd = data.dataCkd.map( (item) => item.current_stock)
                        //                 .reduce( (acc,curr) => acc + curr );
                        // document.querySelector('#ckd').innerText = totalCkd;

                    }
                });
            };

            function getImport() {
                $.ajax({
                    url: '/dashboard/getWhMaterial',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        chartImport.updateSeries([{
                            name: 'Total Material',
                            data: data.dataImport.map(function(item) {
                                return {
                                    x: item.back_number,
                                    y: item.current_stock,
                                    goals: [{
                                        name: 'Minimal Stock',
                                        value: item.limit_qty,
                                        strokeHeight: 5,
                                        strokeColor: '#F35555'
                                    }]
                                }
                            })
                        }]);

                        // let totalImport = data.dataImport.map( (item) => item.current_stock)
                        //                 .reduce( (acc,curr) => acc + curr );
                        // document.querySelector('#import').innerText = totalImport;

                    }
                });
            };

            function getLocal() {
                $.ajax({
                    url: '/dashboard/getWhMaterial',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data.dataLocal);
                        chartLocal.updateSeries([{
                            name: 'Total Material',
                            data: data.dataLocal.map(function(item) {
                                return {
                                    x: item.back_number,
                                    y: item.current_stock,
                                    goals: [{
                                        name: 'Minimal Stock',
                                        value: item.limit_qty,
                                        strokeHeight: 5,
                                        strokeColor: '#F35555'
                                    }]
                                }
                            })
                        }]);

                        // let totalLocal = data.dataLocal.map( (item) => item.current_stock)
                        //                 .reduce( (acc,curr) => acc + curr );
                        // document.querySelector('#local').innerHTML = totalLocal;

                    }
                });
            };
        });
    </script>
@endsection
