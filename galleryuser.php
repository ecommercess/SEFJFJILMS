<?php
include 'db.php';


// Get the current page number and type from the URL
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$type = isset($_GET['type']) ? $_GET['type'] : 'photo'; // Default to 'photo'
$limit = 6; // Number of items per page
$offset = ($page - 1) * $limit; // Calculate the offset

// Build the SQL query based on the selected type
$typeCondition = ($type === 'photo') ? "AND type = 'photo'" : (($type === 'video') ? "AND type = 'video'" : "");
$totalSql = "SELECT COUNT(*) as total FROM gallery WHERE type IN ('photo', 'video') $typeCondition";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit); // Calculate total pages

// Fetch items for the current page
$sql = "SELECT * FROM gallery WHERE type IN ('photo', 'video') $typeCondition LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$response = [
    'totalPages' => $totalPages,
    'items' => []
];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $response['items'][] = $row;
    }
} else {
    $response['items'] = [];
}

$conn->close();

// Send JSON response for AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Regular HTML rendering if not an AJAX request
?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
    <link href="https://cdn.plyr.io/3.6.8/plyr.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.plyr.io/3.6.8/plyr.polyfilled.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
      
        .filter-icons {
            margin-bottom: 20px;
            text-align: center;
        }
        .filter-icons a {
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
            color: #007BFF;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 100px;
           
        }
        .gallery-item {
            width: 100%;
            height: auto;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            border: 1px solid #007BFF;
            border-radius: 5px;
            text-decoration: none;
            color: #007BFF;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination a:hover {
            background-color: #007BFF;
            color: #fff;
        }
        .pagination .active {
            background-color: #007BFF;
            color: #fff;
            pointer-events: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; // Include the navbar here ?>
  
    <div class="gallery" id="gallery"></div>
    <div class="filter-icons">
        <a href="#" onclick="loadGallery(1, 'photo'); return false;">📷 Photos</a>
        <a href="#" onclick="loadGallery(1, 'video'); return false;">🎥 Videos</a>
    </div>
    <div class="pagination" id="pagination"></div>

    <script>
        let currentPage = <?php echo $page; ?>;
        let currentType = '<?php echo $type; ?>';
        let totalPages = <?php echo $totalPages; ?>;

        function loadGallery(page, type) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?page=' + page + '&type=' + type + '&ajax=1', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    const gallery = document.getElementById('gallery');
                    const pagination = document.getElementById('pagination');

                    // Update the totalPages dynamically based on response
                    totalPages = response.totalPages;

                    gallery.innerHTML = '';
                    response.items.forEach(item => {
                        let itemHtml;
                        if (item.type === 'photo') {
                            itemHtml = `<div class='gallery-item'>
                                            <a href='${item.file_path}' data-lightbox='gallery' data-title='${item.title}'>
                                                <img src='${item.file_path}' alt='${item.title}' style='width: 100%; height: auto; object-fit: cover;'/>
                                            </a>
                                        </div>`;
                        } else if (item.type === 'video') {
                            itemHtml = `<div class='gallery-item'>
                                            <video class="plyr__video-embed" controls>
                                                <source src='${item.file_path}' type='video/mp4'>
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>`;
                        }
                        gallery.innerHTML += itemHtml;
                    });

                    // Build pagination dynamically
                    pagination.innerHTML = '';
                    if (totalPages > 0) {
                        if (page > 1) {
                            pagination.innerHTML += `<a href="#" onclick="loadGallery(${page - 1}, '${type}'); return false;">Previous</a>`;
                        }
                        for (let i = 1; i <= totalPages; i++) {
                            pagination.innerHTML += i === page ?
                                `<span class="active">${i}</span>` :
                                `<a href="#" onclick="loadGallery(${i}, '${type}'); return false;">${i}</a>`;
                        }
                        if (page < totalPages) {
                            pagination.innerHTML += `<a href="#" onclick="loadGallery(${page + 1}, '${type}'); return false;">Next</a>`;
                        }
                    }

                    // Initialize Plyr for videos
                    Array.from(document.querySelectorAll('.plyr__video-embed')).forEach(p => new Plyr(p));
                }
            };
            xhr.send();
            currentPage = page;
            currentType = type;
        }

        loadGallery(currentPage, currentType);
    </script>
</body>
</html>
