/*
 * Scripts para geração dos gráficos e componentes dinâmicos do dashboard
 * Autor: Samuel Parente
 */

document.addEventListener("DOMContentLoaded", function () {
    // === Gráfico de Linhas - Vendas Totais por mês ===
    const ctxLine = document.getElementById("chartjs-dashboard-line")?.getContext("2d");
    if (ctxLine) {
        const gradient = ctxLine.createLinearGradient(0, 0, 0, 225);
        gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
        gradient.addColorStop(1, "rgba(215, 227, 244, 0)");

        new Chart(ctxLine, {
            type: "line",
            data: {
                labels: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                datasets: [{
                    label: "Vendas (€)",
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: window.theme.primary,
                    data: [2115, 1562, 1584, 1892, 1587, 1923, 2566, 2448, 2805, 3438, 2917, 3327]
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    filler: { propagate: false },
                    legend: { display: false },
                    tooltip: { intersect: false }
                },
                hover: { intersect: true },
                scales: {
                    xAxes: [{
                        reverse: true,
                        gridLines: { color: "rgba(0,0,0,0.0)" }
                    }],
                    yAxes: [{
                        ticks: { stepSize: 1000 },
                        display: true,
                        borderDash: [3, 3],
                        gridLines: { color: "rgba(0,0,0,0.0)" }
                    }]
                }
            }
        });
    }

    // === Gráfico de Pizza - Fontes de Tráfego ===
    const ctxPie = document.getElementById("chartjs-dashboard-pie");
    if (ctxPie) {
        new Chart(ctxPie, {
            type: "pie",
            data: {
                labels: ["Orgânico", "Pago", "Referência", "Social"],
                datasets: [{
                    data: [4306, 3801, 1689, 1234],
                    backgroundColor: [
                        window.theme.primary,
                        window.theme.warning,
                        window.theme.danger,
                        window.theme.success
                    ],
                    borderWidth: 5
                }]
            },
            options: {
                responsive: !window.MSInputMethodContext,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutoutPercentage: 75
            }
        });
    }

    // === Gráfico de Barras - Top Produtos Vendidos ===
    const ctxBar = document.getElementById("chartjs-dashboard-bar");
    if (ctxBar) {
        new Chart(ctxBar, {
            type: "bar",
            data: {
                labels: [
                    "Produto 1", "Produto 2", "Produto 3", "Produto 4", "Produto 5",
                    "Produto 6", "Produto 7", "Produto 8", "Produto 9", "Produto 10"
                ],
                datasets: [{
                    label: "Unidades Vendidas",
                    backgroundColor: window.theme.primary,
                    borderColor: window.theme.primary,
                    hoverBackgroundColor: window.theme.primary,
                    hoverBorderColor: window.theme.primary,
                    data: [120, 110, 104, 98, 95, 92, 88, 85, 82, 80],
                    barPercentage: .75,
                    categoryPercentage: .5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true },
                        gridLines: { display: true }
                    }],
                    xAxes: [{
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 45
                        },
                        gridLines: { display: false }
                    }]
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});

// === Mapa Interativo de Visitantes ===
document.addEventListener("DOMContentLoaded", function () {
    const markers = [
        { coords: [38.7169, -9.1399], name: "Lisboa" },
        { coords: [41.1496, -8.6109], name: "Porto" }
    ];

    const map = new jsVectorMap({
        map: "world",
        selector: "#world_map",
        zoomButtons: true,
        markers: markers,
        markerStyle: {
            initial: {
                r: 9,
                strokeWidth: 7,
                stokeOpacity: .4,
                fill: window.theme.primary
            },
            hover: {
                fill: window.theme.primary,
                stroke: window.theme.primary
            }
        },
        zoomOnScroll: false
    });

    window.addEventListener("resize", () => {
        map.updateSize();
    });
});

// === Calendário Interativo no Dashboard ===
document.addEventListener("DOMContentLoaded", function () {
    const date = new Date(Date.now() - 5 * 24 * 60 * 60 * 1000);
    const defaultDate = date.getUTCFullYear() + "-" + (date.getUTCMonth() + 1) + "-" + date.getUTCDate();

    document.getElementById("datetimepicker-dashboard")?.flatpickr({
        inline: true,
        prevArrow: "<span title='Mês anterior'>&laquo;</span>",
        nextArrow: "<span title='Próximo mês'>&raquo;</span>",
        defaultDate: defaultDate
    });
});