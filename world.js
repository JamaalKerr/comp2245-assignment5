// Wait for the DOM content to be fully loaded before executing the script
console.log("Script loaded");

document.addEventListener("DOMContentLoaded", function () {

    // Get references to HTML elements
    var lookupButton = document.getElementById("lookup");
    var lookupCitiesButton = document.getElementById("lookupCities");
    var result = document.getElementById("result");
    var searchValue = document.getElementById("country");  // Ensure the correct ID is used

    // Declare the XMLHttpRequest object
    var Req;

    // Function to make an XMLHttpRequest based on the lookup type (country or cities)
    function makeRequest(lookupType) {
        // Initialize the XMLHttpRequest object
        Req = new XMLHttpRequest();

        // Get the value of the country input
        var country = searchValue.value;

        // Check if country value is empty
        if (!country) {
            alert('Please enter a country name.');
            return;
        }

        // Construct the URL for the Ajax request, including the country and lookup parameters
        var url = "http://localhost/comp2245-assignment5/world.php?country=" + encodeURIComponent(country) + "&lookup=" + lookupType;

        console.log("Request URL:", url);  // Log the URL for debugging

        // Open a GET request to the specified URL
        Req.open('GET', url);
        // Send the Ajax request
        Req.send();
        // Set up a callback function to handle the response
        Req.onreadystatechange = respond;
    }

    // Event listener for the "Lookup" button
    lookupButton.addEventListener("click", function () {
        // Make a request for country information
        makeRequest("country");
    });

    // Event listener for the "Lookup Cities" button
    if (lookupCitiesButton) {
        lookupCitiesButton.addEventListener("click", function () {
            // Make a request for city information
            makeRequest("cities");
        });
    }

    // Callback function to handle the Ajax response
    function respond() {
        // Check if the request has completed
        if (Req.readyState === XMLHttpRequest.DONE) {
            // Check if the server responded with a 200 status (OK)
            if (Req.status === 200) {
                // Update the content of the "result" div with the server's response
                result.innerHTML = Req.responseText;
            } else {
                // Alert the user if there was an error
                alert('Error: ' + Req.status);
            }
        }
    }
});
