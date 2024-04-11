<?php 
    include __DIR__ . '/../database/db_config.php';
    
    date_default_timezone_set("Europe/London"); // Get correct timezone

    $passwordInput = $confirmationInput = "";
    $passwordError = $confirmationError = $resetError = "";

    // Check token got passed through URL
    if (isset($_GET["token"]) || !empty($_GET["token"])) {
        
        $token = $_GET["token"]; // Get token
        $token_hash = hash("sha256", $token); // Hash token
        $sql = "SELECT * FROM user WHERE reset_token_hash = ?";
    
        $stmt = $mysqli -> prepare($sql);
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // If no record is found, stop
        if (!empty($user)) {
            // Check token hasn't expired
            if (strtotime($user["reset_token_expires_at"]) <= time()) {
                $resetError = "Token has expired";
            }
        } else {
            $resetError = "Token not found.";
        }

    } else {
        $resetError = "Token not found.";
    }  
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @media (max-width: 768px) {
            .wrapper {
                padding: 20px;
            }
        }
    </style>

    <title>Reset Password</title>

    </head>
    <body>
        <?php   
            require_once __DIR__ . '/../includes/header.php';
            require_once __DIR__ . '/../includes/nav_bar.php'; 
        ?>

        <section class="vh-100">                
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="wrapper">
                        <h2>Reset Password</h2>
                        <?php 
                            if(!empty($resetError)){
                                echo '<div class="alert alert-danger">' . $resetError . '</div>';
                            }  
                            if (!empty($_SESSION['success_message'])) {
                                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                                unset($_SESSION['success_message']); 
                            }      
                        ?>
                        <form action="process-reset-password.php" method="post">

                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <input type="hidden" name="resetError" value="<?= $resetError ?>">

                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($passwordError)) ? 'is-invalid' : ''; ?>" <?php echo (!empty($resetError)) ? 'disabled' : ''; ?>> 
                                <span class="invalid-feedback"><?php echo $passwordError; ?></span>
                            </div>    
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirmationError)) ? 'is-invalid' : ''; ?>" <?php echo (!empty($resetError)) ? 'disabled' : ''; ?>>
                                <span class="invalid-feedback"><?php echo $confirmationError; ?></span>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                            </div>
                        </form>
                    </div>                    
                </div>
            </div>
        </section>
    </body>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

</html>