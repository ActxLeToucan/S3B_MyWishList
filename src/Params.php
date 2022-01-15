<?php

namespace wishlist;

class Params {
    private $rq;
    private $rs;
    private array $params;
    public function __construct($rq, $rs, $base, $route_uri) {
        $this->rq = $rq;
        $this->params = array(
            "base" => $base,
            "route_uri" => $route_uri,
            "notif" => isset($rq->getQueryParams()['notif']) ? filter_var($rq->getQueryParams('notif')["notif"], FILTER_SANITIZE_STRING) : null,
            "link" => isset($rq->getQueryParams()['notif']) && isset($rq->getQueryParams()['link']) ? filter_var($rq->getQueryParams('link')["link"], FILTER_SANITIZE_ADD_SLASHES) : null,
            "redirect" => isset($rq->getQueryParams()['redirect']) ? $rq->getQueryParams('redirect')["redirect"] : null
        );
    }

    /**
     * @return array
     */
    public function getParams(): array {
        return $this->params;
    }
    
    public function execute() {
        if (!is_null($this->params["redirect"])) {
            $p = "";
            if (!is_null($this->params["notif"])) {
                $p = (str_contains($this->params["redirect"], "?") ? "&notif=" : "?notif=") . $this->params["notif"];
                $p = $p . (is_null($this->params["link"]) ? "" : "&link={$this->params['link']}");
            }
            return $this->rs->withRedirect($this->params["redirect"].$p);
        }
    }
}