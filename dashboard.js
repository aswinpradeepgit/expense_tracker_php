document.addEventListener('DOMContentLoaded', () => {
    // Real-time UI update function
    const updateCategoryCards = () => {
        $.ajax({
            url: 'get_expense_data.php', // PHP script to get updated data
            method: 'GET',
            success: function(data) {
                const response = JSON.parse(data);
                document.getElementById('total-expenses').textContent = `₹${response.totalExpenses}`;
                document.getElementById('monthly-limit').textContent = `₹${response.monthlyLimit}`;
                document.getElementById('under-budget').textContent = `₹${response.remainingBudget} (${response.remainingPercentage}%)`;

                Object.keys(response.categories).forEach(category => {
                    if (document.getElementById(`${category}-amount`)) {
                        document.getElementById(`${category}-amount`).textContent = `₹${response.categories[category].amount}`;
                        document.getElementById(`${category}-percentage`).textContent = `${response.categories[category].percentage}%`;
                    }
                });
            }
        });
    };

    // Toggle Add Expense Modal
    $('#add-expense-btn').click(function () {
        $('#addExpenseModal').toggleClass('hidden');
    });

    // Close Modal
    $('#close-modal').click(function () {
        $('#addExpenseModal').toggleClass('hidden');
    });

    // Event listener for adding new categories
    $('#add-category').click(function () {
        let categoryName = prompt("Enter new category name:");
        if (categoryName) {
            $.ajax({
                url: 'add_category.php',
                method: 'POST',
                data: { 'new-category': categoryName },
                success: function () {
                    location.reload(); // Reload to update categories
                },
                error: function () {
                    alert("Failed to add new category.");
                }
            });
        }
    });

    // Call update after any changes
    $('#add-expense-form, #adjust-limit-form').on('submit', function () {
        updateCategoryCards();
    });

    updateCategoryCards(); // Initial update
});
