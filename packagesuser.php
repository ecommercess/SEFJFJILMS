<?php 
include 'db.php';
include 'navbar.php';

$packagesQuery = "
    SELECT p.id AS package_id, p.package_name, p.package_price, p.package_type, 
    GROUP_CONCAT(c.content SEPARATOR '\n') AS contents
    FROM packages p
    LEFT JOIN package_contents c ON p.id = c.package_id
    GROUP BY p.id";
$packagesResult = $conn->query($packagesQuery);

if (!$packagesResult) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Boxes</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            
            height: 100vh;
            margin: 0;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px; /* Space between packages */
        }

        .package {
            box-sizing: border-box;
            width: 300px;
            height: auto;
            border: 3px solid #e8e8e8;
            border-radius: 7px;
            padding: 24px;
            text-align: center;
            transition: margin-top .5s linear;
            position: relative;
            margin-top: 80px;
            cursor: pointer;
        }

        .package:hover {
            margin-top: -30px;
            transition: margin-top .3s linear;
        }

        .name {
            color: #565656;
            font-weight: 300;
            font-size: 3rem;
            margin-top: -5px;
        }

        .price {
            margin-top: 7px;
            font-weight: bold;
            font-size: 2rem;
        }

        .price::after {
            font-weight: normal;
        }

        hr {
            background-color: #dedede;
            border: none;
            height: 1px;
            margin: 20px 0; /* Margin for spacing */
        }

        .trial {
            font-size: .7rem;
            font-weight: 600;
            padding: 2px 21px;
            color: #33c4b6; /* Accent color */
            border: 1px solid #e4e4e4;
            display: inline-block;
            border-radius: 15px;
            background-color: white;
            position: relative;
            bottom: -20px;
        }

        ul {
            list-style: none;
            padding: 0;
            text-align: left;
            margin-top: 29px;
        }

        li {
            margin-bottom: 5px;

        }

        .checkIcon {
            font-family: "FontAwesome";
            content: "\f00c";
            color: #33c4b6; /* Accent color */
            margin-right: 3px;
        }

        .brilliant {
            border-color: #33c4b6; /* Accent color */
        }

        .brilliant::before {
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 64px 64px 0 0;
            border-color: #3bc6b8 transparent transparent transparent;
            position: absolute;
            left: 0;
            top: 0;
            content: "";
        }

        .brilliant::after {
            content: "\f00c"; /* FontAwesome check icon */
            color: white;
            position: absolute;
            left: 9px;
            top: 6px;
            font-size: 1.2rem;
            text-shadow: 0 0 2px #37c5b6;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php while ($row = $packagesResult->fetch_assoc()): ?>
        <div class="package <?php echo ($row['package_type'] === 'brilliant') ? 'brilliant' : ''; ?>" 
             onclick="location.href='booking_form.php?id=<?php echo $row['package_id']; ?>&price=<?php echo $row['package_price']; ?>'">
            <div class="name"><?php echo htmlspecialchars($row['package_name']); ?></div>
            <div class="price">₱<?php echo htmlspecialchars($row['package_price']); ?></div>
            <hr>
            <ul>
                <?php
                $contents = explode("\n", $row['contents']);
                foreach ($contents as $content): ?>
                    <li><?php echo htmlspecialchars($content); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
