<?php
require_once '../configuration/database.php';
require_once '../configuration/auth.php';

require_role('passenger');

$success = '';
$error = '';

$stmt = $conn->prepare('SELECT passenger_id, name, phone FROM passenger WHERE user_id = ? LIMIT 1');
$stmt->execute([$_SESSION['user_id']]);
$passenger = $stmt->fetch();

if (!$passenger) {
    $error = 'Passenger profile was not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_id = (int) ($passenger['passenger_id'] ?? 0);
    $passenger_name = trim($_POST['passenger_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $from_location = trim($_POST['from_location'] ?? '');
    $to_location = trim($_POST['to_location'] ?? '');
    $travel_date = trim($_POST['travel_date'] ?? '');
    $seat_class = trim($_POST['seat_class'] ?? '');
    $seats = (int) ($_POST['seats'] ?? 1);
    $notes = trim($_POST['notes'] ?? '');

    if ($passenger_id < 1 || $passenger_name === '' || $phone === '' || $from_location === '' || $to_location === '' || $travel_date === '' || $seat_class === '' || $seats < 1) {
        $error = 'Please fill all required fields correctly.';
    } else {
        $stmt = $conn->prepare('UPDATE passenger SET name = ?, phone = ? WHERE passenger_id = ?');
        $stmt->execute([$passenger_name, $phone, $passenger_id]);

        $stmt = $conn->prepare(
            'INSERT INTO bookings (passenger_id, from_location, to_location, travel_date, seat_class, seats, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt->execute([$passenger_id, $from_location, $to_location, $travel_date, $seat_class, $seats, $notes])) {
            $success = 'Ticket booked successfully.';
            $passenger['name'] = $passenger_name;
            $passenger['phone'] = $phone;
        } else {
            $error = 'Booking failed. Please try again.';
        }

    }
}

$passenger_id = (int) ($passenger['passenger_id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM bookings WHERE passenger_id = ? ORDER BY created_at DESC');
$stmt->execute([$passenger_id]);
$my_bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Passenger Panel</strong>
            <span><?= e($_SESSION['name']) ?></span>
        </div>
        <a href="../auth/logout.php">Logout</a>
    </header>

    <main class="page">
        <section class="panel">
            <h1>Book Ticket</h1>

            <?php if ($success): ?>
                <div class="alert success"><?= e($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="form grid">
                <label>
                    Passenger Name
                    <input type="text" name="passenger_name" value="<?= e($passenger['name'] ?? $_SESSION['name']) ?>" required>
                </label>

                <label>
                    Phone
                    <input type="text" name="phone" value="<?= e($passenger['phone'] ?? '') ?>" required>
                </label>

                <label>
                    From
                    <input type="text" name="from_location" required>
                </label>

                <label>
                    To
                    <input type="text" name="to_location" required>
                </label>

                <label>
                    Travel Date
                    <input type="date" name="travel_date" required>
                </label>

                <label>
                    Seat Class
                    <select name="seat_class" required>
                        <option value="">Select class</option>
                        <option value="Economy">Economy</option>
                        <option value="Business">Business</option>
                        <option value="First Class">First Class</option>
                    </select>
                </label>

                <label>
                    Seats
                    <input type="number" name="seats" min="1" value="1" required>
                </label>

                <label class="full">
                    Notes
                    <textarea name="notes" rows="3"></textarea>
                </label>

                <button type="submit">Book Ticket</button>
            </form>
        </section>

        <section class="panel">
            <h2>My Bookings</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Seats</th>
                            <th>Booked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($my_bookings) === 0): ?>
                            <tr><td colspan="6" class="empty">No bookings yet.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($my_bookings as $booking): ?>
                            <tr>
                                <td><?= (int) $booking['booking_id'] ?></td>
                                <td><?= e($booking['from_location']) ?> to <?= e($booking['to_location']) ?></td>
                                <td><?= e($booking['travel_date']) ?></td>
                                <td><?= e($booking['seat_class']) ?></td>
                                <td><?= (int) $booking['seats'] ?></td>
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
