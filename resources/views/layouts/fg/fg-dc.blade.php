@extends('layouts.root.main')

@section('content')
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">F/G Stock</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);" class="active">DC Area</a>
                </li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="nav-align-top mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">CSH Part</h5>
                    </div>
                    <div class="card-body">
                        <div id="cshChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card" style="padding: 2rem;">
                <div id="cshPeriodChart"></div>
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
                ajax: `{{ route('dc.getTransaction') }}`,
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
            });

            // Add a console log to inspect the response data from the server
            table.on('xhr', function(event, settings, json) {
                console.log('Server Response Data:', json);
            });

            table.draw();

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

            // getCsh();    
            setInterval(() => {
                getCsh();
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

            var chartCsh = new ApexCharts(document.querySelector("#cshChart"), options);
            var chartDetail = new ApexCharts(document.querySelector("#detailChart"), options2);

            chartCsh.render();
            chartDetail.render();

            function getCsh() {
                $.ajax({
                    url: '/dashboard/getFgPart/dc',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        chartCsh.updateSeries([{
                            name: 'Total Part',
                            data: data.map(function(item) {
                                return {
                                    x: `${item.part_name} - ${item.back_number}`,
                                    y: item.current_stock,
                                    goals: [{
                                        name: 'Minimum Stock',
                                        value: item.qty_limit,
                                        strokeHeight: 5,
                                        strokeColor: '#F35555'
                                    }]
                                }
                            })
                        }]);

                    }
                });
            };

            function getDetail() {
                $.ajax({
                    url: '/dashboard/getFgPart/dc',
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

            function fetchData() {
                // Make AJAX request to fetch hourly data
                $.ajax({
                    url: '/periodStock/' + 2,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        area: 2
                    },
                    success: function(response) {
                        // Process the response data and update the chart
                        updateChart(response.data);
                    },
                    error: function(error) {
                        console.error("Error fetching hourly data:", error);
                    }
                });
            }

            // Function to update the chart with fetched data
            function updateChart(data) {
                if (!Array.isArray(data)) {
                    console.error("Data is not an array:", data);
                    return;
                }

                var series = [];

                // Group data by id_part
                var groups = {};
                var categories = [];
                data.forEach(function(item) {
                    if (!groups[item.back_number]) {
                        groups[item.back_number] = [];
                    }
                    if (!categories[item.id_part]) {
                        categories[item.id_part] = [];
                    }

                    groups[item.back_number].push(item.current_stock);
                    var utcDate = new Date(item.captured_at + ' UTC');
                    var isoCapturedAt = utcDate.toISOString();

                    categories[item.id_part].push(isoCapturedAt);
                });

                // Prepare series data
                for (var id_part in groups) {
                    series.push({
                        name: id_part,
                        data: groups[id_part]
                    });
                }

                var index = 0;
                for (var i = 0; i < categories.length; i++) {
                    if (categories[i] !== undefined) {
                        index = i;
                        break;
                    }
                }

                var options = {
                    series: series,
                    chart: {
                        height: 350,
                        type: 'area'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: categories[index]
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        }
                    },
                };

                var chart = new ApexCharts(document.querySelector("#cshPeriodChart"), options);

                console.log(chart);

                chart.render();
            }

            // Fetch data initially when the page loads
            fetchData();

            // Schedule periodic fetching of data every hour
            setInterval(updateChart, 1000); // 1 hour in milliseconds
        });
    </script>
@endsection
