<?php
namespace Devino;

class DevinoSMS
{

    /**
     * Расшифровка ответа сервера на запрос
     *
     * @param $status int Статус комманды от сервера
     *
     * @return string Расшифровка статус комманды от сервера
     */
    function GetCommandStatus($status){
        switch($status){
            case 0:
                return 'Операция выполнена';
                break;

            case 10:
                return 'Ошибка: некорректный номер получателя сообщения';
                break;

            case 11:
                return 'Ошибка: некорректный адрес отправителя сообщения';
                break;

            case 12:
                return 'Ошибка: некорректный идентификатор сообщения';
                break;

            case 14:
                return 'Ошибка: неправильный пароль';
                break;

            case 15:
                return 'Ошибка: неправильный логин';
                break;

            case 20:
                return 'Ошибка: очередь сообщений полна';
                break;

            case 88:
                return 'Ошибка: недостаточно кредитов';
                break;

            case 1501:
                return 'Ошибка: некорректные параметры';
                break;

            case 1502:
                return 'Ошибка: некорректный тип сообщения';
                break;

            case 1503:
                return 'Неавторизованный IP-адрес';
                break;

            case 1504:
                return 'Ошибка: сервис недоступен';
                break;

            case 1505:
                return 'Ошибка: сервер занят другим запросом';
                break;

            case 1506:
                return 'Ошибка: сервер базы данных недоступен';
                break;

            case 1507:
                return 'Пользователь заблокирован';
                break;

            case 1508:
                return 'Запрещенный host address';
                break;

            case 1509:
                return 'Запрещенный тип доступа';
                break;

            default:
                return $status;
                break;

        }

    }


    /**
     * Расшифровка статуса сообщения
     *
     * @param $status int Статус сообщения
     *
     * @return string Расшифровка статуса сообщения
     */
    function GetMessageStatus($status){
        switch($status){
            case -97:
                return 'Сообщение удвлено';
                break;

            case -40:
                return 'Сообщение находится в очереди';
                break;

            case -30:
                return 'Сообщение передано на сервер';
                break;

            case -20:
                return 'Сообщение находится в режиме отправки';
                break;

            case -10:
                return 'Сообщение передано в мобильную сеть';
                break;

            case 0:
                return 'Сообщение доставлено';
                break;

            case 10:
                return 'Ошибка: некорректный номер получателя сообщения';
                break;

            case 11:
                return 'Ошибка: некорректный адрес отправителя сообщения';
                break;

            case 41:
                return 'Ошибка: сообщение не может быть доставленно';
                break;

            case 42:
                return 'Ошибка: сообщение отклонено СМС центром';
                break;

            case 46:
                return 'Ошибка: истек срок жизни сообщения';
                break;

            default:
                return 'Статус не распознан';
                break;

        }

    }


