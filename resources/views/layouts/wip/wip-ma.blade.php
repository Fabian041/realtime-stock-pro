@extends('layouts.root.main')

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
        <div class="col-md-12 col-12">
            <div class="nav-align-top mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">TCC Part</h5>
                    </div>
                    <div class="card-body">
                        <div id="tccChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card" style="padding: 2rem;">
                <div class="row">
                    <div class="col-md-6">
                        <h5>
                            Detail Transactions
                        </h5>
                    </div>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="datatables-basics table border-top material-datatable">
                        <thead>
                            <tr>
                                <th>Part Number</th>
                                <th>Part Name</th>
                                <th>PIC</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Transaction</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                    </table>
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

            var table = $('.material-datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [3, 'desc']
                ],
                ajax: {
                    url: `{{ route('wipMa.getTransaction') }}`,
                    dataType: 'json',
                },
                columns: [{
                        data: 'part_number'
                    },
                    {
                        data: 'part_name'
                    },
                    {
                        data: 'pic'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'qty'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'type'
                    },
                ],
                pageLength: 25
            });

            $('.quantity').each(function() {
                var $this = $(this);
                jQuery({
                    Counter: 0
                }).animate({
                    Counter: $this.text()
                }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.ceil(this.Counter));
                    }
                });
            });

            // getTcc();
            setInterval(() => {
                getTcc();
            }, 5000);

            // getDetail();
            setInterval(() => {
                getDetail();
            }, 5000);

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
                    type: 'donut'
                },
                series: [],
                labels: [],
                noData: {
                    text: 'No Data'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    }
                },
                legend: {
                    position: 'bottom'
                },
            }

            var chartTcc = new ApexCharts(document.querySelector("#tccChart"), options);
            var chartDetail = new ApexCharts(document.querySelector("#detailChart"), options2);

            chartTcc.render();
            chartDetail.render();

            function getTcc() {
                $.ajax({
                    url: '/dashboard/getWipPart/ma',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        // Filter out specific item.part_name
                        var filteredData = data.filter(function(item) {
                            // Replace 'YOUR_PART_NAME' with the part_name you want to hide
                            return item.part_name !== 'TCC D41A';
                        });

                        chartTcc.updateSeries([{
                            name: 'Total Part',
                            data: filteredData.map(function(item) {
                                return {
                                    x: `${item.part_name} - ${item.back_number}`,
                                    y: item.current_stock,
                                    goals: [{
                                        name: 'Minimum Stock',
                                        value: item.qty_limit,
                                        strokeHeight: 5,
                                        strokeColor: '#F35555'
                                    }]
                                };
                            })
                        }]);
                    }
                });
            };

            function getDetail() {
                $.ajax({
                    url: '/dashboard/getWipPart/ma',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var seriesData = data.map(function(item) {
                            return item.current_stock;
                        });
                        var labelData = data.map(function(item) {
                            return item.part_name;
                        });
                        chartDetail.updateSeries(seriesData);
                        chartDetail.updateOptions({
                            labels: labelData
                        });

                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            };

        });
    </script>
@endsection
