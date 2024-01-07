<?php
require_once "../database/database.php";

$sailors = getAllSailors();

if (isset($_POST["delete"])) {
    $errorMessage = deleteSailors($_POST);

    if ($errorMessage !== null) {
        echo '<script>alert("' . $errorMessage . '");</script>';
    } else if ($errorMessage == null) {
        echo '<script>alert("Sailor deleted successfully"); window.location.href = "sailors_main.php";</script>';
    }
}

if (isset($_POST["add"])) {
    $errorMessage = createSailors($_POST);

    if ($errorMessage !== null) {
        echo '<script>alert("' . $errorMessage . '");</script>';
    } else if ($errorMessage == null) {
        echo '<script>alert("Sailor added successfully"); window.location.href = "sailors_main.php";</script>';
    }
}

if (isset($_POST["submit"])) {
    $errorMessage = updateSailors($_POST);

    if ($errorMessage !== null) {
        echo '<script>alert("' . $errorMessage . '");</script>';
    } else if ($errorMessage == null) {
        echo '<script>alert("Sailor updated successfully"); window.location.href = "sailors_main.php";</script>';
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <style>
        #sid {
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
    </style>

    <title>Sailors Page</title>
</head>

<body>

    <h1>AOL Database</h1>
    <h2>Sailors Page</h2>

    <br><br>

    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">SID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Rating</th>
                    <th scope="col">Age</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php foreach ($sailors as $sailor): ?>
                    <tr>
                        <form action="" method="post" class="sailor-form"
                            onsubmit="return confirm('Are you sure you want to edit?');">
                            <td>
                                <?= $sailor['sid']; ?>
                                <input type="hidden" name="sid" value="<?= $sailor['sid']; ?>">
                            </td>
                            <td>
                                <input type="text" name="sname" class="editable-input" value="<?= $sailor['sname'] ?>"
                                    disabled>
                            </td>
                            <td>
                                <input type="number" name="rating" class="editable-input" value="<?= $sailor['rating'] ?>"
                                    disabled>
                            </td>
                            <td>
                                <input type="number" name="age" class="editable-input" value="<?= $sailor['age'] ?>"
                                    disabled>
                            </td>
                            <td>
                                <input type="submit" class="edit-button" value="Edit" name="edit">
                                <input type="submit" class="submit-button" value="Submit" name="submit"
                                    style="display: none;">
                            </td>
                            <td>
                                <input type="button" class="cancel-button" value="Cancel" style="display: none;">
                            </td>
                        </form>
                        <td>
                            <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete?');">
                                <input type="hidden" name="sid" value="<?= $sailor['sid']; ?>">
                                <input type="submit" class="delete-button" value="Delete" name="delete">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr id="form_add">
                    <td colspan="7">
                        <button>Add New Sailor</button>
                    </td>
                </tr>
                <tr id="sailor-form" style="display: none">
                    <form action="" method="post"
                        onsubmit="return confirm('Are you sure the information is correct?');">
                        <td><input type="text" id="sid" name="sid" placeholder="SID" required></td>
                        <td><input type="text" id="name" name="name" placeholder="Name" required></td>
                        <td><input type="number" id="rating" name="rating" placeholder="Rating" required></td>
                        <td><input type="number" id="age" name="age" placeholder="Age" required></td>
                        <td>
                            <input type="submit" value="Submit" name="add">
                        </td>
                        <td></td>
                        <td>
                            <input type="button" id="cancel" value="Cancel">
                        </td>
                    </form>
                </tr>
            </tfoot>
        </table>
    </div>

    <div style="height: 100px;"></div>

    <div class="footer">
        <button onclick="location.href='data_main.html'">Back</button>
        <p>Created by DB Group 7</p>
        <div></div>
    </div>

    <script>

        window.onload = function () {
            document.getElementById("form_add").onclick = function () {
                this.style.display = 'none';
                document.getElementById("sailor-form").style.display = "table-row";
            }
            document.getElementById("cancel").onclick = function () {
                document.getElementById("sailor-form").style.display = "none";
                document.getElementById("form_add").style.display = "table-row";
            }
        }

        document.querySelectorAll('.edit-button').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                var form = this.parentElement.parentElement;
                var inputs = form.querySelectorAll('.editable-input');
                var submitButton = form.querySelector('.submit-button');
                var cancelButton = form.querySelector('.cancel-button');
                var deleteButton = this.parentElement.nextElementSibling.querySelector('.delete-button');
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
                var form = this.parentElement.parentElement;
                var inputs = form.querySelectorAll('.editable-input');
                var editButton = form.querySelector('.edit-button');
                var submitButton = form.querySelector('.submit-button');
                var deleteButton = this.parentElement.nextElementSibling.querySelector('.delete-button');
                inputs.forEach(function (input) {
                    input.disabled = !input.disabled;
                });
                this.style.display = 'none';
                submitButton.style.display = 'none';
                editButton.style.display = 'inline';
                deleteButton.disabled = false;
            });
        });
    </script>

</body>

</html>