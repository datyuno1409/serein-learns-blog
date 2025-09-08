<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Articles">Total Articles</h5>
                            <h3 class="my-2 py-1"><?= number_format($totalArticles) ?></h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="articles-chart" data-colors="#727cf5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Categories">Total Categories</h5>
                            <h3 class="my-2 py-1"><?= number_format($totalCategories) ?></h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="categories-chart" data-colors="#0acf97"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Comments">Total Comments</h5>
                            <h3 class="my-2 py-1"><?= number_format($totalComments) ?></h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="comments-chart" data-colors="#fa5c7c"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Users">Total Users</h5>
                            <h3 class="my-2 py-1"><?= number_format($totalUsers) ?></h3>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="users-chart" data-colors="#ffbc00"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">Export Data</a>
                            <a href="javascript:void(0);" class="dropdown-item">Print Chart</a>
                        </div>
                    </div>
                    <h4 class="header-title mb-3">Monthly Articles</h4>
                    <div id="monthly-articles-chart" class="apex-charts" data-colors="#727cf5"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Top Categories</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-nowrap">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Articles</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalCategoryArticles = array_sum(array_column($topCategories, 'count'));
                                foreach ($topCategories as $category): 
                                    $percentage = $totalCategoryArticles > 0 ? round(($category['count'] / $totalCategoryArticles) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                    <td><?= $category['count'] ?></td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="text-muted font-12"><?= $percentage ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Recent Activity</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Activity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                <tr>
                                    <td>
                                        <?php if ($activity['type'] === 'article'): ?>
                                            <span class="badge bg-success">Article</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Comment</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($activity['name']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Articles Chart
    var monthlyData = <?= json_encode($monthlyArticles) ?>;
    var months = monthlyData.map(function(item) { return item.month; });
    var counts = monthlyData.map(function(item) { return parseInt(item.count); });
    
    var options = {
        series: [{
            name: 'Articles',
            data: counts
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: months,
            title: {
                text: 'Month'
            }
        },
        yaxis: {
            title: {
                text: 'Number of Articles'
            }
        },
        colors: ['#727cf5'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            x: {
                format: 'yyyy-MM'
            }
        }
    };
    
    if (typeof ApexCharts !== 'undefined') {
        var chart = new ApexCharts(document.querySelector("#monthly-articles-chart"), options);
        chart.render();
    }
});
</script>