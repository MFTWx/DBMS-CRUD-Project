<?php
require_once "../database/database.php";

$totalSailors = countAllSailors();
$totalBoats = countAllBoats();
$avgRating = getAverageSailorRating();
$avgAge = getAverageSailorAge();

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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 30vh;
            width: 30%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-sizing: border-box;
            padding: 20px;
            border: 1px solid black;
            border-radius: 5px;
            list-style: none;
            background-color: #f0f0f0;
        }

        table {
            margin: 0 auto;
        }
    </style>

    <title>Report Page</title>
</head>

<body>

    <h1>AOL Database</h1>
    <h2>Report Page</h2>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Sailors Total</th>
                    <th>Boats Total</th>
                    <th>Sailors Average Rating</th>
                    <th>Sailors Average Age</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <tr>
                    <td>
                        <?= $totalSailors ?>
                    </td>
                    <td>
                        <?= $totalBoats ?>
                    </td>
                    <td>
                        <?= $avgRating ?>
                    </td>
                    <td>
                        <?= $avgAge ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <button onclick="location.href='../index.html'">Back</button>
        <p>Created by DB Group 7</p>
        <div></div>
    </div>


</body>

</html>