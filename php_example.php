 <?php
 
  public function getBalance()
 {
        $endpoint   = "/contract/v3/private/copytrading/wallet/balance";
        $method     = "GET";
        $params     = "coin=>USDT";
        $allTotalEquity = 0;
        /**
         * get data for the first account
         */

    $multiple = CopyTradeConf::getCopyTrader();

        $equity = 0;
            foreach ($multiple as $key => $value) {
                    $data    =  $this->sendToBybitReal($endpoint, $method, $params, $value->api_key, $value->secret_key, "");

                    $newData = json_decode($data, true);
                    $equity += $newData['result']['equity'];
            }

        $allTotalEquity = $equity;

        return response()->success($allTotalEquity);
    }

    public function takeProfitMarket()
    {
        $jml    = $this->SignalCOPY->getBysignalId($request['id']);

                foreach ($jml as $key => $value) {
                     // get the Sl just from entri one
                   if($key == 0){
                     $det_price    = $this->SignalOPD->getBysignalId($value->id);

                     if (isset($dataTp)) {
                      $data_Tp = $dataTp->price;
                      } else {
                        if(isset($det_price)){
                            $new_pos_price = [];
                            foreach ($det_price as $keyprice => $vl) {
                                $endpointPrice = $this->Testnet . '/contract/v3/private/copytrading/order/list';
                                $params = [
                                    'symbol' => $request['token'],
                                    'copyTradeOrderType' => 'OpenOrderFilled',
                                    'orderLinkId'  => $vl->orderLinkId,
                                ];
                                $data_sl =  $this->Test2($endpointPrice, $params, $vl->api_key,$vl->secret_key, "GET");

                                if($data_sl['result']['list']){
                                    foreach ($data_sl['result']['list'] as $keyP => $value) {
                                        if($value['copyTradeOrderStatus'] == 'OpenOrderFilled'){
                                            $new_pos_price[] = $value['orderLinkId'];
                                        }
                                    }
                               }
                            }
                        }
                        if($new_pos_price != []){
                             foreach ($det_price as $ke => $vals) {
                            if(in_array($vals->orderLinkId,$new_pos_price)){
                                $detailPrice[] = [
                                        'orderLinkId' => $vals->orderLinkId,
                                        'api_key' => $vals->api_key,
                                        'secret' => $vals->secret_key,
                                ];
                              }
                           }
                          }

                          if($detailPrice != []){
                           foreach ($detailPrice as $keye => $vali) {
                                if($keye == 0){
                                    $endpointPrice = $this->Testnet . '/contract/v3/private/copytrading/order/list';
                                    $params = [
                                        'symbol' => $request['token'],
                                        'copyTradeOrderType' => 'OpenOrderFilled',
                                        'orderLinkId'  => $vali['orderLinkId'],
                                    ];
                                    $data_sl =  $this->Test2($endpointPrice, $params, $vali['api_key'],$vali['secret'], "GET");

                                    if($data_sl['result']['list']){
                                        foreach ($data_sl['result']['list'] as $keyeP => $valueP) {
                                            if($valueP['copyTradeOrderStatus'] == 'OpenOrderFilled'){
                                                 $priceTP[$keyeP]['price'] = $valueP['price'];
                                            }
                                        }
                                    }
                                }
                            }


                        if($priceTP){
                            $i = 0;
                            foreach ($priceTP as $k => $value) {
                               $newPrice[$i] = $value;
                               $i++;
                            }
                        }
                        $data_Tp =  $newPrice[0]['price'];
                       }
                      }

                     if($request['side'] == 'Buy'){
                          $new_price = doubleval($data_Tp) + doubleval($data_Tp * 0.0012);
                      }else{
                          $new_price = doubleval($data_Tp) - doubleval($data_Tp * 0.0012);
                      }

                      $sl = number_format($new_price,$request['price_scale'],'.','');
                }
                }
    }