<?php

use ChurchCRM\Token;
use ChurchCRM\UserQuery;
use ChurchCRM\Utils\LoggerUtils;
use ChurchCRM\Emails\ResetPasswordTokenEmail;
use Slim\Http\Request;
use Slim\Http\Response;

$app->group('/public/user', function () {
    $this->post('/login', 'userLogin');
    $this->post('/login/', 'userLogin');
    $this->post('/password/reset', 'userPasswordReset');
    $this->post('/password/reset/', 'userPasswordReset');
});

function userLogin(Request $request, Response $response, array $args)
{
    $body = json_decode($request->getBody());
    if (!empty($body->userName)) {
        $user = UserQuery::create()->findOneByUserName($body->userName);
        if (!empty($user)) {
            $password = $body->password;
            if ($user->isPasswordValid($password)) {
                return $response->withJson(["apiKey" => $user->getApiKey()]);
            } else {
                return $response->withStatus(401);
            }
        }
    }
    return $response->withStatus(404);
}

function userPasswordReset(Request $request, Response $response, array $args)
{
    $logger = LoggerUtils::getAppLogger();
    $body = json_decode($request->getBody());
    $userName = strtolower(trim($body->userName));
    if (!empty($userName)) {
        $user = UserQuery::create()->findOneByUserName($userName);
        if (!empty($user) && !empty($user->getEmail())) {
            $token = new Token();
            $token->build("password", $user->getId());
            $token->save();
            $email = new ResetPasswordTokenEmail($user, $token->getToken());
            if (!$email->send()) {
                $logger->error($email->getError());
            }
            return $response->withStatus(200)->withJson(['status' => "success"]);
        } else {
            return $response->withStatus(404)->withJson(["Error" => gettext("User") . " [" . $userName . "] ". gettext("no found or user without an email")]);
        }
    }
    return $response->withStatus(401)->withJson(["Error" => gettext("UserName not set")]);
}
