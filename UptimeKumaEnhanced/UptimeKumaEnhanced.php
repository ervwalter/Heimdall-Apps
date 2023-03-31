<?php namespace App\SupportedApps\UptimeKumaEnhanced;

class UptimeKumaEnhanced extends \App\SupportedApps implements \App\EnhancedApps
{
	public $config;

	public function test()
	{
        $now = time();
        $dashboard = $this->getConfigValue("dashboard", "default");
		$res = parent::execute($this->url("/status-page/heartbeat/${dashboard}?cachebust=${now}"), $this->attrs);
		echo $res->getBody();
	}

	public function livestats()
	{
        $now = time();
        $dashboard = $this->getConfigValue("dashboard", "default");

        $heartbeat_data = $this->apiCall("/status-page/heartbeat/${dashboard}?cachebust=${now}");
        $uptime_list = $heartbeat_data["uptimeList"];

        $checks_total = count($uptime_list);
        $checks_up = array_sum($uptime_list);

        $status = "good";
        if ($checks_total > $checks_up) {
            $status = "bad";
        }

		$data = [];
		$data["checks_total"] = $checks_total;
		$data["checks_up"] = $checks_up;
        $data["status"] = $status;
		return parent::getLiveStats($status, $data);
	}
    
	public function url($endpoint)
	{

		$api_url = parent::normaliseurl($this->config->url) . "/api" . $endpoint;
		return $api_url;
	}

	public function apiCall($endpoint)
	{
		$res = parent::execute($this->url($endpoint), $this->attrs);
		return json_decode($res->getBody())->data;
	}    

}
