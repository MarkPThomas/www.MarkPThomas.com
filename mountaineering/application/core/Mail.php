<?php

namespace markpthomas\mountaineering;
use PHPMailer;

/**
 * Class Mail
 *
 * Handles everything regarding mail-sending.
 */
class Mail
{
    /** @var mixed variable to collect errors */
    private $error;

    /**
     * Try to send a mail by using PHP's native mail() function.
     * Please note that PHP itself will not send a mail, it's just a wrapper for Linux's sendmail or other mail tools.
     *
     * Good guideline on how to send mails natively with mail():
     * @see http://stackoverflow.com/a/24644450/1114320
     * @see http://www.php.net/manual/en/function.mail.php
     */
    public function sendMailWithNativeMailFunction()
    {
        // no code yet, so we just return something to make IDEs and code analyzer tools happy
        return false;
    }

    /**
     * Try to send a mail by using SwiftMailer.
     * Make sure you have loaded SwiftMailer via Composer.
     *
     * @return bool
     */
    public function sendMailWithSwiftMailer()
    {
        // no code yet, so we just return something to make IDEs and code analyzer tools happy
        return false;
    }

    /**
     * Try to send a mail by using PHPMailer.
     * Make sure you have loaded PHPMailer via Composer.
     * Depending on your EMAIL_USE_SMTP setting this will work via SMTP credentials or via native mail()
     *
     * @param $toEmail
     * @param $fromEmail
     * @param $fromName
     * @param $subject
     * @param $body
     * @param string $userName
     * @param string $bodyNoHTML
     * @return bool
     */
    public function sendMailWithPHPMailer($toEmail, $fromEmail, $fromName, $subject, $body, $userName = '', $bodyNoHTML = '')
    {
        $mail = new PHPMailer;
        
        // you should use UTF-8 to avoid encoding issues
        $mail->CharSet = 'UTF-8';

        // Set email format to HTML
        //$mail->isHTML(true);

        // if you want to send mail via PHPMailer using SMTP credentials
        if (Config::get('EMAIL_USE_SMTP')) {
            // ==== Server settings ====

            // set PHPMailer to use SMTP
            // Turned off to work from localhost. See: https://www.youtube.com/watch?v=ERaTuqeIRBM
//            $mail->isSMTP();

            // 0 = off,
            // 1 = commands,
            // 2 = commands and data, perfect to see SMTP errors
            // 2 enables verbose debug output. This writes all of the actions out as echo on the screen.
            // 3 = 3: As 2, but also show details about the initial connection;
            // only use this if you're having trouble connecting (e.g. connection timing out)
            $mail->SMTPDebug = 0;

            // enable SMTP authentication
            $mail->SMTPAuth = Config::get('EMAIL_SMTP_AUTH');

            // encryption
            if (Config::get('EMAIL_SMTP_ENCRYPTION')) {
                $mail->SMTPSecure = Config::get('EMAIL_SMTP_ENCRYPTION');
            }

            // set SMTP provider's credentials
            $mail->Host = Config::get('EMAIL_SMTP_HOST');           // Specify main and backup SMTP servers
            $mail->Username = Config::get('EMAIL_SMTP_USERNAME');
            $mail->Password = Config::get('EMAIL_SMTP_PASSWORD');
            $mail->Port = Config::get('EMAIL_SMTP_PORT');

        } else {
            // Send messages using PHP's mail() function.
            $mail->isMail();
        }

        // fill mail with data
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $userName);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $bodyNoHTML;

        // try to send mail, put result status (true/false into $wasSendingSuccessful)
        // I'm unsure if mail->send really returns true or false every time, this method in PHPMailer is quite complex
        $wasSendingSuccessful = $mail->send();

        if ($wasSendingSuccessful) {
            return true;

        } else {

            // if not successful, copy errors into Mail's error property
            $this->error = $mail->ErrorInfo;
            return false;
        }
    }

    /**
     * The main mail sending method, this simply calls a certain mail sending method depending on which mail provider
     * you've selected in the application's config.
     *
     * @param $toEmail string Email.
     * @param $fromEmail string Sender's email.
     * @param $fromName string Sender's name.
     * @param $subject string Subject.
     * @param $body string Full mail body text.
     * @param string $userName
     * @param string $bodyNoHTML
     * @return bool The success status of the mail sending method.
     */
    public function sendMail($toEmail, $fromEmail, $fromName, $subject, $body, $userName = '', $bodyNoHTML = '')
    {
        if (Config::get('EMAIL_USED_MAILER') == "phpmailer") {

            // returns true if successful, false if not
            return $this->sendMailWithPHPMailer(
                $toEmail, $fromEmail, $fromName, $subject, $body, $userName, $bodyNoHTML
            );
        }

        if (Config::get('EMAIL_USED_MAILER') == "swiftmailer") {
            return $this->sendMailWithSwiftMailer();
        }

        if (Config::get('EMAIL_USED_MAILER') == "native") {
            return $this->sendMailWithNativeMailFunction();
        }
    }

    /**
     * The different mail sending methods write errors to the error property $this->error.
     * This method simply returns this error / error array.
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
