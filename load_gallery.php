<?php
// Database connection
include 'db.php';

// Fetch media based on the type passed in the URL
$type = isset($_GET['type']) ? $_GET['type'] : 'photo'; // Default to photo
$sql = "SELECT * FROM gallery WHERE type = '$type'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Start the carousel structure
    echo '<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">';
    echo '<ol class="carousel-indicators">';

    // Generate indicators
    for ($i = 0; $i < $result->num_rows; $i++) {
        echo '<li data-target="#carouselExampleIndicators" data-slide-to="' . $i . '" ' . ($i === 0 ? 'class="active"' : '') . '></li>';
    }
    echo '</ol>';
    echo '<div class="carousel-inner">';

    $i = 0;
    while ($row = $result->fetch_assoc()) {
        echo '<div class="carousel-item ' . ($i === 0 ? 'active' : '') . '">'; // Add active class to the first item
        if ($type === 'photo') {
            echo '<img class="d-block carousel-media" src="' . $row["file_path"] . '" alt="' . htmlspecialchars($row["title"]) . '">';
        } else if ($type === 'video') {
            echo '<video class="d-block carousel-media" controls>';
            echo '<source src="' . $row["file_path"] . '" type="video/mp4">';
            echo 'Your browser does not support the video tag.';
            echo '</video>';
        }
        echo '</div>'; // Close carousel item
        $i++;
    }
    echo '</div>'; // Close carousel-inner

    // Carousel controls
    echo '<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">';
    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
    echo '<span class="sr-only">Previous</span>';
    echo '</a>';
    echo '<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">';
    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
    echo '<span class="sr-only">Next</span>';
    echo '</a>';
    echo '</div>'; // Close carousel
} else {
    echo "No items available in the gallery.";
}

$conn->close();
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>