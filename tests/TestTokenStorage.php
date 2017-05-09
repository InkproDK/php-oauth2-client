<?php

/**
 * Copyright (c) 2016, 2017 François Kooman <fkooman@tuxed.net>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace fkooman\OAuth\Client\Tests;

use fkooman\OAuth\Client\AccessToken;
use fkooman\OAuth\Client\TokenStorageInterface;

class TestTokenStorage extends TestSession implements TokenStorageInterface
{
    /**
     * @param string $userId
     *
     * @return array
     */
    public function getAccessToken($userId)
    {
        $accessTokenList = [];
        if ($this->has(sprintf('_oauth2_token_%s', $userId))) {
            foreach ($this->get(sprintf('_oauth2_token_%s', $userId)) as $accessToken) {
                $accessTokenList[] = AccessToken::fromStorage($accessToken);
            }
        }

        return $accessTokenList;
    }

    /**
     * @param string      $userId
     * @param AccessToken $accessToken
     */
    public function addAccessToken($userId, AccessToken $accessToken)
    {
        $accessTokenList = $this->getAccessToken($userId);
        $accessTokenList[] = $accessToken->toStorage();
        $this->set(sprintf('_oauth2_token_%s', $userId), $accessTokenList);
    }

    /**
     * @param string      $userId
     * @param AccessToken $accessToken
     */
    public function deleteAccessToken($userId, AccessToken $accessToken)
    {
        $accessTokenList = $this->getAccessToken($userId);
        foreach ($accessTokenList as $k => $v) {
            if ($accessToken->getProviderId() === $v->getProviderId()) {
                if ($accessToken->getToken() === $v->getToken()) {
                    unset($accessTokenList[$k]);
                }
            }
        }

        $this->set(sprintf('_oauth2_token_%s', $userId), array_values($accessTokenList));
    }
}
