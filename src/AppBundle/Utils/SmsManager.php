<?php
namespace AppBundle\Utils;

use Devino\DevinoSMS;
use AppBundle\Entity\User;

class SmsManager
{

    private $phoneNumberUtil;
    private $login;
    private $pass;

    public function __construct($phoneNumberUtil, $login, $pass)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->login = $login;
        $this->pass  = $pass;
    }

    public function generateCode()
    {
        $alphabet = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $length = 6;
        $alphabetLength = sizeof($alphabet);

        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[mt_rand(0, $alphabetLength-1)];
        }

        return $code;
    }

    public function sendValidation(User $user, $smsTemplate = 'Код валидации %s')
    {
        $phoneString = $this->phoneNumberUtil->format($user->getPhone(), \libphonenumber\PhoneNumberFormat::E164);
        $code = $this->generateCode();
        $this->sendSms($phoneString, sprintf($smsTemplate, $code));

        $user->setGeneratedCode($code);
    }

    public function normalizePhone($phone)
    {
        $p = str_replace(array('+7', '(', '-', ')'), array('8', '', '', ''), $phone);
        if (preg_match('/^\d{11}$/', $p)) {
            return $p;
        } else {
            return null;
        }
    }

    public function sendSms($phone, $message)
    {
        $devino = new DevinoSMS();

        $result = $devino->GetSessionID($this->login,$this->pass);  // Получение ID сессии, его не обязательно получать при каждой отправке СМС,
        //можно использовать пока он не будет удален из кэша нашего сервера

        $da = array(//Массив с номерами, на которые будет отправлено СМС. Использовать толко такой формат.
                $phone
        );


        $countDA = count($da); //Количество номеров.

        $sourceAddress = addslashes('<![CDATA[carservice]]>'); //Имя отправителя, подключаются у менеджеров

        $receiptRequested='true';
        $destinationAddresses = '';

        foreach ($da as $s)//Перевод номеров в тег <string>
            $destinationAddresses.='<string>'.$s.'</string>';

        $data = addslashes('<![CDATA['.$message.']]>');  //Текст СМС, вводится между квадратными скобками
        $n = 1; //Количество сообщений в одном СМС, необходимо для корректного возврата MessageID, если не нужны, то можно не трогать
        //Правила деления смотрите на нашем сайте devinosms.com

        $result += $devino->SendMessage($result['SessionID'],$data, $destinationAddresses,$sourceAddress,$receiptRequested,$countDA*$n); // Отправка СМС

        return $result;
    }

}