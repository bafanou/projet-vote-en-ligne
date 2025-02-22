<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

function envoyerEmail($destinataire, $sujet, $messageHtml, $messageTexte = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ajoutvie24@gmail.com'; // Ton email SMTP
        $mail->Password = 'gwtx csvv dnhg pvfm '; // Mot de passe SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@example.com', 'Systeme de Vote en Ligne');
        $mail->addAddress($destinataire);

        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $messageHtml;
        $mail->AltBody = $messageTexte;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "L'envoi de l'email a échoué : {$mail->ErrorInfo}";
        return false;
    }
}
?>
