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
        try {
            $data = "{}";
            $mysql = $this->mysql;
            $query = $mysql->query("SELECT * FROM cosmetics WHERE uuid=?", array($uuid));

            if ($mysql->rowCount($query) >= 1) {
                $reponse = $mysql->fetch($query);
                $data = $reponse['json'];
            }

            $data = json_decode($data, true);
            if (is_null($request->get("type"))) {
                echo '{"code":400,"message":"No type was set!"}';
                return;
            }
            $data[$request->get("type")] = $request->get("value");
            $data = json_encode($data);

            $mysql->update("INSERT INTO cosmetics (uuid, json) VALUES (?, ?) ON DUPLICATE KEY UPDATE json=?", array($uuid, $data, $data));
            echo '{"code":200,"message":"Ok!"}';
        } catch (Exception $th) {
            echo sprintf('{"code":500,"message":"%s"}', $th->getMessage());
        }
    }

    #[Route("/v0/cosmetics/get/{uuid}")]
    public function v0_get(string $uuid, Request $request)
    {
        $mysql = $this->mysql;
        $query = $mysql->query("SELECT * FROM cosmetics WHERE uuid=?", array($uuid));
        if ($mysql->rowCount($query) >= 1) {
            $reponse = $mysql->fetch($query);
            echo sprintf('{"code":200,"cosmetics":%s}', $reponse['json']);
            return;
        }
        echo sprintf('{"code":400,"message":"No entry found for %s"}', $uuid);
    }

}