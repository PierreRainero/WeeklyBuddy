<?php

namespace WeeklyBuddy\Services\Util;

use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};
use WeeklyBuddy\Exceptions\EmailException;

/**
 * Service to check and send emails
 * It should be the only part of the backend that communicate directly with the user that's why no i18n is used.
 */
class EmailService {
    /**
     * Parameters to send a mail through an SMTP server
     * @var array
     */
    private $emailsParams;

	/**
     * Injected constructor
     */
    public function __construct() {
        $smtpConfigFile = join(DIRECTORY_SEPARATOR, array(dirname(dirname(dirname(dirname(__DIR__)))), 'env', 'smtp.php'));
        if(file_exists($smtpConfigFile)){
            $this->emailsParams = include $smtpConfigFile;
        } else {
            $this->emailsParams = null;
        }
    }
    
    /**
     * Checks if a given email is considered as valid
     * @param string $email The email to check
     * @return bool "true" if the email pass the validation, "false" otherwise
     */
    public function emailIsValid(string $email): bool {
        $explodedString = explode('@',$email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) && checkdnsrr(array_pop($explodedString),'MX');
    }

    /**
     * Sends an activation email for an user
     * @param string $email The email to use as recipient
     * @param string $token The activation token to use
     * @param string $keyLang The language key to use ("fr", "en")
     * @return void
     * @throws EmailException
     */
    public function sendActivationEmail(string $email, string $token, string $keyLang): void {
        if($this->emailsParams === null) {
            throw new EmailException('SMTP configurations missing.');
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->SMTPAuth     = $this->emailsParams['auth'];
            $mail->Host         = $this->emailsParams['host'];
            $mail->Port         = $this->emailsParams['port'];
            $mail->SMTPSecure   = $this->emailsParams['protocol'];
            $mail->Username     = $this->emailsParams['username'];
            $mail->Password     = $this->emailsParams['password'];
            $mail->setFrom($this->emailsParams['emitter'], 'WeeklyBuddy');
            $mail->addAddress($email);
            $mail->CharSet      = 'UTF-8';
            $mail->Subject      = $this->activationTitle($keyLang);
            $mail->isHTML(true);
            $mail->Body         = $this->activationBody($keyLang, $token);
            $mail->send();
        } catch (Exception $e) {
            throw new EmailException('Activation email can\'t be sent.');
        }
    }

    /**
     * Generates the activation email title according to given language
     * @param string $keyLang The language key to use ("fr", "en")
     * @return string Email subject
     */
    private function activationTitle(string $keyLang): string {
        if($keyLang === 'fr') {
            return 'Bienvenue sur WeeklyBuddy';
        } else {
            return 'Welcome to WeeklyBuddy';
        }
    }

    /**
     * Generates the activation email body according to given language
     * @param string $token The activation token to use
     * @param string $keyLang The language key to use ("fr", "en")
     * @return string Email body
     */
    private function activationBody(string $keyLang, string $token): string {
        if($keyLang === 'fr') {
            return '<h2>Bienvenue sur WeeklyBuddy</h2><br/>Pour commencer à utiliser votre compte vous avez <strong>une semaine</strong> pour l\'activer. Pour cela il vous suffit de cliquer sur le lien suivant :<br/><div style="text-align:center;"><div style="background-color: #838ebd;display: inline-block;padding: 4px;border-radius: 4px;"><a target="_blank" href="'.$this->activationUrl($token).'" style="color: #fff;font-weight: 700;text-decoration: none;">Activer mon compte</a></div></div><br/><br/>Cordialement,<br/>WeeklyBuddy';
        } else {
            return '<h2>Welcome to WeeklyBuddy</h2><br/>To start using your account you have <strong>one week</strong> to activate it. To do this, simply click on the following link:<br/><div style="text-align:center;"><div style="background-color: #838ebd;display: inline-block;padding: 4px;border-radius: 4px;"><a target="_blank" href="'.$this->activationUrl($token).'" style="color: #fff;font-weight: 700;text-decoration: none;">Activate my account</a></div></div><br/><br/>Cordially,<br/>WeeklyBuddy';
        }
    }

    private function activationUrl(string $token): string {
        return 'https://'.$this->emailsParams['api-domain'].'/users/activate?token='.$token;
    }
}
