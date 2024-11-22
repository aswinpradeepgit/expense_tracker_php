<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <!-- Logout Button -->
    <div class="text-right p-4">
        <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Logout</a>
    </div>

    <div class="container mx-auto p-6">
        <!-- Dashboard Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <h5 class="text-lg font-semibold text-gray-600">Total Expenses (Current Month)</h5>
                <p id="total-expenses" class="text-3xl font-bold text-blue-500">₹0</p>
            </div>
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <h5 class="text-lg font-semibold text-gray-600">Monthly Limit</h5>
                <p id="monthly-limit" class="text-3xl font-bold text-green-500">₹0</p>
                <form action="adjust_limit.php" method="POST" id="adjust-limit-form">
                    <input type="number" name="new-limit" class="mt-3 p-2 border rounded-lg" placeholder="Enter new limit" required>
                    <button type="submit" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">Adjust Limit</button>
                </form>
            </div>
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <h5 class="text-lg font-semibold text-gray-600">Under Budget</h5>
                <p id="under-budget" class="text-3xl font-bold text-red-500">₹0</p>
            </div>
        </div>

        <!-- Month Selector -->
        <div class="mb-6">
            <label for="month-select" class="block text-lg font-medium text-gray-700 mb-2">Select Month:</label>
            <select id="month-select" class="w-full p-3 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="2024-11">November 2024</option>
                <option value="2024-10">October 2024</option>
            </select>
        </div>

        <!-- Category Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6" id="category-cards">
            <!-- Default Categories -->
            <?php
            include 'config.php';
            $userId = $_SESSION['user_id'];
            $result = $conn->query("SELECT id, name FROM categories WHERE user_id = $userId");
            while ($row = $result->fetch_assoc()) {
                echo "<div class='bg-white shadow rounded-lg p-4 text-center'>";
                echo "<h5 class='text-lg font-semibold text-gray-600'>" . htmlspecialchars($row['name']) . "</h5>";
                echo "<p id='" . strtolower($row['name']) . "-amount' class='text-2xl font-bold text-gray-800'>₹0</p>";
                echo "<p id='" . strtolower($row['name']) . "-percentage' class='text-sm text-gray-500'>0%</p>";
                echo "</div>";
            }
            ?>

            <!-- Add Category -->
            <div class="bg-gray-50 border-2 border-dashed border-gray-400 rounded-lg flex justify-center items-center text-gray-600 text-lg font-semibold cursor-pointer hover:bg-gray-100" id="add-category">
                + Add Category
            </div>
        </div>

        <!-- Add Expense Button -->
        <button class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600" id="add-expense-btn">
            Add Expense
        </button>

        <!-- Add Expense Modal -->
        <div id="addExpenseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-10">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Expense</h3>
                <form action="add_expense.php" method="POST" id="add-expense-form">
                    <div class="mb-4">
                        <label for="expense-amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="expense-amount" id="expense-amount" class="w-full p-3 border rounded-lg shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="expense-description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" name="expense-description" id="expense-description" class="w-full p-3 border rounded-lg shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="expense-category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="expense-category" id="expense-category" class="w-full p-3 border rounded-lg shadow-sm">
                            <?php
                            $result = $conn->query("SELECT id, name FROM categories WHERE user_id = $userId");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="expense-date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="expense-date" id="expense-date" class="w-full p-3 border rounded-lg shadow-sm" required>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">Add Expense</button>
                </form>
                <button class="mt-4 w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300" id="close-modal">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle Add Expense Modal
            $('#add-expense-btn').click(function() {
                $('#addExpenseModal').removeClass('hidden');
            });

            // Close Add Expense Modal
            $('#close-modal').click(function() {
                $('#addExpenseModal').addClass('hidden');
            });

            // Add Category Event
            $('#add-category').click(function() {
                let categoryName = prompt("Enter new category name:");
                if (categoryName) {
                    $.ajax({
                        url: 'add_category.php',
                        method: 'POST',
                        data: { 'new-category': categoryName },
                        success: function() {
                            location.reload(); // Reload to update categories
                        },
                        error: function() {
                            alert("Failed to add new category.");
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
