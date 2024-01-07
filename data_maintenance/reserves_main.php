<?php
require_once "../database/database.php";

$reserves = getAllReserves();
$sailors = getAllSailors();
$boats = getAllBoats();

$reservationsBySid = [];
foreach ($reserves as $reserve) {
    $reservationsBySid[$reserve['sid']][] = $reserve;
}
$bids = array_unique(array_column($boats, 'bid'));

if (isset($_POST["add"])) {
    $errorMessage = createReservations($_POST);

    if ($errorMessage !== null) {
        echo '<script>alert("' . $errorMessage . '");</script>';
    } else if ($errorMessage == null) {
        echo '<script>alert("Reservation added successfully"); window.location.href = "reserves_main.php";</script>';
    }
}

if (isset($_POST["delete"])) {
    $errorMessage = deleteReservations($_POST);

    if ($errorMessage !== null) {
        echo '<script>alert("' . $errorMessage . '");</script>';
    } else if ($errorMessage == null) {
        echo '<script>alert("Reservation deleted successfully"); window.location.href = "reserves_main.php";</script>';
    }
}
if (isset($_POST["submit"])) {
    $errorMessage = updateReservations($_POST);

    if ($errorMessage !== null) {
        echo '<script>alert("' . $errorMessage . '");</script>';
    } else if ($errorMessage == null) {
        echo '<script>alert("Reservations updated successfully"); window.location.href = "reserves_main.php";</script>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <style>
        #sid,
        #bid {
            width: 50px;
        }

        table {
            width: 100%;
            text-align: center;
        }

        table {
            width: 100%;
            text-align: center;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        h2,
        div {
            text-align: center;
        }

        .footer {
            background-color: whitesmoke;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            border-top: 1px solid black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }

        .footer button {
            margin-left: 10px;
        }

        .footer p {
            margin: 0;
        }

        .table-container {
            width: 30vw;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
        }

        table {
            margin: 10px;
            border: 2px solid black;
            padding: 10px;
            background-color: #f0f0f0;
        }

        td[rowspan] {
            vertical-align: middle;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
        }

        .center-button {
            display: flex;
            justify-content: center;
        }
    </style>

    <title>Reservations Page</title>
</head>

<body>

    <h1>AOL Database</h1>
    <h2>Revervations Page</h2>

    <br><br>

    <div class="center-button">
        <button id="showFormButton">Add New Reservation</button>


        <form id="reservationForm" action="" method="post" style="display: none;"
            onsubmit="return confirm('Are you sure the information is correct?');">
            <label for="sid">SID:</label>
            <select id="sid" name="sid">
                <?php foreach ($sailors as $sailor): ?>
                    <option value="<?= $sailor['sid']; ?>">
                        <?= $sailor['sid']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="bid">BID:</label>
            <select id="bid" name="bid">
                <?php foreach ($boats as $boat): ?>
                    <option value="<?= $boat['bid']; ?>">
                        <?= $boat['bid']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="date">Date of Reservation:</label>
            <input type="date" id="date" name="date">

            <input type="submit" value="Add Reservation" name="add">
            <input type="button" id="cancelButton" value="Cancel">
        </form>
    </div>

    <br><br>
    <div class="container">
        <?php
        foreach ($reservationsBySid as $sid => $reservations):
            $rowspan = count($reservations);
            ?>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SID</th>
                            <th>BID</th>
                            <th>Date of Reservations</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $first = true;
                        foreach ($reservations as $reserve):
                            if (!$first)
                                echo '<tr>';
                            else
                                echo '<tr><td rowspan="' . $rowspan . '">' . $sid . '</td>';
                            ?>
                            <form action="" method="post" onsubmit="return confirm('Are you sure you want to edit?');">
                                <td>
                                    <input type="hidden" name="old_date" value="<?= $reserve['days']; ?>">
                                    <input type="hidden" name="old_bid" value="<?= $reserve['bid']; ?>">
                                    <select name="bid" id="bidSelect" disabled>
                                        <?php foreach ($bids as $bid): ?>
                                            <option value="<?= $bid; ?>" <?= $bid == $reserve['bid'] ? 'selected' : ''; ?>>
                                                <?= $bid; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <?php
                                    $date = DateTime::createFromFormat('d/m/y', $reserve['days']);
                                    $formattedDate = $date->format('Y-m-d');
                                    ?>
                                    <input type="date" value="<?= $formattedDate; ?>" disabled name="date">
                                </td>
                                <td>
                                    <input type="hidden" name="sid" value="<?= $reserve['sid']; ?>">
                                    <input type="submit" class="edit-button" value="Edit" name="edit">
                                    <input type="submit" class="submit-button" value="Submit" name="submit"
                                        style="display: none;">
                                </td>
                            </form>
                            <td>
                                <input type="button" class="cancel-button" value="Cancel" style="display: none;">
                            </td>
                            <td>
                                <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete?');">
                                    <input type="hidden" name="sid" value="<?= $reserve['sid']; ?>">
                                    <input type="hidden" name="date" value=" <?= $reserve['days']; ?>">
                                    <input type="submit" class="delete-button" value="Delete" name="delete">
                                </form>
                            </td>
                            </tr>
                            <?php
                            $first = false;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        endforeach; ?>
    </div>

    <div style="height: 100px;"></div>

    <div class="footer">
        <button onclick="location.href='data_main.html'">Back</button>
        <p>Created by DB Group 7</p>
        <div></div>
    </div>

    <script>
        document.getElementById('showFormButton').addEventListener('click', function () {
            document.getElementById('reservationForm').style.display = 'block';
            this.style.display = 'none';
        });

        document.getElementById('cancelButton').addEventListener('click', function () {
            document.getElementById('reservationForm').style.display = 'none';
            document.getElementById('showFormButton').style.display = 'block';
        });

        document.querySelectorAll('.edit-button').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                var row = this.closest('tr');
                var inputs = row.querySelectorAll('input[type="date"], select');
                var submitButton = row.querySelector('.submit-button');
                var cancelButton = row.querySelector('.cancel-button');
                var deleteButton = row.querySelector('.delete-button');
                inputs.forEach(function (input) {
                    input.disabled = !input.disabled;
                });
                this.style.display = 'none';
                submitButton.style.display = 'inline';
                cancelButton.style.display = 'inline';
                deleteButton.disabled = true;
            });
        });

        document.querySelectorAll('.cancel-button').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                var row = this.closest('tr');
                var inputs = row.querySelectorAll('input[type="date"], select');
                var editButton = row.querySelector('.edit-button');
                var submitButton = row.querySelector('.submit-button');
                var deleteButton = row.querySelector('.delete-button');
                inputs.forEach(function (input) {
                    input.disabled = !input.disabled;
                });
                this.style.display = 'none';
                submitButton.style.display = 'none';
                editButton.style.display = 'inline';
                deleteButton.disabled = false;
            });
        });

        document.getElementById('editButton').addEventListener('click', function () {
            document.getElementById('bidSelect').disabled = false;
        });
    </script>

</body>

</html>