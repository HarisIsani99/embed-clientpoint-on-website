<!DOCTYPE html>
<head>
<link rel="shortcut icon" href="https://www.clientpoint.net/hubfs/favicon-1.ico">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        body {
            background-image: url('https://img.freepik.com/free-vector/abstract-background-with-monochrome-low-poly-design_1048-14453.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
        .login-form {
            width: 385px;
            margin: 100px auto;
        }
        .login-form form {
            margin-bottom: 15px;
            background: #f7f7f7;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .login-form h2 {
            margin: 0 0 15px;
        }
        .form-control, .login-btn {
            min-height: 38px;
            border-radius: 2px;
        }
        .input-group-addon .fa {
            font-size: 18px;
        }
        .login-btn {
            font-size: 15px;
            font-weight: bold;
        }
        .social-btn .btn {
            border: none;
            margin: 10px 3px 0;
            opacity: 1;
        }
        .social-btn .btn:hover {
            opacity: 0.9;
        }
        .social-btn .btn-primary {
            background: #507cc0;
        }
        .social-btn .btn-info {
            background: #64ccf1;
        }
        .social-btn .btn-danger {
            background: #df4930;
        }
        .or-seperator {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #ccc;
        }
        .or-seperator i {
            padding: 0 10px;
            background: #f7f7f7;
            position: relative;
            top: -11px;
            z-index: 1;
        }

        .logo{
            width:200px;
            height:auto;
            text-align:center;
        }

        .div-logo{
            text-align:center;
            padding:20px
        }

        .iframe-links{
            text-align: right;
        }

        .iframe-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio, adjust as needed */
        }

        iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none; /* Remove border, adjust as needed */
            margin-top: 10px;
        }
        .iframe{
            margin-top: 20px;
        }

        .iframe-label{
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<?php
    /*
        Author: Haris Isani
        Email: haris.isani@clientpoint.net
        Date: September 21, 2023
        Description: This codebase contains code for embedding ClientPoint on your website
    */
    require 'env.php';
    if(empty($api_key) || empty($base_url)){
        echo '<div class="alert alert-danger" role="alert">Please fill out the api key and base URL in the env file</div>';
        die();
    }
?>
<html lang="en">
<body>
<div class="login-form">
    <form id="form">
        <div class="div-logo">
            <img src="https://developer.clientpoint.net/logo2.png" alt="logo" class="logo" >
        </div>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required="required">
            </div>
        </div>
        <div class="form-group"  style="display:none">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="hidden" class="form-control"  id="pin" name="pin" placeholder="Pin" value='<?= $pin ?>'>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-paper-plane"></i></span>
                <input type="number" class="form-control" id="proposalID" name="proposalID" placeholder="Proposal Id" required="required">
            </div>
        </div>
        <div class="form-group">
            <button type="button" onclick="accessClientPoint()" class="btn btn-primary login-btn btn-block">Sign in</button>
        </div>
    </form>
</div>
<div class="container iframe" style="display:none;">
    <div class="iframe-links">
        <a onclick="chooseAnother()" class="btn btn-warning" href="javascript:void()" role="button">Choose Another</a>
        <a id="ClientPointLink" class="btn btn-primary" target="_blank" href="" role="button">Open in a new tab</a>
        <div class="iframe-label">
                <div class="alert alert-warning" role="alert">If iframe fails to load for any reason, contact support@clientpoint.net or use open in a new tab</div>
        </div>
        <div class="iframe-container">
            <iframe id="ClientPointFrame" src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>
</body>
<script>

/**
 * Hides all elements with the class 'iframe' and shows elements with the class 'login-form'.
 */
function chooseAnother() {
    $('.iframe').hide();
    $('.login-form').show();
}


/**
 * Accesses the client point by retrieving a PIN and opening the ClientPoint Frame with the provided URL.
 */
function accessClientPoint() {
    // Get the email and proposal ID from input fields
    var email = $('#email').val().trim();
    var newEmail = encodeURIComponent(email);
    var proposalId = $('#proposalID').val();

    // Check if both email and proposal ID are provided
    if (email !== '' && proposalId !== '') {
        // Validate the email format
        if (!validateEmail(email)) {
            alert('Email is not valid!');
            return;
        }

        // Use async/await to wait for the PIN value from fetchPin
        fetchPin(proposalId)
            .then(pin => {
                if (pin !== '') {
                    // Construct the URL for accessing the client point
                    var url = "<?=$base_url?>/proposal/unlock-view/proposalId/" + proposalId + "/pin/" + pin + "/email/" + newEmail;

                    // Open the ClientPointFrame by setting the src attribute
                    $("#ClientPointFrame").attr("src", url);
                    
                    // Set the ClientPointLink href attribute (optional)
                    $("#ClientPointLink").attr("href", url);
                    
                    // Show the iframe and hide the login form
                    $('.iframe').show();
                    $('.login-form').hide();
                } else {
                    alert('Failed to get PIN.');
                }
            })
            .catch(error => {
                alert('Error fetching PIN: ' + error);
            });

    } else {
        alert('Please fill required fields!');
    }
}



/**
 * Fetches a PIN for a given proposal ID by making a POST request to the server.
 *
 * @param {string} pId - The proposal ID for which to fetch the PIN.
 * @returns {Promise<string>} - A Promise that resolves with the PIN value on success, or rejects with an error on failure.
 */
function fetchPin(pId) {
    // Prepare the data to be sent in the request body
    var requestData = {
        pId: pId,
    };

    // Configure the request options, including headers and body
    var requestOptions = {
        method: 'POST',
        headers: new Headers({
            "Content-Type": "application/json",
        }),
        body: JSON.stringify(requestData),
        redirect: 'follow'
    };

    // Create a Promise to handle the asynchronous operation
    return new Promise((resolve, reject) => {
        // Send the POST request to the server
        fetch("api.php", requestOptions)
            .then(response => response.text())
            .then(result => {
                // Parse the JSON response to extract the PIN value
                var res = JSON.parse(result);
                var pin = res.pin;
                
                // Resolve the Promise with the PIN value
                resolve(pin);
            })
            .catch(error => {
                // Log and reject the Promise with the error
                console.log('error', error);
                reject(error);
            });
    });
}

/**
 * Validates an email address using a regular expression.
 *
 * @param {string} email - The email address to validate.
 * @returns {boolean} - True if the email is valid; otherwise, false.
 */
function validateEmail(email) {
    // Regular expression pattern for validating email addresses
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    // Test the email against the regular expression and return the result
    return re.test(String(email).toLowerCase());
}

  
</script>
</html>