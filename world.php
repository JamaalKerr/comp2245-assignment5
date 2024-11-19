<?php
// Database connection parameters
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

// Establish a PDO database connection
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

// Retrieve country and lookupType from the query parameters
$country = $_GET['country'] ?? null;
$lookupType = $_GET['lookup'] ?? null;

// Check if the country parameter is provided
if ($country === null) {
    // If not, retrieve all countries
    $stmt = $conn->query("SELECT * FROM countries");
} else {
    // If country parameter is provided, check the lookup type
    if ($lookupType === "cities") {
        // If lookup type is "cities," retrieve city information with a JOIN between cities and countries tables
        $stmt = $conn->prepare("SELECT cities.name AS city_name, cities.district, cities.population
                                FROM cities
                                JOIN countries ON cities.country_code = countries.code
                                WHERE countries.name = :country");
    } else {
        // If lookup type is not specified or is not "cities," retrieve country information
        $stmt = $conn->prepare("SELECT * FROM countries WHERE name = :country");
    }

    // Bind the country parameter and execute the query
    $stmt->bindParam(':country', $country);
    $stmt->execute();
}

// Fetch the results as an associative array
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML table to display the results -->
<table>
    <?php if ($lookupType === "cities"): ?>
        <!-- If lookup type is "cities," display city information -->
        <tr>
            <th>City Name</th>
            <th>District</th>
            <th>Population</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= $row['city_name']; ?></td>
                <td><?= $row['district']; ?></td>
                <td><?= $row['population']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- If lookup type is not specified or is not "cities," display country information -->
        <tr>
            <th>Country Name</th>
            <th>Continent</th>
            <th>Independence Year</th>
            <th>Head of State</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= $row['name']; ?></td>
                <td><?= $row['continent']; ?></td>
                <td><?= $row['independence_year']; ?></td>
                <td><?= $row['head_of_state']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>