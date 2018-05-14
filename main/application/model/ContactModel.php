<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 3/25/18
 * Time: 5:25 PM
 */

namespace markpthomas\main;


class ContactModel {
    /**
     * Sends a contact e-mail to the website administrator.
     *
     * @return boolean Gives back the success status of sending the contact email.
     */
    public static function contactAdmin()
    {
        if (!Request::post('email')) return false;

        // clean the input
        $toEmail = Config::get('EMAIL_WEBMASTER');
        $fromEmail = strip_tags(Request::post('email'));
        $fromName = strip_tags(Request::post('senderName'));
        $subject = strip_tags(Request::post('subject'));
        $body = strip_tags(Request::post('body'));
        $body = wordwrap($body, 70);

        // stop registration flow if registrationInputValidation() returns false (= anything breaks the input check rules)
        $validation_result = self::contactInputValidation(Request::post('captcha'), $fromEmail, $fromName, $subject, $body);
        if (!$validation_result) {
            return false;
        }

        // send verification email
        $fromName = Config::get('DOMAIN_NAME') . ' - ' . $fromName;
        return self::sendContactEmail($toEmail, $fromEmail, $fromName, $subject, $body);
    }

    /**
     * Validates the contact input.
     *
     * @param $captcha
     * @param $fromEmail
     * @param $fromName
     * @param $subject
     * @param $body
     * @return bool
     */
    public static function contactInputValidation($captcha, $fromEmail, $fromName, $subject, $body)
    {
        $return = true;

        // perform all necessary checks
        if (!CaptchaModel::checkCaptcha($captcha)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_CAPTCHA_WRONG'));
            $return = false;
        }

        // if username, email and password are all correctly validated, but make sure they all run on first submit
        if (DataValidator::validateUserEmail($fromEmail, $fromEmail) &&
            $return) {
            return true;
        }

        if (empty($fromName)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FROM_FIELD_EMPTY'));
            return false;
        }

        if (empty($subject)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_SUBJECT_FIELD_EMPTY'));
            return false;
        }

        if (empty($body)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_BODY_FIELD_EMPTY'));
            return false;
        }

        // otherwise, return false
        return false;
    }

    /**
     * Sends the contact email to the web admin.
     *
     * @param string $toEmail E-mail address the message is being sent to.
     * @param string $fromEmail The e-mail address of the e-mail sender.
     * @param string $fromName Who the e-mail is being sent from.
     * @param string $subject The email subject.
     * @param string $body The email body.
     * @return boolean Gives back true if mail has been sent, gives back false if no mail could been sent
     */
    public static function sendContactEmail($toEmail, $fromEmail, $fromName, $subject, $body)
    {
        $mail = new Mail;
        $mail_sent = $mail->sendMail(
            $toEmail,
            $fromEmail,
            $fromName,
            $subject,
            $body
        );

        if ($mail_sent) {
            Session::add('feedback_positive', Text::get('FEEDBACK_CONTACT_MAIL_SENDING_SUCCESSFUL'));
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_CONTACT_MAIL_SENDING_ERROR') . $mail->getError() );
            return false;
        }
    }
} 