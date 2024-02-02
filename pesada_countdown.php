<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Insert Data</title>
    <style>
        body {
            font-family: cursive;
        }

        h1 {
            margin-top: 10%;
            text-align: center;
            font-size: 40px;
            padding: 0px;
        }

        p {
            text-align: center;
            font-size: 100px;
            font-weight: bolder;
            padding: 0px;
            margin: 0px;
        }

        /* Add this style for red text */
        .red-text {
            color: red;
        }
    </style>
</head>
<body>

<h1>Auto Upload Data</h1>
<p id="countdown">Next Upload in: 1:00</p>

<script>
    function insertData() {
        // Make an AJAX request to insert data into the database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'pesada_autoupload.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    console.log(response.message);

                    // Update the countdown value
                    let countdown = response.countdown;
                    updateCountdown(countdown);

                    // Set up the countdown timer
                    const countdownInterval = setInterval(function () {
                        countdown--;
                        updateCountdown(countdown);

                        // Change text color to red when there are 10 minutes left
                        if (countdown <= 10) {
                            document.getElementById('countdown').classList.add('red-text');
                        }

                        // Reload the page after the countdown
                        if (countdown <= 0) {
                            clearInterval(countdownInterval); // Stop the interval
                            location.reload();
                        }
                    }, 1000);
                } else {
                    console.error('Error inserting data:', xhr.responseText);
                }
            }
        };

        xhr.send();
    }

    function updateCountdown(countdown) {
        const countdownElement = document.getElementById('countdown');
        const minutes = Math.floor(countdown / 60);
        const seconds = countdown % 60;

        countdownElement.textContent = `Next Upload in: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    }

    // Insert data immediately when the page loads
    insertData();
</script>
</body>
</html>
