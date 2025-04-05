<?php
require_once '../config.php';

// Check if user is logged in and is an admin


// Fetch all trainers from the team page data
$trainers = [
    [
        'name' => 'ENVER FLORENDO',
        'role' => 'Gym Trainer',
        'image' => '../img/team/smrt.jpg'
    ],
    [
        'name' => 'JHERO RICH LLANTO',
        'role' => 'Gym Trainer',
        'image' => '../img/team/kiffy.jpg'
    ],
    [
        'name' => 'JILLIANNE DACAY',
        'role' => 'Gym Trainer',
        'image' => '../img/team/baki.jpg'
    ],
    [
        'name' => 'SHAUN CHISPA',
        'role' => 'Gym Trainer',
        'image' => '../img/team/PIC5.jpg'
    ],
    [
        'name' => 'MAY THIN KHINE',
        'role' => 'Gym Trainer',
        'image' => '../img/team/PIC6.jpg'
    ],
    [
        'name' => 'JENNIFER GAO',
        'role' => 'Gym Trainer',
        'image' => '../img/team/PIC7.jpg'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers - GymLife Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 py-6 flex flex-col">
            <div class="px-6 mb-8">
                <h1 class="text-2xl font-bold">GymLife Admin</h1>
            </div>
            <nav class="flex-1">
                <a href="dashboard.php" class="flex items-center px-6 py-3 hover:bg-gray-700">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="members.php" class="flex items-center px-6 py-3 hover:bg-gray-700">
                    <i class="fas fa-users mr-3"></i>
                    Members
                </a>
                <a href="trainers.php" class="flex items-center px-6 py-3 bg-gray-700">
                    <i class="fas fa-dumbbell mr-3"></i>
                    Trainers
                </a>
                <a href="payments.php" class="flex items-center px-6 py-3 hover:bg-gray-700">
                    <i class="fas fa-credit-card mr-3"></i>
                    Payments
                </a>
               
            </nav>
            <div class="px-6 py-4">
                <a href="../logout.php" class="flex items-center text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <div class="container mx-auto px-6 py-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-semibold text-gray-800">Trainers</h2>
                    <a href="?action=add" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-plus mr-2"></i>Add Trainer
                    </a>
                </div>

                <!-- Trainers Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($trainers as $trainer): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?php echo htmlspecialchars($trainer['image']); ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>" class="w-full h-64 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($trainer['name']); ?></h3>
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($trainer['role']); ?></p>
                            <div class="flex space-x-4">
                                <a href="#" class="text-blue-500 hover:text-blue-600">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="#" class="text-blue-400 hover:text-blue-500">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="text-red-600 hover:text-red-700">
                                    <i class="fab fa-youtube"></i>
                                </a>
                                <a href="#" class="text-pink-600 hover:text-pink-700">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <div class="flex justify-end space-x-3">
                                <a href="?action=edit&id=<?php echo $trainer['name']; ?>" class="text-blue-500 hover:text-blue-600">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?php echo $trainer['name']; ?>" class="text-red-500 hover:text-red-600" onclick="return confirm('Are you sure you want to delete this trainer?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>