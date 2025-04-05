<?php
require_once '../config.php';
requireAdminLogin(); // This will redirect to login if not authenticated

// Fetch total members count
$members_query = $conn->query("SELECT COUNT(*) as total_members FROM users WHERE status = 1");
$members_count = $members_query->fetch(PDO::FETCH_ASSOC)['total_members'];

// Fetch total sales
$sales_query = $conn->query("SELECT SUM(amount) as total_sales FROM payments WHERE status = 'completed'");
$total_sales = $sales_query->fetch(PDO::FETCH_ASSOC)['total_sales'];

// Fetch monthly sales for the graph
$monthly_sales_query = $conn->query("
    SELECT DATE_FORMAT(payment_date, '%Y-%m') as month,
           SUM(amount) as monthly_total
    FROM payments 
    WHERE status = 'completed'
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthly_sales = $monthly_sales_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent payments
$recent_payments_query = $conn->query("
    SELECT p.*, u.full_name, c.class_name
    FROM payments p
    JOIN users u ON p.user_id = u.id
    JOIN classes c ON p.class_id = c.class_id
    WHERE p.status = 'completed'
    ORDER BY p.payment_date DESC
    LIMIT 5
");
$recent_payments = $recent_payments_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch trainers
$trainers_query = $conn->query("
    SELECT id, full_name, email, phone, created_at 
    FROM users 
    WHERE status = 1 
    LIMIT 6
");
$trainers = $trainers_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GymLife</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 py-6 flex flex-col">
            <div class="px-6 mb-8">
                <h1 class="text-2xl font-bold">GymLife Admin</h1>
                <p class="text-sm text-gray-400">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
            </div>
            <nav class="flex-1">
                <a href="dashboard.php" class="flex items-center px-6 py-3 bg-gray-700">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="members.php" class="flex items-center px-6 py-3 hover:bg-gray-700">
                    <i class="fas fa-users mr-3"></i>
                    Members
                </a>
                <a href="trainers.php" class="flex items-center px-6 py-3 hover:bg-gray-700">
                    <i class="fas fa-dumbbell mr-3"></i>
                    Trainers
                </a>
                <a href="payments.php" class="flex items-center px-6 py-3 hover:bg-gray-700">
                    <i class="fas fa-credit-card mr-3"></i>
                    Payments
                </a>
                
            </nav>
            <div class="px-6 py-4">
                <a href="../log.php" class="flex items-center text-red-400 hover:text-red-300" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <div class="container mx-auto px-6 py-8">
                <h2 class="text-3xl font-semibold text-gray-800 mb-8">Dashboard Overview</h2>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Members</h3>
                                <span class="text-2xl font-bold"><?php echo $members_count; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <i class="fas fa-dollar-sign text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Sales</h3>
                                <span class="text-2xl font-bold">₱<?php echo number_format($total_sales, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-75">
                                <i class="fas fa-dumbbell text-white text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Active Trainers</h3>
                                <span class="text-2xl font-bold"><?php echo count($trainers); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-xl font-semibold mb-6">Monthly Sales</h3>
                    <canvas id="salesChart" height="100"></canvas>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Payments -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold">Recent Payments</h3>
                            <a href="payments.php" class="text-blue-500 hover:text-blue-600">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($recent_payments as $payment): ?>
                                    <tr>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($payment['full_name']); ?></td>
                                        <td class="px-6 py-4">₱<?php echo number_format($payment['amount'], 2); ?></td>
                                        <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-6">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="members.php?action=add" class="flex items-center p-4 bg-green-100 rounded-lg hover:bg-green-200">
                                <i class="fas fa-user text-green-600 text-2xl mr-3"></i>
                                <span class="text-green-600 font-medium">Show Member</span>
                            </a>
                            <a href="trainers.php?action=add" class="flex items-center p-4 bg-blue-100 rounded-lg hover:bg-blue-200">
                                <i class="fas fa-user-plus text-blue-600 text-2xl mr-3"></i>
                                <span class="text-blue-600 font-medium">Add Trainer</span>
                            </a>
                            <a href="payments.php?action=add" class="flex items-center p-4 bg-purple-100 rounded-lg hover:bg-purple-200">
                                <i class="fas fa-money-bill text-purple-600 text-2xl mr-3"></i>
                                <span class="text-purple-600 font-medium">Record Payment</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = <?php echo json_encode(array_reverse($monthly_sales)); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Monthly Sales',
                    data: salesData.map(item => item.monthly_total),
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>