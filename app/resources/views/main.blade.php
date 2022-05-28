@extends('layouts.admin')
@section('content')
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"
            integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

</head>
<body>
<h1>Поле построения графика</h1>
<form>
    @csrf
    From:
    <label style="margin-left: 50px">
        <input type="date" id="dateFrom" name="dateFrom"/>
    </label>
    <br/>
    To:
    <label style="margin-left: 68px">
        <input type="date" id="dateTo" name="dateTo" value="{{$current_date}}"/>
    </label>
    <button style="position: absolute; left: 32%; bottom: 79%" class="btn btn-primary" id="draw-btn"><i class="fa-solid fa-pen-ruler"></i></button>
</form>

<span><figure class="highcharts-figure">
    <div id="container" style="margin-top: 30px; align-content: center"></div>
    <p class="highcharts-description">Курс доллара к курсу рубля</p>
</figure></span>

</body>
<script type="text/javascript">


    Highcharts.theme = {
        colors: ['#279190'],
        chart: {
            backgroundColor: {
                linearGradient: [0, 0, 500, 500],
                stops: [
                    [0, 'rgb(51,51,53)'],
                    [1, 'rgb(51,51,53)']
                ]
            },
        },
        title: {
            style: {
                color: '#ffffff',
                font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
            }
        },
        subtitle: {
            style: {
                color: '#ffffff',
                font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
            }
        },
        yAxis: {
            title: {
                style: {
                    color: '#ffffff'
                }
            },
            labels: {
                position: {},
                style: {
                    color: '#ffffff'
                }
            }
        },
        xAxis: {
            labels: {
                style: {
                    color: '#ffffff'
                }
            }
        },
        legend: {
            itemStyle: {
                font: '9pt Trebuchet MS, Verdana, sans-serif',
                color: '#ffffff'
            },
            itemHoverStyle: {
                color: '#ffffff'
            }
        }
    };
    // Apply the theme
    Highcharts.setOptions(Highcharts.theme);
    const myChart = Highcharts.chart('container', {
        chart: {
            width: 1000,
            height: 500
        },
        title: {
            text: 'Rates of dollar'
        },
        xAxis: {
            categories: []
        },
        yAxis: {
            title: {
                text: 'Rubles',
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        plotOptions: {
            series: {
                allowPointSelect: true
            }
        },
        series: [{
            name: 'Dollar line',
            data: []
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
    });

    $(document).ready(function () {
        $("#draw-btn").on("click", function (event) {
            event.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{route('main.get')}}',
                data: {
                    _token: '{{csrf_token()}}',
                    dateFrom: $("#dateFrom").val(),
                    dateTo: $("#dateTo").val(),
                },
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    myChart.series[0].setData(data.values);
                    myChart.xAxis[0].setCategories(data.dates);
                }
            });
        });
    })
</script>
</html>
@stop
