<?php

session_start();
$conn = "";
$stmt = "";

function connectToDB() // function untuk konek ke database
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbName = "aol_dbms"; // ini ganti ke database yang ada di sql masing2
    $dataSourceName = "mysql:host=" . $servername . ";dbname=" . $dbName;
    try {
        $conn = new PDO($dataSourceName, $username, $password); // buat koneksi
        return $conn; // return koneksi
    } catch (PDOException $e) { // saat error
        echo $e->getMessage();
        return null;
    }
}

function closeConnection() // function buat nutup koneksi ke database
{
    $conn = null;
    $stmt = null;
}

connectToDB();

function getAllSailors() // function dapetin semua data dari table sailors
{
    $conn = connectToDB(); // konek dulu database
    $stmt = $conn->query("SELECT * FROM sailors"); // syntax sqlnya
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetch buat narik semua data
    closeConnection(); // nutup koneksi
    return $result; // return hasil dari yang udah didapatkan
}

function getAllBoats() // function dapetin semua data dari table boats
{
    $conn = connectToDB();
    $stmt = $conn->query("SELECT * FROM boats");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    closeConnection();
    return $result;
}

function getAllReserves() // function dapetin semua data dari table reserves
{
    $conn = connectToDB();
    $stmt = $conn->query("SELECT * FROM reserves");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    closeConnection();
    return $result;
}

function countAllSailors() // function buat count sailor
{
    $conn = connectToDB();
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sailors"); // ini pake alias biar bisa dipanggil
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    closeConnection();
    return $result['total']; // return cuman aliasnya / totalnya aja
}

function countAllBoats() // function buat count boat
{
    $conn = connectToDB();
    $stmt = $conn->query("SELECT COUNT(*) as total FROM boats");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    closeConnection();
    return $result['total'];
}

function getAverageSailorRating() // function buat dapetin average rating sailor
{
    $conn = connectToDB();
    $stmt = $conn->query("SELECT AVG(rating) as averageRating FROM sailors"); // pake alias juga
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    closeConnection();
    $averageRating = $result['averageRating'];
    return number_format($averageRating, 2); // ini buat formatingnya, tpi sebenernya di html atau di page nya bisa di format juga
}

function getAverageSailorAge() // function buat dapetin average age sailor
{
    $conn = connectToDB();
    $stmt = $conn->query("SELECT AVG(age) as averageAge FROM sailors");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    closeConnection();
    $averageAge = $result['averageAge'];
    return number_format($averageAge, 2);
}

