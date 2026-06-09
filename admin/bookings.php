<?php
require_once '../configuration/database.php';
require_once '../configuration/auth.php';

require_role('admin');

$stmt = $conn->query(
    "SELECT bookings.*, passenger.name AS passenger_name, passenger.phone, users.username
     FROM bookings
     INNER JOIN passenger ON passenger.passenger_id = bookings.passenger_id
     INNER JOIN users ON users.user_id = passenger.user_id
     ORDER BY bookings.created_at DESC"
);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Tickets</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Admin Panel</strong>
            <span><?= e($_SESSION['name']) ?></span>
        </div>
        <a href="../auth/logout.php">Logout</a>
    </header>

    <main class="page">
        <section class="panel">
            <h1>Booked Tickets</h1>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Passenger</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Seats</th>
                            <th>Notes</th>
                            <th>Booked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bookings) === 0): ?>
                            <tr><td colspan="10" class="empty">No tickets have been booked yet.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= (int) $booking['booking_id'] ?></td>
                                <td><?= e($booking['passenger_name']) ?></td>
                                <td><?= e($booking['username']) ?></td>
                                <td><?= e($booking['phone']) ?></td>
                                <td><?= e($booking['from_location']) ?> to <?= e($booking['to_location']) ?></td>
                                <td><?= e($booking['travel_date']) ?></td>
                                <td><?= e($booking['seat_class']) ?></td>
                                <td><?= (int) $booking['seats'] ?></td>
                                <td><?= e($booking['notes'] ?? '') ?></td>
                                <td><?= e($booking['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
