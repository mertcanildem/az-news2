<?php
session_start(); // Start the session to access session variables
require 'config.php';
require 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    /* Replicate the existing CSS from login.php */
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:"poppins",sans-serif;
    }
    body{
        background-color: #c9d6ff;
        background:linear-gradient(to right,#e2e2e2,#c9d6ff);
    }
    .container{
        background:#fff;
        width:450px;
        padding:1.5rem;
        margin:50px auto;
        border-radius:10px;
        box-shadow:0 20px 35px rgba(0,0,1,0.9);
    }
    form{
        margin:0 2rem;
    }
    .form-title{
        font-size:1.5rem;
        font-weight:bold;
        text-align:center;
        padding:1.3rem;
        margin-bottom:0.4rem;
    }
    input{
        color:inherit;
        width:100%;
        background-color:transparent;
        border:none;
        border-bottom:1px solid #757575;
        padding-left:1.5rem;
        font-size:15px;
    }
    .input-group{
        padding:1% 0;
        position:relative;
    }
    .input-group i{
        position:absolute;
        color:black;
    }
    input:focus{
        background-color: transparent;
        outline:transparent;
        border-bottom:2px solid hsl(327,90%,28%);
    }
    input::placeholder{
        color:transparent;
    }
    label{
        color:#757575;
        position:relative;
        left:1.2em;
        top:-1.3em;
        cursor:auto;
        transition:0.3s ease all;
    }
    input:focus~label,input:not(:placeholder-shown)~label{
        top:-3em;
        color:hsl(327,90%,28%);
        font-size:15px;
    }
    .recover{
        text-align:right;
        font-size:1rem;
        margin-bottom:1rem;
    }
    .recover a{
        text-decoration:none;
        color:rgb(125,125,235);
    }
    .recover a:hover{
        color:blue;
        text-decoration:underline;
    }
    .btn{
        font-size:1.1rem;
        padding:8px 0;
        border-radius:5px;
        outline:none;
        border:none;
        width:100%;
        background:rgb(125,125,235);
        color:white;
        cursor:pointer;
        transition:0.9s;
    }
    .btn:hover{
        background:#07001f;
    }
    .buttons{
        display:flex;
        flex-direction: column;
        gap: 10px;
    }
    .links{
        display:flex;
        justify-content:space-around;
        padding:0 4rem;
        margin-top:0.9rem;
        font-weight:bold;
    }
    button{
        color:rgb(125,125,235);
        border:none;
        background-color:transparent;
        font-size:1rem;
        font-weight:bold;
    }
    button:hover{
        text-decoration:underline;
        color:blue;
    }
    </style>
</head>
<body>
<div id="messageContainer" style="color: red; text-align: center; position: absolute; width: 100%; top: 20px;"></div>

    <div class="container" id="forgetPassword">
        <h1 class="form-title">Forgot Password</h1>
        <form id="forgotPasswordForm" method="post" action="register.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="forgotEmail" id="forgotEmail" placeholder="Email" required>
                <label for="forgotEmail">Email</label>
            </div>
            <div class="input-group" id="codeInput" style="display:none;">
                <i class="fas fa-key"></i>
                <input type="text" name="verificationCode" id="verificationCode" placeholder="Verification Code" required>
                <label for="verificationCode">Verification Code</label>
            </div>
            <div class="input-group" id="newPasswordInput" style="display:none;">
                <i class="fas fa-lock"></i>
                <input type="password" name="newPassword" id="newPassword" placeholder="New Password" required>
                <label for="newPassword">New Password</label>
            </div>
            <div class="buttons">
                <button type="button" id="sendCodeButton" class="btn">Send Verification Code</button>
                <button type="button" id="verifyCodeButton" class="btn" style="display:none;">Verify Code</button>
                <button type="submit" name="resetPassword" class="btn" style="display:none;">Reset Password</button>
            </div>
        </form>

        <div class="links">
            <p>Remember your password?</p>
            <a href="sign_in.php"><button>Sign In</button></a>
        </div>
    </div>
    
    <script>
    // Include necessary JavaScript from login.php
    const sendCodeButton = document.getElementById('sendCodeButton');
    const verifyCodeButton = document.getElementById('verifyCodeButton');
    const resetPasswordButton = document.querySelector('button[name="resetPassword"]');
    const messageContainer = document.getElementById('messageContainer');
    const errorMessage = document.getElementById('errorMessage');

    function hideErrorMessage() {
        if(errorMessage) {
            errorMessage.style.display = 'none';
        }
    }

    sendCodeButton.addEventListener('click', function(){
        const email = document.getElementById('forgotEmail').value;
        
        if(email){
            const formData = new FormData();
            formData.append('action', 'sendCode');
            formData.append('email', email);

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                messageContainer.textContent = data;
                messageContainer.style.color = data.includes('Failed') || data.includes('not found') ? 'red' : 'green';
                
                if(data === 'Verification code sent to your email.') {
                    document.getElementById('codeInput').style.display = "block";
                    verifyCodeButton.style.display = "block";
                    sendCodeButton.style.display = "none";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageContainer.textContent = 'An error occurred. Please try again.';
                messageContainer.style.color = 'red';
            });
        }
    });

    verifyCodeButton.addEventListener('click', function(){
        const email = document.getElementById('forgotEmail').value;
        const code = document.getElementById('verificationCode').value;
        
        if(email && code){
            const formData = new FormData();
            formData.append('action', 'verifyCode');
            formData.append('email', email);
            formData.append('code', code);

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                messageContainer.textContent = data;
                messageContainer.style.color = data.includes('Invalid') || data.includes('No verification') ? 'red' : 'green';
                
                if(data === 'Code verified. You can now reset your password.') {
                    document.getElementById('newPasswordInput').style.display = "block";
                    verifyCodeButton.style.display = "none";
                    resetPasswordButton.style.display = "block";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageContainer.textContent = 'An error occurred. Please try again.';
                messageContainer.style.color = 'red';
            });
        }
    });
    </script>
</body>
</html> 