function createSailors($data) // function buat create sailor
{
    $conn = connectToDB(); // konek database

    try {
        $stmt = $conn->prepare("SELECT * FROM sailors WHERE sid = ?"); // select apakah ada sid yang sama
        $stmt->execute([$data["sid"]]);
        $sailor = $stmt->fetch(); // dapetin datanya

        if ($sailor) { // kalau ada brarti eror
            throw new Exception("Error: Sailor with the following SID already exists.");
        }

        $stmt = $conn->prepare("INSERT INTO sailors (sid, sname, rating, age) VALUES (?, ?, ?, ?)"); // ga ada sailor dengan sid yang sama maka insert
        $stmt->execute([
            $data["sid"],
            $data["name"],
            $data["rating"],
            $data["age"],
        ]);

        closeConnection();
        return null;
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function createBoats($data) // function buat create boat
{
    $conn = connectToDB(); // intinya sama kaya create sailor

    try {
        $stmt = $conn->prepare("SELECT * FROM boats WHERE bid = ?");
        $stmt->execute([$data["bid"]]);
        $boat = $stmt->fetch();

        if ($boat) {
            throw new Exception("Error: Boat with the following BID already exists.");
        }

        $stmt = $conn->prepare("INSERT INTO boats (bid, bname, color) VALUES (?, ?, ?)");
        $stmt->execute([
            $data["bid"],
            $data["name"],
            $data["color"],
        ]);

        closeConnection();
        return null;
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}
// {
//     $conn = connectToDB();

//     try {
//         $stmt = $conn->prepare("SELECT * FROM reserves WHERE days = ?");
//         $stmt->execute([$data["date"]]);
//         if ($stmt->rowCount() > 0) {
//             $stmt = $conn->prepare("SELECT * FROM reserves WHERE sid = ? AND days = ?");
//             $stmt->execute([$data["sid"], $data["date"]]);
//             if($stmt->rowCount() > 0){
//                 closeConnection();
//                 throw new Exception("Error: The same SID has already reserved a boat on this date.");
//             }

//             $stmt = $conn->prepare("SELECT * FROM reserves WHERE bid = ? AND days = ?");
//             $stmt->execute([$data["bid"], $data["date"]]);
//             if($stmt->rowCount() > 0){
//                 closeConnection();
//                 throw new Exception("Error: The boat has already been reserved on this date.");
//             }
//         }

//         $stmt = $conn->prepare("INSERT INTO reserves (sid, bid, days) VALUES (?, ?, ?)");
//         $stmt->execute([
//             $data["sid"],
//             $data["bid"],
//             $data["date"],
//         ]);

//         closeConnection();
//         return null; 

//     } catch (Exception $e) {
//         closeConnection();
//         return $e->getMessage();
//     }
// }

function createReservations($data) // function buat create reservation
{
    $conn = connectToDB(); // sama cuman beda dikit parameternya

    try {
        $date = DateTime::createFromFormat('Y-m-d', $data["date"]);
        if (!$date) {
            throw new Exception('Invalid date format. Expected format is Y-m-d.');
        }
        $formattedDate = $date->format('d/m/y'); // formating tanggalnya

        $stmt = $conn->prepare("SELECT * FROM reserves WHERE days = ?"); // cek dulu harinya
        $stmt->execute([$formattedDate]);
        if ($stmt->rowCount() > 0) { // kalau ada yg tanggalnya sama 
            $stmt = $conn->prepare("SELECT * FROM reserves WHERE sid = ? AND days = ?"); // cek sidnya
            $stmt->execute([$data["sid"], $formattedDate]);
            if ($stmt->rowCount() > 0) { // brarti udah ada yg reserve jadi eror
                closeConnection();
                throw new Exception("Error: The same SID has already reserved a boat on this date.");
            }

            $stmt = $conn->prepare("SELECT * FROM reserves WHERE bid = ? AND days = ?"); // ini cek bid di hari yang sama
            $stmt->execute([$data["bid"], $formattedDate]);
            if ($stmt->rowCount() > 0) {
                closeConnection();
                throw new Exception("Error: The boat has already been reserved on this date.");
            }
        }

        $stmt = $conn->prepare("INSERT INTO reserves (sid, bid, days) VALUES (?, ?, ?)"); // klo ga ada tinggal insert
        $stmt->execute([
            $data["sid"],
            $data["bid"],
            $formattedDate,
        ]);

        closeConnection();
        return null;
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function deleteSailors($data) // function buat delete sailor
{
    $conn = connectToDB(); // sama cuman beda jadi delete

    try {
        $stmt = $conn->prepare("SELECT * FROM sailors WHERE sid = ?");
        $stmt->execute([$data["sid"]]);
        $sailor = $stmt->fetch();

        if ($sailor) {
            $stmt = $conn->prepare("DELETE FROM sailors WHERE sid = ?");
            $stmt->execute([
                $data["sid"],
            ]);

            closeConnection();
            return null;
        } else {
            throw new Exception("Error: Sailor with the following id does not exist.");
        }
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function deleteBoats($data) // function buat delete boat
{
    $conn = connectToDB();

    try {
        $stmt = $conn->prepare("SELECT * FROM boats WHERE bid = ?");
        $stmt->execute([$data["bid"]]);
        $boat = $stmt->fetch();

        if ($boat) {
            $stmt = $conn->prepare("DELETE FROM boats WHERE bid = ?");
            $stmt->execute([
                $data["bid"],
            ]);

            closeConnection();
            return null;
        } else {
            throw new Exception("Error: Boat with the following id does not exist.");
        }
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function deleteReservations($data) // function buat delete reservation
{
    $conn = connectToDB();
    try {
        $trimmedDate = trim($data["date"]);
        $stmt = $conn->prepare("SELECT * FROM reserves WHERE sid = ? and days = ?");
        $stmt->execute([$data["sid"], $trimmedDate]);
        $result = $stmt->fetch();

        if ($result) {
            $stmt = $conn->prepare("DELETE FROM reserves WHERE sid = ? and days = ?");
            $stmt->execute([
                $data["sid"],
                $trimmedDate,
            ]);

            closeConnection();
            return null;
        } else {
            throw new Exception("Error: Reservation with the following option does not exist.");
        }

    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function updateSailors($data) // function buat update sailor
{
    $conn = connectToDB(); // tinggal di update aja

    try {
        $stmt = $conn->prepare("SELECT * FROM sailors WHERE sid = ?");
        $stmt->execute([$data["sid"]]);
        $sailors = $stmt->fetch();

        if ($sailors) {
            $stmt = $conn->prepare("UPDATE sailors SET sname = ?, rating = ?, age = ? WHERE sid = ?");
            $stmt->execute([
                $data["sname"],
                $data["rating"],
                $data["age"],
                $data["sid"],
            ]);

            closeConnection();
            return null;
        } else {
            throw new Exception("Error: Sailor with the following id does not exist.");
        }
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function updateBoats($data) // function buat update boat
{
    $conn = connectToDB();

    try {
        $stmt = $conn->prepare("SELECT * FROM boats WHERE bid = ?");
        $stmt->execute([$data["bid"]]);
        $boat = $stmt->fetch();

        if ($boat) {
            $stmt = $conn->prepare("UPDATE boats SET bname = ?, color = ? WHERE bid = ?");
            $stmt->execute([
                $data["name"],
                $data["color"],
                $data["bid"],
            ]);

            closeConnection();
            return null;
        } else {
            throw new Exception("Error: Boat with the following id does not exist.");
        }
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}

function updateReservations($data) // function buat update reservation
{
    $conn = connectToDB(); // ini agak beda

    try {
        $date = DateTime::createFromFormat('Y-m-d', $data["date"]);
        if (!$date) {
            throw new Exception('Invalid date format. Expected format is Y-m-d.');
        }
        $formattedDate = $date->format('d/m/y'); // formating

        $stmt = $conn->prepare("SELECT * FROM reserves WHERE sid = ? AND days = ? AND NOT (bid = ? AND days = ?)");
        // ini cek dulu si sailor pernah reserve di tanggal yang baru atau engga, nah misal si sailornya cuman mau ganti hari aja
        // maka harus ditambahin parameter yang lama, jadi bidnya gapapa sama tapi hari yang lama harus beda
        $stmt->execute([$data["sid"], $formattedDate, $data["old_bid"], $data["old_date"]]);
        $sailor = $stmt->fetch();

        if ($sailor) {
            throw new Exception("Error: This SID has already reserved a boat on this date.");
        }

        $stmt = $conn->prepare("SELECT * FROM reserves WHERE bid = ? AND days = ? AND NOT (sid = ? AND days = ?)");
        // kalau ini cek ada sid lain ga yang reserve boat yang sama di hari yang sama, kalau sid lain ada yg reserve brarti eror, kalau ga ada ya gapapa
        $stmt->execute([$data["bid"], $formattedDate, $data["sid"], $data["old_date"]]);
        $boat = $stmt->fetch();

        if ($boat) {
            throw new Exception("Error: This boat has already been reserved on this date.");
        }

        $stmt = $conn->prepare("UPDATE reserves SET bid = ?, days = ? WHERE sid = ? AND bid = ? AND days = ?");
        $stmt->execute([
            $data["bid"],
            $formattedDate,
            $data["sid"],
            $data["old_bid"],
            $data["old_date"],
        ]);

        closeConnection();
        return null;
    } catch (Exception $e) {
        closeConnection();
        return $e->getMessage();
    }
}