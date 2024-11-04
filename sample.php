<?php 
include 'db.php';
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

<?php while ($row = $packagesResult->fetch_assoc()): ?>
        <div class="package-box" 
             onclick="location.href='booking_form.php?id=<?php echo $row['package_id']; ?>&price=<?php echo $row['package_price']; ?>'">
            <div class="package-title"><?php echo htmlspecialchars($row['package_name']); ?></div>
            <div><?php echo nl2br(htmlspecialchars($row['contents'])); ?></div>
            <div class="package-price">₱<?php echo htmlspecialchars($row['package_price']); ?></div>
        </div>
    <?php endwhile; ?>