<?php
/*
 *  Last Modified: 6/29/21, 12:06 AM
 *  Copyright (c) 2021
 *  -created by Ariful Islam
 *  -All Rights Preserved By
 *  -If you have any query then knock me at
 *  arif98741@gmail.com
 *  See my profile @ https://github.com/arif98741
 */

namespace Xenon\LaravelBDSms\Provider;

use Xenon\LaravelBDSms\Facades\Request;
use Xenon\LaravelBDSms\Handler\ParameterException;
use Xenon\LaravelBDSms\Sender;

class DianaSms extends AbstractProvider
{
    /**
     * DianaHost constructor.
     * @param Sender $sender
     */
    public function __construct(Sender $sender)
    {
        $this->senderObject = $sender;
    }

    /**
     * Send Request To Api and Send Message
     */
    public function sendRequest()
    {
        $number = $this->senderObject->getMobile();
        $text = $this->senderObject->getMessage();
        $config = $this->senderObject->getConfig();

        $query = [
            'ApiKey' => $config['ApiKey'],
            'ClientId' => $config['ClientId'],
            'SenderId' => $config['SenderId'],
            'MobileNumbers' => $number,
            'Message' => $text,
        ];

        $response = Request::get('https://q.dianasms.com/api/v2/SendSMS', $query);

        $body = $response->getBody();
        $smsResult = $body->getContents();

        $data['number'] = $number;
        $data['message'] = $text;
        return $this->generateReport($smsResult, $data)->getContent();
    }

    /**
     * @throws ParameterException
     */
    public function errorException()
    {
        if (!array_key_exists('ApiKey', $this->senderObject->getConfig())) {
            throw new ParameterException('ApiKey is absent in configuration');
        }
        if (!array_key_exists('ClientId', $this->senderObject->getConfig())) {
            throw new ParameterException('ClientId key is absent in configuration');
        }
        if (!array_key_exists('SenderId', $this->senderObject->getConfig())) {
            throw new ParameterException('SenderId key is absent in configuration');
        }
    }

}
