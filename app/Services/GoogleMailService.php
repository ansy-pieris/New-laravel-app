<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class GoogleMailService
{
    private $client;
    private $gmail;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->addScope(Gmail::GMAIL_SEND);
        $this->client->setAccessType('offline');
        
        // Get access token using refresh token
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');
        if ($refreshToken) {
            try {
                // Use refresh token to get access token
                $accessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                
                if (isset($accessToken['error'])) {
                    throw new \Exception('Error getting access token: ' . $accessToken['error']);
                }
                
                $this->client->setAccessToken($accessToken);
            } catch (\Exception $e) {
                throw new \Exception('Failed to authenticate with Google: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('Google refresh token not found. Please run OAuth setup.');
        }

        $this->gmail = new Gmail($this->client);
    }

    public function sendOrderConfirmation($order, $customerEmail)
    {
        $subject = "Order Confirmation - ARES Store #{$order->id}";
        
        $body = $this->buildOrderConfirmationEmail($order);
        
        $message = $this->createMessage(
            env('MAIL_FROM_ADDRESS'),
            $customerEmail,
            $subject,
            $body
        );

        return $this->gmail->users_messages->send('me', $message);
    }

    private function buildOrderConfirmationEmail($order)
    {
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border: 2px solid #000; overflow: hidden; }
                .header { background: #000; color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 32px; letter-spacing: 2px; }
                .header h2 { margin: 10px 0 0 0; font-size: 18px; font-weight: normal; }
                .content { padding: 30px; color: #000; }
                .order-details { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin: 20px 0; }
                .order-details p { margin: 8px 0; }
                .item { display: flex; justify-content: space-between; margin: 15px 0; padding: 15px 0; border-bottom: 1px solid #ddd; }
                .item:last-child { border-bottom: none; }
                .total { background: #000; color: white; padding: 20px; text-align: center; font-size: 20px; font-weight: bold; margin: 20px 0; }
                .footer { background: #000; color: white; padding: 20px; text-align: center; }
                .footer p { margin: 5px 0; }
                h3 { color: #000; border-bottom: 2px solid #000; padding-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>ARES</h1>
                    <h2>Order Confirmation</h2>
                    <p>Thank you for your purchase!</p>
                </div>
                
                <div class='content'>
                    <h3>Order Details</h3>
                    <div class='order-details'>
                        <p><strong>Order Number:</strong> #{$order->order_id}</p>
                        <p><strong>Order Date:</strong> " . $order->created_at->format('F j, Y g:i A') . "</p>
                        <p><strong>Status:</strong> Confirmed</p>
                    </div>

                    <h3>Items Ordered</h3>";

        foreach($order->items as $item) {
            $body .= "
                    <div class='item'>
                        <span>{$item->product->name} (x{$item->quantity})</span>
                        <span>Rs. " . number_format($item->price * $item->quantity, 2) . "</span>
                    </div>";
        }

        $body .= "
                    <div class='total'>
                        Total: Rs. " . number_format($order->total_price, 2) . "
                    </div>

                    <h3>Delivery Information</h3>
                    <div class='order-details'>
                        <p><strong>Estimated Delivery:</strong> " . $order->created_at->addDays(4)->format('F j, Y') . "</p>
                        <p><strong>Payment Method:</strong> Cash on Delivery</p>
                    </div>

                    <p style='margin-top: 30px; padding: 15px; background: #f9f9f9; border-left: 4px solid #000;'>
                        We'll send you another email when your order ships. If you have any questions, please contact us at <a href='mailto:ansypieris@gmail.com' style='color: #000;'>ansypieris@gmail.com</a>
                    </p>
                </div>
                
                <div class='footer'>
                    <p>&copy; 2025 ARES Store. All rights reserved.</p>
                    <p>Empowering fashion, one order at a time.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $body;
    }

    private function createMessage($from, $to, $subject, $body)
    {
        $message = new Message();
        
        $rawMessage = "From: ARES Store <{$from}>\r\n";
        $rawMessage .= "To: {$to}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $rawMessage .= $body;

        $message->setRaw(base64url_encode($rawMessage));
        
        return $message;
    }
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}