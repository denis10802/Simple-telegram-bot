<?php
require 'vendor/autoload.php';
use Telegram\Bot\Api;



 $telegram = new Api('1741243408:AAHpK6cNA5ettpQ421uS88Wy7YpmILHGoNA');
 $result = $telegram->getWebhookUpdates();

$text = $result["message"]["text"]; //Текст сообщения
$chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
$name = $result["message"]["from"]["username"]; //Юзернейм пользователя
$keyboard = [["Курс валют","Новости IT"]]; //Клавиатура

    if ($text == "/start")
    {
        $reply = "Привет @". $name. ' Вас приветствует Бот помощник!';
       $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    }

    elseif ($text == "Курс валют")
    {
        $date = date('d/m/Y');
        $xml = simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date");

        foreach ($xml->Valute as $valute){
            switch ($valute->NumCode){
                case 978:
                    $nameEUR = $valute->Name;
                    $valueEUR = $valute->Value;
                    $charCodeEUR = $valute->CharCode;
                    $nominalEUR = $valute->Nominal;
                    break;
                case 840:
                    $nameUSD = $valute->Name;
                    $valueUSD = $valute->Value;
                    $charCodeUSD = $valute->CharCode;
                    $nominalUSD = $valute->Nominal;
                    break;
            }
        }
        $message = "Курс валют на ".$date. " \n\n " .$nameEUR." \n ". $nominalEUR.' '. $charCodeEUR." = ".$valueEUR." RUB\n\n ".
            $nameUSD." \n ". $nominalUSD.' '. $charCodeUSD." = ".$valueUSD." RUB \n\n<em> По данным ЦБРФ</em>";
        $telegram->sendMessage(['chat_id' => $chat_id,'parse_mode'=>'HTML','text'=> $message ]);
    }


    elseif($text == "Новости IT")
    {
        $reply = "Новости из мира IT: \n\n";

        $xml = simplexml_load_file('https://news.google.com/rss/search?q=iT&hl=ru&gl=RU&ceid=RU%3Aru');
        $i=0;
        foreach ($xml->channel->item as $item){
            $i++;
            if($i>5){
                break;
            }
            $reply .= "\xE2\x9E\xA1" .$item->title."\nДата: ".$item->pubDate."\n<a href='". $item->link ."'>Читать полностью ...</a>\n\n";
        }
        $telegram->sendMessage(['chat_id' => $chat_id,'parse_mode'=>'HTML','disable_web_page_preview'=>true,'text'=> $reply ]);
    }


    else
    {
        $reply = "Что бы узнать курс валют введите в поле ввода Курс евро";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
    }



