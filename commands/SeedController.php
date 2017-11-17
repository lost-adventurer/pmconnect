<?php
/**
 * Created by PhpStorm.
 * User: Rafe
 * Date: 29/11/2017
 * Time: 23:48
 */

// commands/SeedController.php
namespace app\commands;

use yii\console\Controller;
use app\models\User;
use app\models\Product;
use app\models\Subscription;

class SeedController extends Controller
{
    public function actionIndex()
    {
        $faker = \Faker\Factory::create();

        $user = new User();
        $product = new Product();
        $subscription = new Subscription();

        for ( $i = 1; $i <= 20; $i++ )
        {
            $user->setIsNewRecord(true);
            $user->id = null;
            $user->msisdn = $faker->phoneNumber;
            $user->save();

            $product->setIsNewRecord(true);
            $product->id = null;
            $product->description = $faker->text;
            $product->save();

            $subscription->setIsNewRecord(true);
            $subscription->id = null;
            $subscription->user_id = $user->id;
            $subscription->product_id = $product->id;
            $subscription->status = 1;
            $subscription->start_date = $faker->dateTime()->format('Y-m-d H:i:s');
            $subscription->end_date = $faker->dateTimeInInterval('now', '+1 year')->format('Y-m-d H:i:s');
            $subscription->save();
        }

    }
}