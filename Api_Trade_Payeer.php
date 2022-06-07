<?php

class Api_Trade_Payeer
{
    private $arError = [];

    public function __construct(private array $arParams = [])
    {
    }

    private function Request(array $req = [])
    {
        $msec = round(microtime(true) * 1000);
        $req['post']['ts'] = $msec;

        $post = json_encode($req['post']);

        $sign = hash_hmac('sha256', $req['method'] . $post, $this->arParams['key']);

        $ch = curl_init();

        $url = "https://payeer.com/api/trade/" . $req['method'];
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "API-ID: " . $this->arParams['id'],
            "API-SIGN: " . $sign
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $arResponse = json_decode($response, true);

        if ($arResponse['success'] !== true) {
            $this->arError = $arResponse['error'];
            throw new Exception($arResponse['error']['code']);
        }

        return $arResponse;
    }


    public function GetError()
    {
        return $this->arError;
    }


    public function Info(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'info',
            'post' => $req,
        ]);

        return $res;
    }

    public function Orders(string $pair = 'BTC_USDT'): array
    {
        $res = $this->Request([
            'method' => 'orders',
            'post' => [
                'pair' => $pair,
            ],
        ]);

        return $res['pairs'];
    }

    public function Account(): array
    {
        $res = $this->Request([
            'method' => 'account',
        ]);

        return $res['balances'];
    }

    public function OrderCreate(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'order_create',
            'post' => $req,
        ]);

        return $res;
    }

    public function OrderStatus(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'order_status',
            'post' => $req,
        ]);

        return $res['order'];
    }

    public function MyOrders(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'my_orders',
            'post' => $req,
        ]);

        return $res['items'];
    }

    public function Time(): bool
    {
        $res = $this->Request([
            'method' => 'time',
        ]);

        return $res['success'];
    }

    public function Ticker(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'ticker',
            'post' => $req
        ]);

        return $res;
    }

    public function Trades(string $pair = 'BTC_USD,BTC_RUB'): array
    {
        $res = $this->Request([
            'method' => 'trades',
            'post' => [
                'pair' => $pair
            ]
        ]);

        return $res;
    }

    public function OrderCancel(int $order_id): bool
    {
        $res = $this->Request([
            'method' => 'order_cancel',
            'post' => [
                'order_id' => $order_id
            ],
        ]);

        return $res['success'];
    }

    public function OrdersCancel(array $req): array
    {
        $res = $this->Request([
            'method' => 'orders_cancel',
            'post' => $req,
        ]);

        return $res;
    }

    public function MyHistory(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'my_history',
            'post' => $req,
        ]);

        return $res;
    }

    public function MyTrades(array $req = []): array
    {
        $res = $this->Request([
            'method' => 'my_trades',
            'post' => $req,
        ]);

        return $res;
    }

}
