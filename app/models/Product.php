<?php

namespace app\models;

use luxury\App;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_SmtpTransport;

class Product extends AppModel 
{
    public $attributes = [
        'product' => '',
    ];
	
	public static function sendMail($body, $first = false)
	{
        // Create the Transport
        $transport = (new Swift_SmtpTransport(App::$app->getProperty('smtp_host'), App::$app->getProperty('smtp_port'), App::$app->getProperty('smtp_protocol')))
            ->setUsername(App::$app->getProperty('smtp_login'))
            ->setPassword(App::$app->getProperty('smtp_password'))
        ;
        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('CARiD suspension systems | ' . date('Y-m-d H:i:s')))
            ->setFrom([App::$app->getProperty('smtp_login') => App::$app->getProperty('site_name')])
            ->setTo(App::$app->getProperty('email'))
            ->setBody($body, 'text/html')
        ;
		
		if($first)
		{
			$message->attach(Swift_Attachment::fromPath(CSV_FILES . '/products.csv'));
		}
		else
		{
			$message->attach(Swift_Attachment::fromPath(CSV_FILES . '/new_products.csv'));
			
			$message->attach(Swift_Attachment::fromPath(CSV_FILES . '/disapperaed_products.csv'));
			
			$message->attach(Swift_Attachment::fromPath(CSV_FILES . '/recently_reviewed_products.csv'));
		}

        // Send the message
        $result = $mailer->send($message);  
    }
	
	public function getLastProducts()
	{
		$oldData = \R::findOne('products', 'ORDER BY id DESC');
		return $oldData;
	}
	

}