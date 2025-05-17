<?php
header('Content-Type: text/html; charset=UTF-8');
require("inc/req.php");
require("inc/header.inc.php");
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .progress {
        height: 20px;
    }
    .kpi-chart {
        max-height: 300px;
    }
</style>

<header class="page-header">
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li><a href="start.php"><i class="fa fa-home"></i></a></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>

<div class="container-fluid">

    <!-- CRM Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <input type="text" class="form-control" placeholder="🔍 Suche nach Projekten, Kunden, Tickets..." />
        </div>
    </div>

    <!-- 1. Projektübersicht -->
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading"><h4><b>Projektübersicht</b></h4></header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Website Relaunch <small>(Deadline: 25.05.2025)</small></h5>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" style="width: 70%;">70%</div>
                            </div>
                            <p>Projektleitung: <strong>Nina Maier</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h5>CRM Migration <small>(Deadline: 01.06.2025)</small></h5>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-info" style="width: 40%;">40%</div>
                            </div>
                            <p>Projektleitung: <strong>David Krause</strong></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- 2. Ticket-Status Chart -->
    <div class="row">
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading"><h4><b>Ticket-Status</b></h4></header>
                <div class="panel-body">
                    <canvas id="ticketStatusChart" class="kpi-chart"></canvas>
                </div>
            </section>
        </div>

        <!-- 3. Compliance Warnungen -->
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading"><h4><b>Compliance-Warnungen</b</h4></header>
                <div class="panel-body">
                    <div class="alert alert-danger">❗ Fehlender AV-Vertrag mit <strong>Autohaus Lenz</strong></div>
                    <div class="alert alert-warning">⏰ Frist zur DSGVO-Prüfung läuft am 20.05.2025 ab</div>
                </div>
            </section>
        </div>
    </div>

    <!-- 4. Vertragsstatus -->
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading"><h4><b>Vertragsstatus</b></h4></header>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Vertrag</th>
                            <th>Laufzeit</th>
                            <th>Kündigungsfrist</th>
                            <th>Automatische Verlängerung</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>CRM SaaS Vertrag</td>
                            <td>01.01.2024 – 31.12.2025</td>
                            <td>3 Monate</td>
                            <td>Ja</td>
                        </tr>
                        <tr>
                            <td>Webhosting</td>
                            <td>15.03.2024 – 14.03.2025</td>
                            <td>1 Monat</td>
                            <td>Nein</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <!-- 5. KPIs -->
    <div class="row">
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading"><h4><b>Kundenzufriedenheit</b></h4></header>
                <div class="panel-body">
                    <canvas id="kpiSatisfactionChart" class="kpi-chart"></canvas>
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading"><h4><b>Projekt-Auslastung</b></h4></header>
                <div class="panel-body">
                    <canvas id="kpiUtilizationChart" class="kpi-chart"></canvas>
                </div>
            </section>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading"><h4><b>Nächste Geburtstage</b></h4></header>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>John Doe</strong> - 20.05.2025
                        </li>
                        <li class="list-group-item">
                            <strong>Jane Smith</strong> - 25.05.2025
                        </li>
                        <li class="list-group-item">
                            <strong>Michael Brown</strong> - 30.05.2025
                        </li>
                    </ul>
                </div>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading"><h4><b>Aktionen</b></h4></header>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Email:</strong> Follow-up with <strong>John Doe</strong> - <em>20.05.2025</em>
                        </li>
                        <li class="list-group-item">
                            <strong>Phone:</strong> Call with <strong>Jane Smith</strong> - <em>19.05.2025</em>
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong> Proposal sent to <strong>Michael Brown</strong> - <em>18.05.2025</em>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</div>


<script>
    // Example: Fetch chatbot metrics dynamically (replace with actual API endpoint)
    fetch('/api/chatbot/metrics')
        .then(response => response.json())
        .then(data => {
            document.getElementById('automatedChats').textContent = data.automatedChats;
            document.getElementById('humanEscalations').textContent = data.humanEscalations;
            document.getElementById('customerSatisfaction').textContent = data.customerSatisfaction + '%';
            document.getElementById('avgResponseTime').textContent = data.avgResponseTime + 's';

            // Update chart
            new Chart(document.getElementById('chatbotMetricsChart'), {
                type: 'bar',
                data: {
                    labels: ['Automated', 'Escalations', 'Satisfaction', 'Response Time'],
                    datasets: [{
                        label: 'Chatbot Metrics',
                        data: [
                            data.automatedChats,
                            data.humanEscalations,
                            data.customerSatisfaction,
                            data.avgResponseTime
                        ],
                        backgroundColor: ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f']
                    }]
                }
            });
        })
        .catch(error => console.error('Error fetching chatbot metrics:', error));
</script>

<!-- Chart.js Scripts -->
<script>
    const ticketStatusChart = new Chart(document.getElementById('ticketStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Offen', 'In Bearbeitung', 'Erledigt'],
            datasets: [{
                data: [10, 5, 15],
                backgroundColor: ['#f39c12', '#3498db', '#2ecc71']
            }]
        }
    });

    const kpiSatisfactionChart = new Chart(document.getElementById('kpiSatisfactionChart'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai'],
            datasets: [{
                label: 'Zufriedenheit (1-5)',
                data: [4.2, 4.5, 4.0, 4.6, 4.4],
                backgroundColor: '#8e44ad'
            }]
        }
    });

    const kpiUtilizationChart = new Chart(document.getElementById('kpiUtilizationChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai'],
            datasets: [{
                label: 'Projekt-Auslastung (%)',
                data: [65, 75, 60, 80, 90],
                fill: true,
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: '#2980b9',
                tension: 0.3
            }]
        }
    });
</script>

<?php require 'inc/footer.inc.php'; ?>
