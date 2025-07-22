<?php
namespace Omneo\Relay\Utils;
use Psr\Log\LoggerInterface;

class Request {
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function call(string $url, string $method = "POST", $body = null, array $customHeaders = [])
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            $headers = ['Content-Type: application/json'];
            $headers = array_merge($headers, $customHeaders);

            if ($body) {
                $payload = json_encode($body);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $headers[] = 'Content-Length: ' . strlen($payload);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                $this->logger->error('cURL Error: ' . $error_msg);
                curl_close($ch);
                return [
                    'status' => 500,
                    'body' => ['error' => 'cURL Error: ' . $error_msg]
                ];
            }

            curl_close($ch);

            return [
                'status' => $status,
                'body' => json_decode($response, true)
            ];
        } catch (\Exception $e) {
            $this->logger->error('Omneo Relay Request Error: ' . $e->getMessage());
            $this->logger->debug($e);
            return [
                'status' => 500,
                'body' => ['error' => $e->getMessage()]
            ];
        }
    }

    public function post(string $url, $body = null, array $headers = [])
    {
        return $this->call($url, 'POST', $body, $headers);
    }
}