    /**
     * Формирования и отправка запроса на сервер через cURL
     *
     * @param $xml_data string XML-запрос к серверу (SOAP)
     * @param $headers string Заголовки запроса к серверу (SOAP)
     *
     * @return string XML-ответ от сервера (SOAP)
     */
    function SendToServer($xml_data,$headers){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://ws.devinosms.com/SmsService.asmx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Error: " . curl_error($ch));
        } else {
            curl_close($ch);
            return $data;
        }
    }



    /**
     * GetSessionID – запрос на получение идентификатора сесси
     *
     * @param $UserLogin string Логин пользователя
     * @param $Password string Пароль пользователя
     *
     * @return array("SessionID" => (string) Ответ сервера в виде массива данных
     */

    function GetSessionID($UserLogin,$Password){
        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetSessionID xmlns="http://ws.devinosms.com">
              <login>'.$UserLogin.'</login>
              <password>'.$Password.'</password>
            </GetSessionID>
        </soap:Body>
    </soap:Envelope>';

        $headers = array(
                "POST /SmsService.asmx HTTP/1.1",
                "Host: ws.devinosms.com",
                "Content-Type: text/xml; charset=utf-8",
                "Content-length: ".strlen($xml_data),
                "SOAPAction: http://ws.devinosms.com/GetSessionID"
        );

        $data = $this->SendToServer($xml_data,$headers);

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);


        return array(
                "SessionID" => $results[3]['value']
        );
    }




    /**
     * SendTextMessage - передача простого текстового SMS-сообщения
     *
     * @param $SessionID string Идентификатор сессии
     * @param $DestinationAddresses string Мобильный телефонный номер получателя сообщения, в международном формате: код страны + код сети + номер телефона./Массив мобильных телефонов
     * @param $Parameters string Массив параметров сообщения.
     * @param $Data string Текст сообщения

     *
     * @return array("CommandStatus" => (string) Ответ сервера, "MessageID" => (decimal)) ID смс сообщения/Массив ID смс сообщений
     */
    function SendMessage($SessionID, $Data, $DestinationAddresses,$SourceAddress,$ReceiptRequested, $CountDA){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <SendMessage xmlns="http://ws.devinosms.com">
              <sessionID>'.$SessionID.'</sessionID>
          <message>
                <Data>'.$Data.'</Data>
                <DestinationAddresses>'.$DestinationAddresses.'</DestinationAddresses>
                <SourceAddress>'.$SourceAddress.'</SourceAddress>
                <ReceiptRequested>'.$ReceiptRequested.'</ReceiptRequested>
              </message>
            </SendMessage>
          </soap:Body>
        </soap:Envelope>';

        $headers = array(
                "POST /SmsService.asmx HTTP/1.1",
                "Host: ws.devinosms.com",
                "Content-Type: text/xml; charset=utf-8",
                "Content-length: ".strlen($xml_data),
                "SOAPAction: http://ws.devinosms.com/SendMessage"
        );

        $data = $this->SendToServer($xml_data,$headers);
        // Show me the result
        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);

        $n=0;

        while($n!=$CountDA)
        {
            $res["MessageID".($n+1).""] = iconv("UTF-8","WINDOWS-1251",$results[4+$n]['value']);
            $n++;
        }

        return $res;
    }


    /**
     * GetMessageState – запрос на получение статус отправленного SMS-сообщения
     *
     * @param $SessionID string Идентификатор сессии
     * @param $MessageID string Идентификатор сообщения
     *
     * @return array("CommandStatus" => (string) Ответ сервера, "MessageStatus" => (string) Сататус сообщения/Массив статусов сообщений, "Date" => (string)) Дата получения отчёта
     */
    function GetMessageState($SessionID,$MessageID){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://
www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetMessageState xmlns="http://ws.devinosms.com">
              <sessionID>'.$SessionID.'</sessionID>
              <messageID>'.$MessageID.'</messageID>
            </GetMessageState>
          </soap:Body>
        </soap:Envelope>';

        $headers = array(
                "POST /SmsService.asmx HTTP/1.1",
                "Host: ws.devinosms.com",
                "Content-Type: text/xml; charset=utf-8",
                "Content-length: ".strlen($xml_data),
                "SOAPAction: http://ws.devinosms.com/GetMessageState"
        );

        $data = $this->SendToServer($xml_data,$headers);

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
        return array(
                "State" => $this->GetCommandStatus($results[4]['value']),
                //"TimeStampUtc" => join(' ',split('T',$results[5]['value'])),
                "StateDescription" =>iconv("UTF-8","WINDOWS-1251",$results[6]['value'])
        );
    }
    /**
     * GetMessageState – запрос на получение состояния счета
     *
     * @param $SessionID string Идентификатор сессии
     * Пример вызова:
     * $bal = $devino->GetBalance($result[SessionID]); // переменная $bal приняла значение счета.
     *
     * return array("GetBalanceResult" => iconv("UTF-8","WINDOWS-1251",$results[3]['value']) -- Состояние счета. Если $results[3] заменит
     * на $results[4], выдаст статус запроса(пустой ответ значит выполнилось правильно)
     */
    function GetBalance($SessionID){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://
www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetBalance xmlns="http://ws.devinosms.com">
                <sessionID>'.$SessionID.'</sessionID>
            </GetBalance>
          </soap:Body>
        </soap:Envelope>';

        $headers = array(                                    //Такие же как и в GetMassageState
                "POST /SmsService.asmx HTTP/1.1",
                "Host: ws.devinosms.com",
                "Content-Type: text/xml; charset=utf-8",
                "Content-length: ".strlen($xml_data),
                "SOAPAction: http://ws.devinosms.com/GetBalance"
        );

        $data = $this->SendToServer($xml_data,$headers);

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
        //$NewString =$results[3]."med";
        return array(
                "GetBalanceResult" => iconv("UTF-8","WINDOWS-1251",$results[3]['value']),
        );

    }
    }