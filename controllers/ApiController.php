<?php
/**
 * Created by PhpStorm.
 * User: Rafe
 * Date: 29/11/2017
 * Time: 20:18
 */

namespace app\controllers;

use Yii;
use app\models\Subscription;
use app\models\User;
use app\models\Product;
use yii\rest\ActiveController;
use yii\web\Response;


class ApiController extends ActiveController
{
    public $modelClass = 'app\models\Subscription';

    public function actionSubscribe($msisdn, $product_id){

        //TODO validation
        if(strtoupper(substr($msisdn,0,1)) == 'A'){
            $user = User::findOne([
                'alias'=>$msisdn,
            ]);
            if(!$user){
                $response['status'] = '400';
                $response[] = [
                    'message'=>'User Alias does not exit',
                ];
                return $response;
            }
        }else{
            $user = User::findOne([
                'msisdn'=>$msisdn,
            ]);
        }

        $product = Product::findOne($product_id);

        if($user and $product){
            $subscription = Subscription::findOne([
                'user_id'=>$user->id,
                'product_id'=>$product->id,
            ]);

            if($subscription and $subscription->status == 0){
                $subscription->status = 1;
                if($subscription->save()){
                    $response['status'] = '200';
                    $response[] = [
                        'message'=>'Subscription activated',
                    ];
                }else{
                    $response['status'] = '400';
                    $response[] = [
                        'message'=>'Did not save Subscription status',
                    ];
                }
            }elseif($subscription){
                $response['status'] = '200';
                $response[] = [
                    'message'=>'Subscription activated',
                ];
            }else{
                $subscription = new Subscription();
                $date = new \DateTime('now');
                $datePlus = new \DateTime('+1 year');
                $subscription->user_id = $user->id;
                $subscription->product_id = $product->id;
                $subscription->start_date = $date->format('Y-m-d H:i:s');
                $subscription->end_date = $datePlus->format('Y-m-d H:i:s');
                $subscription->status = 1;
                if($subscription->save()){
                    $response['status'] = '200';
                }else{
                    $response['status'] = '400';
                    $response[] = [
                        'message'=>'Did not save Subscription status',
                    ];
                }
            }
        }elseif($user){
            $response['status'] = '400';
            $response[] = [
                'message'=>'Product does not exist',
            ];
        }else{
            $response['status'] = '400';
            $response[] = [
                'message'=>'User does not exist',
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionUnsubscribe($msisdn, $product_id){

        //TODO validation

        if(strtoupper(substr($msisdn,0,1)) == 'A'){
            $user = User::findOne([
                'alias'=>$msisdn,
            ]);
            if(!$user){
                $response['status'] = '400';
                $response[] = [
                    'message'=>'User Alias does not exit',
                ];
                return $response;
            }
        }else{
            $user = User::findOne([
                'msisdn'=>$msisdn,
            ]);
        }

        $product = Product::findOne($product_id);

        if($user and $product){
            $subscription = Subscription::findOne([
                'user_id'=>$user->id,
                'product_id'=>$product->id,
            ]);

            if($subscription and $subscription->status == 1){
                $subscription->status = 0;
                if($subscription->save()){
                    $response['status'] = '200';
                }else{
                    $response['status'] = '400';
                    $response[] = [
                        'message'=>'Failed to save',
                    ];
                }
            }elseif($subscription){
                $response['status'] = '200';
            }else{
                $response['status'] = '400';
                $response[] = [
                    'message'=>'Subscription does not exist',
                ];
            }
        }elseif($user){
            $response['status'] = '400';
            $response[] = [
                'message'=>'Product does not exist',
            ];
        }elseif($product){
            $response['status'] = '400';
            $response[] = [
                'message'=>'User does not exist',
            ];
        }else{
            $response['status'] = '400';
            $response[] = [
                'message'=>'Product and user does not exist',
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionSearch($msisdn = null, $product_id = null){
        //TODO validation

        if($msisdn and $product_id){
            $user = User::findOne([
                'msisdn'=>$msisdn,
            ]);
            $product = Product::findOne($product_id);

            $subscription = Subscription::findOne([
                'user_id'=>$user->id,
                'product_id'=>$product->id,
            ]);
            $response[] = [
                'id'=>$subscription->id,
                'description'=>$subscription->product->description,
                'start_date'=>$subscription->start_date,
                'end_date'=>$subscription->end_date,
                'status'=>$subscription->status,
            ];
            $response['status'] = '200';
        }elseif($msisdn){
            $user = User::findOne([
                'msisdn'=>$msisdn,
            ]);

            $subscriptions = Subscription::findAll([
                'user_id'=>$user->id,
            ]);

            foreach($subscriptions as $subscription){
                $response[] = [
                    'id'=>$subscription->id,
                    'description'=>$subscription->product->description,
                    'start_date'=>$subscription->start_date,
                    'end_date'=>$subscription->end_date,
                    'status'=>$subscription->status,
                ];
            }
            $response['status'] = '200';
        }elseif($product_id){
            $product = Product::findOne($product_id);

            $subscriptions = Subscription::findAll([
                'product_id'=>$product->id,
            ]);

            foreach($subscriptions as $subscription){
                $response[] = [
                    'id'=>$subscription->id,
                    'description'=>$subscription->product->description,
                    'start_date'=>$subscription->start_date,
                    'end_date'=>$subscription->end_date,
                    'status'=>$subscription->status,
                ];
            }
            $response['status'] = '200';
        }else{
            $response['status'] = '400';
            $response[] = [
                'message'=>'Either product_id or msisdn needs to be set',
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    private function getAlias($msisdn){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://interview.pmcservices.co.uk/alias/lookup?msisdn='.$msisdn,
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}