<?php

class SessionController
{

    public function __construct(
        private readonly MySQL $sql
    )
    {
    }

    #[Route("/v0/session/create")]
    public function v0_create(Request $request)
    {
        $uuid = $request->get("uuid", "NOT A VALID UUID");
        if (!is_string($uuid) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1) {
            echo '{"code":400,"message":"Not a valid UUID!"}';
            return;
        }

        $query = $this->sql->query("SELECT * FROM sessions WHERE uuid=?", array($uuid));
        if ($this->sql->rowCount($query) >= 1) {
            echo '{"code":400,"message":"You have already a Session!"}';
            return;
        }

        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!§$%&';
        $charLength = strlen($chars);
        $token = '';
        for ($i = 0; $i < 4096; $i++)
            $token .= $chars[rand(0, $charLength - 1)];
        $token = password_hash($token, PASSWORD_DEFAULT);

        $this->sql->update("INSERT INTO sessions (uuid, token) VALUES (?, ?)", array($uuid, password_hash($token, PASSWORD_DEFAULT)));
        echo sprintf('{"code":200,"message":"Ok!","token":"%s"}', $token);
    }

    #[Route("/v0/session/destroy")]
    public function v0_destroy(Request $request)
    {
        $uuid = $request->get("uuid", "NOT A VALID UUID");
        if (!is_string($uuid) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1) {
            echo '{"code":400,"message":"Not a valid UUID!"}';
            return;
        }

        $query = $this->sql->query("SELECT * FROM sessions WHERE uuid=?", array($uuid));
        if ($this->sql->rowCount($query) <= 0) {
            echo '{"code":400,"message":"You don not have a Session!"}';
            return;
        }
        $query = $this->sql->fetch($query);

        $token = $request->get("token", "abcd");
        $token§database = $query['token'];

        if (!password_verify($token, $token§database)) {
            echo '{"code":400,"message":"Please provide a valid token!"}';
            return;
        }

        $this->sql->update("DELETE FROM sessions WHERE uuid=?", array($uuid));
        echo '{"code":200,"message":"Ok!"}';
    }

}