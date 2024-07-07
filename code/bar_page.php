<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Page</title>
    <!-- Add your CSS stylesheets here -->
</head>
<body>
    <header>
        <h1>Bar Details</h1>
    </header>

    <main>
        <div class="bar-details">
            <?php
            // Retrieve the bar details from the database based on the bar_id parameter
            $bar_id = $_GET['bar_id']; // Assuming 'bar_id' is the parameter name
            // Fetch bar details from the database using $bar_id and display them here
            // For example:
            $bar_name = "Sample Bar";
            $bar_description = "This is a sample bar description.";
            $bar_location = "Sample Location";
            $bar_contact = "123-456-7890";
            $bar_avg_score = 4.5;
            $bar_num_reviews = 100;
            ?>
            <h2><?php echo $bar_name; ?></h2>
            <p>Description: <?php echo $bar_description; ?></p>
            <p>Location: <?php echo $bar_location; ?></p>
            <p>Contact: <?php echo $bar_contact; ?></p>
            <p>Average Score: <?php echo $bar_avg_score; ?></p>
            <p>Number of Reviews: <?php echo $bar_num_reviews; ?></p>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Bar Name</p>
    </footer>
</body>
</html>
