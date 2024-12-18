

<?php
require("rss.php");
// Output RSS feed to HTML
$output = get_rss_feed_as_html('https://baku.ws/rss', 20, true, true, 200);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AZERTAC News Feed</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>AZERTAC News Feed</h1>
    <div class="container">
        <?php echo $output; ?>
    </div>
</body>
</html>
