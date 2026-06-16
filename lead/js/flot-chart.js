var Script = function () {




//    graph chart


    $(function () {
        // data
        /*var data = [
         { label: "Series1",  data: 10},
         { label: "Series2",  data: 30},
         { label: "Series3",  data: 90},
         { label: "Series4",  data: 70},
         { label: "Series5",  data: 80},
         { label: "Series6",  data: 110}
         ];*/
        /*var data = [
         { label: "Series1",  data: [[1,10]]},
         { label: "Series2",  data: [[1,30]]},
         { label: "Series3",  data: [[1,90]]},
         { label: "Series4",  data: [[1,70]]},
         { label: "Series5",  data: [[1,80]]},
         { label: "Series6",  data: [[1,0]]}
         ];*/
        var data = [];
        var series = Math.floor(Math.random()*10)+1;
        for( var i = 0; i<series; i++)
        {
            data[i] = { label: "Series"+(i+1), data: 56 }
        }



        // GRAPH 1
        $.plot($("#graph1"), data,
            {
                series: {
                    pie: {
                        show: true
                    }
                },
                legend: {
                    show: false
                }
            });

        // GRAPH 2
        $.plot($("#graph2"), data,
            {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: function(label, series){
                                return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
                            },
                            background: { opacity: 0.8 }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });

        // GRAPH 3
        $.plot($("#graph3"), data,
            {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        label: {
                            show: true,
                            radius: 3/4,
                            formatter: function(label, series){
                                return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
                            },
                            background: { opacity: 0.5 }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });


        // DONUT
        $.plot($("#donut"), data,
            {
                series: {
                    pie: {
                        innerRadius: 0.5,
                        show: true
                    }
                }
            });



    });

    function pieHover(event, pos, obj)
    {
        if (!obj)
            return;
        percent = parseFloat(obj.series.percent).toFixed(2);
        $("#hover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
    }

    function pieClick(event, pos, obj)
    {
        if (!obj)
            return;
        percent = parseFloat(obj.series.percent).toFixed(2);
        alert(''+obj.series.label+': '+percent+'%');
    }

    
}();