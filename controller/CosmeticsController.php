<?php

class CosmeticsController
{

    /*
    (
    uuid VARCHAR(255),
    json VARCHAR(255)
    )
    */

    public function __construct(
        private readonly MySQL $mysql
    )
    {

    }

    #[Route("/v0/cosmetics/update/{uuid}")]
    public function v0_update(string $uuid, Request $request)
    {
        if (!is_string($uuid) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1) {
            echo '{"code":400,"message":"Not a valid UUID!"}';
            return;
        }
        try {
            $sql = $this->mysql;
            $query = $sql->query("SELECT * FROM sessions WHERE uuid=?", array($uuid));
            if ($sql->rowCount($query) <= 0) {
                echo '{"code":400,"message":"You don not have a Session!"}';
                return;
            }
            $query = $sql->fetch($query);

            $token = $request->get("token", "abcd");
            $token§database = $query['token'];
            if (!password_verify($token, $token§database)) {
                echo '{"code":400,"message":"No Session with this token was found!"}';
                return;
            }

            $renew_at = $query['updated_at'];
            $renew_at = strtotime("+10 day", strtotime($renew_at));
            $renew_at = date("Y-m-d H:i:s", $renew_at);
            $today = date("Y-m-d H:i:s");
            if ($today >= $renew_at) {
                echo '{"code":400,"message":"Your Session expired!"}';
                $sql->update("DELETE FROM sessions WHERE uuid=?", array($uuid));
                return;
            }

            $data = "{}";

            $query = $sql->query("SELECT * FROM cosmetics WHERE uuid=?", array($uuid));

            if ($sql->rowCount($query) >= 1) {
                $reponse = $sql->fetch($query);
                $data = $reponse['json'];
            }

            $data = json_decode($data, true);
            if (is_null($request->get("type"))) {
                echo '{"code":400,"message":"No type was set!"}';
                return;
            }
            $data[$request->get("type")] = $request->get("value");
            $data = json_encode($data);

            $sql->update("INSERT INTO cosmetics (uuid, json) VALUES (?, ?) ON DUPLICATE KEY UPDATE json=?", array($uuid, $data, $data));
            $sql->update("UPDATE sessions SET updated_at=? WHERE uuid=?", array(date("Y-m-d H:i:s"), $uuid));
            echo '{"code":200,"message":"Ok!"}';
        } catch (Exception $th) {
            echo sprintf('{"code":500,"message":"%s"}', $th->getMessage());
        }
    }

    #[Route("/v0/cosmetics/get/{uuid}")]
    public function v0_get(string $uuid, Request $request)
    {
        if (!is_string($uuid) || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1) {
            echo '{"code":400,"message":"Not a valid UUID!"}';
            return;
        }

        $sql = $this->mysql;

        $query = $sql->query("SELECT * FROM cosmetics WHERE uuid=?", array($uuid));
        if ($sql->rowCount($query) >= 1) {
            $reponse = $sql->fetch($query);
            echo sprintf('{"code":200,"cosmetics":%s}', $reponse['json']);
            return;
        }
        echo sprintf('{"code":400,"message":"No entry found for %s"}', $uuid);
    }

}