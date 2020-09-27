<?php
declare(strict_types=1);
namespace app\modules\common\services;


use Mobvista\MixTools\Src\Request\RequestApi;

class LinkService
{
    public function verifyShopifyLinks(array $links)
    {
        $data = [];
        foreach ($links as $link)
        {
            $isLink = 1;
            $isShopifyLink = $this->verifyShopifyLink($link);
            if (!$isShopifyLink) {
                $isLink = $this->verifyLink($link);
            }
            $data[] = [
                'link' => $link,
                'is_shopify_link' => $isShopifyLink,
                'is_link' => $isLink
            ];
        }
        return $data;
    }

    public function verifyShopifyLink(string $link)
    {
        $url = $this->getShopifyLink($link);
        $result = RequestApi::get($url, [], []);
        $isShopifyLink = 0;
        if ($result && isset($result['product'])) {
            $isShopifyLink = 1;
        }
        return $isShopifyLink;
    }

    private function getShopifyLink(string $link)
    {
        if (($index = strpos($link, '?')) !== false) {
            $link = substr($link, 0, $index);
        }
        return $link . '.json';
    }

    private function verifyLink(string $link)
    {
        $isLink = 0;
        RequestApi::checkGet($link);
        $code = RequestApi::getResponseCode();

        if (in_array($code, [200, 301, 302])) {
            $isLink = 1;
        }
        return $isLink;
    }
}
