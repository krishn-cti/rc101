$(function () {
    var dataArray = JSON.parse(document.getElementById('totalSalesLastOneYear').value);
    var dataValues = Object.values(dataArray);
    var e = {
        series: [{
            name: "Sales",
            data: dataValues
        }],
        chart: {
            height: 200,
            type: "bar",
            foreColor: "#adb0bb",
            toolbar: {
                show: !1
            },
            stacked: !0
        },
        colors: ["#0652DD"],
        plotOptions: {
            bar: {
                borderRadius: [6],
                horizontal: !1,
                barHeight: "60%",
                columnWidth: "40%"
            }
        },
        stroke: {
            show: !1
        },
        dataLabels: {
            enabled: !1
        },
        legend: {
            show: !1
        },
        grid: {
            show: !1
        },
        yaxis: {
            tickAmount: 4
        },
        xaxis: {
            categories: Object.keys(dataArray), // Use the keys (months) as categories
            axisTicks: {
                show: !1
            }
        },
        tooltip: {
            theme: "dark",
            fillSeriesColor: !1,
            x: {
                show: !1
            }
        }
    };

    var o = new ApexCharts(document.querySelector("#most-visited"), e);
    o.render();

    // var e = {
    //         series: [{
    //             name: "San Francisco",
    //             data: [70, 90, 41, 67, 120, 43]
    //         }, {
    //             name: "Diego",
    //             data: [28, 50, 33, 30, 13, 27]
    //         }, ],
    //         chart: {
    //             height: 200,
    //             type: "bar",
    //             foreColor: "#adb0bb",
    //             toolbar: {
    //                 show: !1
    //             },
    //             stacked: !0
    //         },
    //         colors: ["#0652DD", "#198754"],
    //         plotOptions: {
    //             bar: {
    //                 borderRadius: [6],
    //                 horizontal: !1,
    //                 barHeight: "60%",
    //                 columnWidth: "40%"
    //             }
    //         },
    //         stroke: {
    //             show: !1
    //         },
    //         dataLabels: {
    //             enabled: !1
    //         },
    //         legend: {
    //             show: !1
    //         },
    //         grid: {
    //             show: !1
    //         },
    //         yaxis: {
    //             tickAmount: 4
    //         },
    //         xaxis: {
    //             categories: ["01", "02", "03", "04", "05", "06"],
    //             axisTicks: {
    //                 show: !1
    //             }
    //         },
    //         tooltip: {
    //             theme: "dark",
    //             fillSeriesColor: !1,
    //             x: {
    //                 show: !1
    //             }
    //         }
    //     },
    //     o = new ApexCharts(document.querySelector("#most-visited"), e);
    // o.render();

    var e = {
            series: [{
                name: "Footware",
                data: [2.5, 2.7, 3.2, 2.6, 1.9]
            }, {
                name: "Fashionware",
                data: [-2.8, -1.1, -3, -1.5, -1.9]
            }, ],
            chart: {
                height: 200,
                type: "bar",
                toolbar: {
                    show: !1
                },
                offsetX: -20,
                stacked: !0
            },
            colors: ["#198754", "#0652DD"],
            plotOptions: {
                bar: {
                    horizontal: !1,
                    barHeight: "60%",
                    columnWidth: "20%",
                    borderRadius: [5],
                    borderRadiusApplication: "end",
                    borderRadiusWhenStacked: "all"
                }
            },
            stroke: {
                show: !1
            },
            dataLabels: {
                enabled: !1
            },
            legend: {
                show: !1
            },
            grid: {
                show: !1
            },
            yaxis: {
                min: -5,
                max: 5,
                tickAmount: 4
            },
            xaxis: {
                categories: ["Jan", "Feb", "Mar", "Apr", "May"],
                axisTicks: {
                    show: !1
                }
            },
            tooltip: {
                theme: "dark"
            }
        },
        o = new ApexCharts(document.querySelector("#revenue-updates"), e);
    o.render();

    var e = {
            color: "#adb5bd",
            series: [38, 40, 25],
            labels: ["Expance", "Revenue", "Profit"],
            chart: {
                height: 230,
                type: "donut",
                foreColor: "#adb0bb"
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "89%",
                        background: "transparent",
                        labels: {
                            show: !0,
                            name: {
                                show: !0,
                                offsetY: 7
                            },
                            value: {
                                show: !1
                            },
                            total: {
                                show: !0,
                                color: "#5A6A85",
                                fontSize: "20px",
                                fontWeight: "600",
                                label: "$500,458"
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: !1
            },
            stroke: {
                show: !1
            },
            legend: {
                show: !1
            },
            colors: ["#0652DD", "#EAEFF4", "#198754"],
            tooltip: {
                theme: "dark",
                fillSeriesColor: !1
            }
        },
        o = new ApexCharts(document.querySelector("#sales-overview"), e);

    o.render(), new ApexCharts(document.querySelector("#OrderStatistics"), {
        chart: {
            height: 300,
            type: "bar",
            animations: {
                enabled: !0,
                easing: "easeinout",
                speed: 1e3
            },
            dropShadow: {
                enabled: !0,
                opacity: .1,
                blur: 2,
                left: -1,
                top: 5
            },
            zoom: {
                enabled: !1
            },
            toolbar: {
                show: !1
            }
        },
        plotOptions: {
            bar: {
                horizontal: !1,
                borderRadius: 3,
                columnWidth: "50%",
                endingShape: "rounded"
            }
        },
        colors: ["#0652DD", "#198754"],
        dataLabels: {
            enabled: !1
        },
        grid: {
            borderColor: "#f3f3f4",
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: !0
                }
            },
            yaxis: {
                lines: {
                    show: !1
                }
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0
            }
        },
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        legend: {
            position: "top",
            horizontalAlign: "right",
            offsetY: 0,
            fontSize: "14px",
            fontFamily: "Inter, sans-serif",
            markers: {
                width: 9,
                height: 9,
                strokeWidth: 0,
                radius: 20
            },
            itemMargin: {
                horizontal: 5,
                vertical: 0
            }
        },
        tooltip: {
            theme: "light",
            marker: {
                show: !0
            },
            x: {
                show: !1
            }
        },
        stroke: {
            show: !0,
            colors: ["transparent"],
            width: 3
        },
        series: [{
            name: "Sales",
            data: [4200, 4600, 4200, 3800, 4500, 4300, 3800, 4900, 4600, 4e3, 4800, 5100]
        }, {
            name: "Revenue",
            data: [4900, 4800, 3900, 3600, 4e3, 3700, 4e3, 4200, 3800, 3900, 4100, 5900]
        }],
        xaxis: {
            crosshairs: {
                show: !0
            },
            labels: {
                offsetX: 0,
                offsetY: 5,
                style: {
                    colors: "#8380ae",
                    fontSize: "12px"
                }
            },
            tooltip: {
                enabled: !1
            }
        },
        yaxis: {
            labels: {
                formatter: function (e, o) {
                    return e / 1e3 + "K"
                },
                offsetX: -10,
                offsetY: 0,
                style: {
                    colors: "#8380ae",
                    fontSize: "12px"
                }
            }
        },
        responsive: [{
            breakpoint: 600,
            options: {
                chart: {
                    height: 230
                },
                plotOptions: {
                    bar: {
                        columnWidth: "70%"
                    }
                }
            }
        }]
    }).render();
    
} );