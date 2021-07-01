@extends('layout.index')


@section('title', 'Dashboard')


@section('content')

<div class="container-fluid mt-4 mb-4">
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
                            <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
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
                            <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
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
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="m-0 font-weight-bold text-dark">TREN PENJUALAN & PEMBELIAN</h6>
                </div>
                <div>
                    <div class="input-group">
                        <input class="form-control bg-light border-0" name="dates">
                        <div class="input-group-append">
                            <span class="input-group-text border-0 bg-light" id="basic-addon2"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">  
            <div class="">
                
                <div class="row">  
                    <div class="col-md-6 mb-3">
                         <div class="card">
                            <div class="row">
                                <div class="col-xs-6 col-md-6 border-right">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        TOTAL PENJUALAN
                                                    </div> 
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="penjualan">{{ Helper::currency($info["penjualan"]) }}</div>  
                                                </div> 
                                                <div class="col-auto"> 
                                                    <i class="fas fa-arrow-circle-up fa-2x text-primary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-6 col-md-6">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        TOTAL PEMBELIAN
                                                    </div>
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
                         </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="row">
                                <div class="col-xs-6 col-md-6 border-right">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        TOTAL PUPUK KELUAR
                                                    </div> 
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="penjualan-produk">{{ $info["penjualan_produk"] }} MT</div>  
                                                </div> 
                                                <div class="col-auto"> 
                                                    <i class="fas fa-arrow-circle-up fa-2x text-primary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-6">
                                    <div class="">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        TOTAL PUPUK MASUK
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="pembelian-produk">{{ $info["pembelian_produk"] }} MT</div>
                                                </div>
                                                <div class="col-auto"> 
                                                    <i class="fas fa-arrow-circle-down fa-2x text-success"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div id="chart-cumulative"></div> 
                </div>
                <div class="col-md-6">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
    </div> 

    <div class="row mt-4 card-deck"> 
            <div class="card shadow-sm border-top-warning">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h6 class="m-0 font-weight-bold text-dark header-text">YOUR TOP CUSTOMERS</h6>
                    </div>
                    <div class="row"> 
                        <div class="col-md-4">
                            <img src="{{ asset('img/feedback.png') }}" class="d-none d-md-block" width="100%">
                        </div>
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush">
                                @foreach($info["top_customers"] as $top_customers)
                                    <li class="list-group-item border-0 text-dark font-weight-bold">
                                        <div>{{ $top_customers->kategori == 'perusahaan' ?  $top_customers->nama.' - '.$top_customers->perusahaan : $top_customers->nama }}</div>
                                        <div class="text-warning font-size-20">Rp {{ Helper::currency($top_customers->total) }}</div>
                                    </li> 
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div> 

 
            <div class="card shadow-sm border-top-success">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h6 class="m-0 font-weight-bold text-dark">TOP SELLING PRODUCTS</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ asset('img/cart_bag.jpg') }}" class="d-none d-md-block" width="100%">
                        </div>
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush">
                                @foreach($info["top_products"] as $top_products)
                                    <li class="list-group-item border-0 text-dark font-weight-bold">
                                        <div class="text-top-selling">{{ $top_products->total }} MT</div> 
                                        <div>{{ $top_products->nama }} <small class="text-muted">{{ $top_products->spesifikasi }}</small></div>
                                        
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
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

    let penjualan = {!! json_encode($info["tren_produk"]["penjualan"]) !!};
    let pembelian = {!! json_encode($info["tren_produk"]["pembelian"]) !!};

    var options = {
        series: [{
            name: 'Penjualan',
            data: penjualan
        }, {
            name: 'Pembelian',
            data: pembelian
        }],
        chart: { 
            height: 350,
            type: 'bar',
            events: {
              dataPointSelection: function(event, chartContext, config) {
                
                // if(config.seriesIndex == 0){
                //     console.log(penjualan[config.dataPointIndex])
                // }
                // if(config.seriesIndex == 1){
                //     console.log(pembelian[config.dataPointIndex])
                // } 

                console.log(config.w.globals.series[config.seriesIndex][config.dataPointIndex])
              }
            }
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
                  return formatNumber(value, 1) + " MT";
                }
            }
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
                  return "Rp " + formatNumber(value, 2);
                }
            }
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
                $("#penjualan-produk").html(resp.penjualan_produk + " MT");
                $("#pembelian-produk").html(resp.pembelian_produk + " MT");
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
                data : resp.tren_penjualan_produk
            },
            {
                name : 'Pembelian',
                data : resp.tren_pembelian_produk
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
