@extends('layout.index')


@section('title', 'Dashboard')


@section('content')

<div class="container-fluid mt-4">

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">DASHBOARD</h6>   
            <div> 

            </div>
        </div>  
    </div>

    <div class="row"> 
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TOTAL PENJUALAN
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Helper::currency($info["total_penjualan"]) }}</div>
                        </div>
                        <div class="col-auto"> 
                            <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                TOTAL PEMBELIAN    
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Helper::currency($info["total_pembelian"]) }}</div>
                        </div>
                        <div class="col-auto"> 
                            <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                TOTAL HUTANG
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Helper::currency(Helper::toFixed($info["total_hutang"], 2)) }}</div>
                        </div>
                        <div class="col-auto"> 
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                TOTAL PIUTANG    
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Helper::currency(Helper::toFixed($info["total_piutang"], 2)) }}</div>
                        </div>
                        <div class="col-auto"> 
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <div class="card shadow-sm" id="card-filter">
        <div class="card-body bg-white">
            <div class="d-flex justify-content-between">
                <div>

                </div>
                <div>
                    <div class="input-group">
                        <input class="form-control" name="dates">
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">  
            <div class="row">  
                <div class="col-xl-6 border-right col-md-6 mb-4">
                    <div class="h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">TOTAL PENJUALAN</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="penjualan">{{ Helper::currency($info["penjualan"]) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-arrow-circle-up fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="col-xl-6  col-md-6 mb-4">
                    <div class="h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">TOTAL PEMBELIAN</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="pembelian">{{ Helper::currency($info["pembelian"]) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-arrow-circle-down fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>

            <div id="chart"></div>
            <div id="chart-cumulative"></div>
        </div>
    </div> 
</div>

@endsection

@section('footer')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="{{ asset('vendor/daterangepicker/daterangepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}" />
<script type="text/javascript" src="{{ asset('vendor/apexchart/apexcharts') }}"></script>

<script>

    let start = moment().clone().startOf('month');
    let end = moment().clone().endOf('month'); 

    var options = {
        series: [{
            name: 'Penjualan',
            data: {!! json_encode($info["tren"]["penjualan"]) !!}
        }, {
            name: 'Pembelian',
            data: {!! json_encode($info["tren"]["pembelian"]) !!}
        }],
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
        yaxis: {
            labels: {
                formatter: function (value) {
                  return formatNumber(value, 2);
                }
            },
        },
        xaxis: {
            type: 'datetime',
            categories: dateRange(start, end)
        },
        tooltip: {
            x: { format: 'dd/MM/yy HH:mm' },
        },
    };

    var chart_tren = new ApexCharts(document.querySelector("#chart"), options);
    chart_tren.render();


    var options_cumulative = {
        series: [{
            name: 'Penjualan',
            data: {!! json_encode($info["tren"]["penjualan_kumulatif"]) !!}
        }, {
            name: 'Pembelian',
            data: {!! json_encode($info["tren"]["pembelian_kumulatif"]) !!}
        }],
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
        yaxis: {
            labels: {
                formatter: function (value) {
                  return formatNumber(value, 2);
                }
            },
        },
        xaxis: {
            type: 'datetime',
            categories: dateRange(start, end)
        },
        tooltip: {
            x: { format: 'dd/MM/yy HH:mm' },
        },
    }; 

    var chart_tren_cumulative = new ApexCharts(document.querySelector("#chart-cumulative"), options_cumulative);
    chart_tren_cumulative.render(); 

    $('input[name="dates"]').daterangepicker({
        startDate: start,
        endDate: end,
        opens: "left"
    },
    function(start, end, label) {
        $.ajax({
            url : '{{ url('dashboard/filter') }}',
            type : 'POST',
            data : { 
                _token : $('meta[name="csrf-token"]').attr('content'), 
                start: start.format('YYYY-MM-DD'), 
                end: end.format('YYYY-MM-DD') 
            },
            dataType : 'json',
            beforeSend : function(){
                loader('#card-filter', true); 
            },
            success : function(resp){
                $("#penjualan").html(resp.penjualan);
                $("#pembelian").html(resp.pembelian); 
                updateChart(start, end, resp);
                loader('#card-filter', false);
            },
            error : function(){
                loader('#card-filter', false);
            }
        });
    });


    function updateChart(start, end, resp)
    {
        let dates = dateRange(start, end);

        chart_tren.updateOptions( {
            xaxis: { 
                categories: dates
            }
        });

        chart_tren.updateSeries([
            {
                name : 'Penjualan',
                data : resp.tren_penjualan
            },
            {
                name : 'Pembelian',
                data : resp.tren_pembelian
            }
        ]);

        chart_tren_cumulative.updateOptions( {
            xaxis: { 
                categories: dates
            }
        });

        chart_tren_cumulative.updateSeries([
            {
                name : 'Penjualan',
                data : resp.tren_penjualan_kumulatif
            },
            {
                name : 'Pembelian',
                data : resp.tren_pembelian_kumulatif
            }
        ]);
    }
 
    function dateRange(startDate, endDate, steps = 1) {
      const dateArray = [];
      let currentDate = new Date(startDate);
      const format = "YYYY-MM-DD" 

      while (currentDate <= new Date(endDate)) {
        dateArray.push(moment(new Date(currentDate)).format(format));
        // Use UTC date to prevent problems with time zones and DST
        currentDate.setUTCDate(currentDate.getUTCDate() + steps);
      }

      return dateArray;
    } 


    
</script>

@endsection
