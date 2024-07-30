<?php
require 'C:\xampp\htdocs\PHP-Tots\config\db.php';
require 'C:\xampp\htdocs\PHP-Tots\config\auth.php';
redirect_if_not_admin();

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $bookingId = $_POST['booking_id'];
    $status = $_POST['status'];

    $updateSql = "UPDATE bookings SET status = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $status, $bookingId);

    if ($updateStmt->execute()) {
        $_SESSION['success_message'] = "Booking status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating booking status: " . $conn->error;
    }

    header("Location: manage_booking.php");
    exit();
}

// Handle booking deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_booking_id'])) {
    $bookingId = $_POST['delete_booking_id'];

    $deleteSql = "DELETE FROM bookings WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $bookingId);

    if ($deleteStmt->execute()) {
        $_SESSION['success_message'] = "Booking deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting booking: " . $conn->error;
    }

    header("Location: manage_booking.php");
    exit();
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] == 'true') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=bookings.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Guest Name', 'Room Type', 'Check-in Date', 'Check-out Date', 'Status']);

    $sql = "SELECT b.id, u.first_name, u.last_name, r.type AS room_type, b.check_in_date, b.check_out_date, b.status
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $row['Guest Name'] = $row['first_name'] . ' ' . $row['last_name'];
        unset($row['first_name'], $row['last_name']);
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

// Search and filtering
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build dynamic SQL query
$queryConditions = [];
$params = [];

if (!empty($searchTerm)) {
    $queryConditions[] = "(u.first_name LIKE ? OR u.last_name LIKE ?)";
    $searchTermWildcard = "%" . $searchTerm . "%";
    $params[] = &$searchTermWildcard;
    $params[] = &$searchTermWildcard;
}

if (!empty($startDate)) {
    $queryConditions[] = "b.check_in_date >= ?";
    $params[] = &$startDate;
}

if (!empty($endDate)) {
    $queryConditions[] = "b.check_out_date <= ?";
    $params[] = &$endDate;
}

$sql = "SELECT b.id, u.first_name, u.last_name, r.type AS room_type, b.check_in_date, b.check_out_date, b.status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id";

if (!empty($queryConditions)) {
    $sql .= " WHERE " . implode(" AND ", $queryConditions);
}

$searchStmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $searchStmt->bind_param($types, ...$params);
}

$searchStmt->execute();
$result = $searchStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php include("../../templates/Header.php"); ?>

    <main class="container mx-auto px-4 py-8">
        <h2 class="text-4xl font-bold mb-6">Manage Bookings</h2>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">';
            echo '<span class="block sm:inline">' . $_SESSION['success_message'] . '</span>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
            echo '<span class="block sm:inline">' . $_SESSION['error_message'] . '</span>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <!-- Search and Filter Form -->
        <form method="get" class="mb-6">
            <input type="text" name="search" placeholder="Search by guest name" class="border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <input type="date" name="start_date" class="border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($startDate); ?>">
            <input type="date" name="end_date" class="border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($endDate); ?>">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Search</button>
        </form>

        <!-- Export Button -->
        <a href="manage_bookings.php?export=true" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mb-6 inline-block">Export to CSV</a>

        <!-- Booking List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card custom-shadow">';
                    echo '<div class="card-content p-4">';
                    echo '<h3 class="text-xl font-bold">Booking ID: ' . htmlspecialchars($row['id']) . '</h3>';
                    echo '<p class="mt-2">Guest Name: ' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</p>';
                    echo '<p>Room Type: ' . htmlspecialchars($row['room_type']) . '</p>';
                    echo '<p>Check-in: ' . htmlspecialchars($row['check_in_date']) . '</p>';
                    echo '<p>Check-out: ' . htmlspecialchars($row['check_out_date']) . '</p>';
                    echo '<p>Status: ' . htmlspecialchars($row['status']) . '</p>';
                    echo '<form action="manage_booking.php" method="post" class="mt-4">';
                    echo '<input type="hidden" name="booking_id" value="' . $row['id'] . '">';
                    echo '<select name="status" class="border-gray-300 rounded-md shadow-sm">';
                    echo '<option value="confirmed" ' . ($row['status'] == 'confirmed' ? 'selected' : '') . '>Confirmed</option>';
                    echo '<option value="cancelled" ' . ($row['status'] == 'cancelled' ? 'selected' : '') . '>Cancelled</option>';
                    echo '<option value="checked_in" ' . ($row['status'] == 'checked_in' ? 'selected' : '') . '>Checked In</option>';
                    echo '</select>';
                    echo '<button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mt-2">Update Status</button>';
                    echo '</form>';
                    echo '<form action="manage_booking.php" method="post" class="mt-4">';
                    echo '<input type="hidden" name="delete_booking_id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Delete Booking</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p class='text-center mt-8'>No bookings found.</p>";
            }
            ?>
        </div>
    </main>

  

    <?php include("../../templates/Footer.php"); ?>
</body>
</html>
