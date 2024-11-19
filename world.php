<?php
// Database connection parameters
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

try {
    // Establish PDO database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Retrieve country and lookupType from the query parameters
$country = $_GET['country'] ?? null;
$lookupType = $_GET['lookup'] ?? null;

// Check if the country parameter is provided
if ($country === null) {
    // If not, retrieve all countries
    $stmt = $conn->query("SELECT * FROM countries");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Use LIKE operator for partial matching
    $country = "%$country%";

    // If country parameter is provided, check the lookup type
    if ($lookupType === "cities") {
        // Retrieve city information with a JOIN between cities and countries tables
        $stmt = $conn->prepare("SELECT cities.name AS city_name, cities.district, cities.population
                                FROM cities
                                JOIN countries ON cities.country_code = countries.code
                                WHERE countries.name LIKE :country");
        $stmt->bindParam(':country', $country);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Retrieve country information
        $stmt = $conn->prepare("SELECT * FROM countries WHERE name LIKE :country");
        $stmt->bindParam(':country', $country);
        $stmt->execute();
        $countryResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retrieve city information for the specified country
        $stmt = $conn->prepare("SELECT cities.name AS city_name, cities.district, cities.population
                                FROM cities
                                JOIN countries ON cities.country_code = countries.code
                                WHERE countries.name LIKE :country");
        $stmt->bindParam(':country', $country);
        $stmt->execute();
        $cityResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!-- HTML table to display the results -->
<table>
    <?php if (isset($results) && count($results) === 0): ?>
        <tr><td colspan="4">No results found for your search.</td></tr>
    <?php elseif (isset($lookupType) && $lookupType === "cities"): ?>
        <!-- Display city information -->
        <tr>
            <th>City Name</th>
            <th>District</th>
            <th>Population</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['city_name']); ?></td>
                <td><?= htmlspecialchars($row['district']); ?></td>
                <td><?= htmlspecialchars($row['population']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Display country information -->
        <tr>
            <th>Country Name</th>
            <th>Continent</th>
            <th>Independence Year</th>
            <th>Head of State</th>
        </tr>
        <?php foreach ($countryResults as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['continent']); ?></td>
                <td><?= htmlspecialchars($row['independence_year']); ?></td>
                <td><?= htmlspecialchars($row['head_of_state']); ?></td>
            </tr>
        <?php endforeach; ?>
        <!-- Display city information for the specified country -->
        <tr>
            <th colspan="4">Cities</th>
        </tr>
        <tr>
            <th>City Name</th>
            <th>District</th>
            <th>Population</th>
        </tr>
        <?php foreach ($cityResults as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['city_name']); ?></td>
                <td><?= htmlspecialchars($row['district']); ?></td>
                <td><?= htmlspecialchars($row['population']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>