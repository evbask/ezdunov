<?php

namespace App\Http\Controllers\ApiApp;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\ApiAppController;

use App\User;
use App\LogsSms;
use App\Logs_rfi_bank;
use App\Jobs\SendSms;
use App\Jobs\WriteOfMoney;
use App\Components\Firebase;
use Auth;
use App\Components\Sms;
use App\Components\RfiBank;
// use Request;


use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;

/**
 * @todo контроллер помойка, для тестов
 * в продакшене убрать
 */
class TestController extends ApiAppController
{
    /**
     * получение информации о пользователее
     */
    public function test(Request $request)
    {
        $rfi = Logs_rfi_bank::create([
            'user_id'       => Auth::user()->id,
            'summ'          => 500,
            'status'        => Logs_rfi_bank::STATUS_WAITING,
            'service_id'    => RfiBank::SERVICE_ID,
            'phone'         => Auth::user()->phone,
            'type'          => RfiBank::PAYMENT_TYPE,
            'email'         => Auth::user()->email
        ]);
        
        dispatch((new WriteOfMoney($rfi))->
            onConnection('database')->
            onQueue('WriteOfMoney'));
        

        // SendSms::dispatch($sms);
        exit;
        var_dump($request->file);
        $files = new \App\Components\ImageWebp();
        $array = [
            base_path(),
            'secureImages',
            'temp'
        ];
        $files->build($request->file, $array);
        $files->convert();
        exit;
        return \App\LogsBonus::historyForApp(Auth::user());
        echo (new \DateTime())->format('Y-m-d H:i:s');
        echo "<hr>";
        echo (new \DateTime())->modify('-60 sec')->format('Y-m-d H:i:s');
        exit;
        // echo Auth::user()->id;
        // exit;
    //    var_dump(Auth::user()->recurrentPayment(200));
    //    var_dump(Auth::user()->getErrors());
    $data = [
        'title' => 'Тест',
        'body'  => 'Тестовый тест теста'
    ];
//         $user = Auth::user();
//         $push = new Firebase([$user, $user], $data);
//         $push->send();
// exit;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = $file->extension();
            $oldFullPath = $file->getRealPath();
            // echo "test1.jpg:<br />\n";
            $exif = exif_read_data($oldFullPath);
            echo $exif===false ? "Не найдено данных заголовка.<br />\n" : "Изображение содержит заголовки<br />\n";
    echo "<pre>";
            $exif = exif_read_data($oldFullPath);
            print_r($exif);
            $lat = $exif['GPSLatitude'];
            $lon = $exif['GPSLongitude'];
            print_r($lat);
            print_r($lon);
            // echo "test2'.jpg:<br />\n";
            // foreach ($exif as $key => $section) {
            //     foreach ($section as $name => $val) {
            //         echo "$key.$name: $val<br />\n";
            //     }
            // }
        } else {
            echo "проблемы";
        }
    }

    public function sendPush()
    {
        echo "push";
        Auth::user()->sendPush(['title' => 'title', 'body' => 'body']);
        exit;
        // exit;
        // This assumes that you have placed the Firebase credentials in the same directory
        // as this PHP file.
        $serviceAccount = ServiceAccount::fromJsonFile(base_path() . '/config/ezdunov-firebase-adminsdk-9syv3-56f864a14f.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();
            
        $messaging = $firebase->getMessaging();

        $message = CloudMessage::fromArray([
            'topic' => 'user58',
            'notification' => [
                'title' => 'test push',
                'body' => 'test'
            ]
        ]);

        print_r($messaging->send($message));
    }

    public function balance(Request $request)
    {
        $user = Auth::user();
        if ($request->amount < 0) {
            $user->balanceDecrease(+$request->amount, 1);
        } else {
            $user->balanceIncrease(+$request->amount, 1);
        }
    }

    public function bonus(Request $request)
    {
        $user = Auth::user();
        if ($request->amount < 0) {
            $user->bonusDecrease(+$request->amount, 1);
        } else {
            $user->bonusIncrease(+$request->amount, 1);
        }
    }
}